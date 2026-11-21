<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\ClassAttendance;
use App\Models\GateAttendance;
use App\Models\Permission; // Model permohonan surat izin/sakit
use App\Models\Teacher;
use App\Models\TeachingJournal;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ClassAttendanceController extends Controller
{
    /**
     * Mendapatkan nama kolom tanggal yang valid pada tabel 'permissions'
     */
    private function getPermissionDateColumn()
    {
        if (Schema::hasColumn('permissions', 'permission_date')) {
            return 'permission_date';
        } elseif (Schema::hasColumn('permissions', 'start_date')) {
            return 'start_date';
        }
        return 'created_at';
    }

    public function index(Request $request)
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        
        $selectedClassId = $request->get('class_id');
        $classCode = $request->get('class_code');
        $attendanceDate = $request->get('date', date('Y-m-d'));

        $selectedClass = null;
        if ($selectedClassId) {
            $selectedClass = SchoolClass::find($selectedClassId);
        } elseif ($classCode) {
            $selectedClass = SchoolClass::where('id', $classCode)->orWhere('name', $classCode)->first();
        } else {
            $selectedClass = SchoolClass::first();
        }

        $students = collect();
        $gateLogs = [];
        $approvedPermits = [];
        $journal = null;
        $savedAttendances = [];
        $savedNotes = [];
        $savedPermitIds = [];

        if ($selectedClass && $teacher) {
            // Ambil daftar siswa di kelas terpilih
            $students = Student::where('class_id', $selectedClass->id)
                ->orderBy('name', 'asc')
                ->get();

            $studentIds = $students->pluck('id');

            // 1. Ambil data absensi KHUSUS GURU YANG SEDANG LOGIN (teacher_id)
            $attendancesData = ClassAttendance::with('permit')
                ->where('class_id', $selectedClass->id)
                ->where('teacher_id', $teacher->id)
                ->where('date', $attendanceDate)
                ->get();

            $savedAttendances = $attendancesData->pluck('status', 'student_id')->toArray();
            $savedNotes = $attendancesData->pluck('notes', 'student_id')->toArray();
            $savedPermitIds = $attendancesData->pluck('permit_id', 'student_id')->toArray();

            // 2. Ambil log scan gerbang (Smart Gate)
            if (class_exists('App\Models\GateAttendance')) {
                $gateLogs = GateAttendance::whereIn('student_id', $studentIds)
                    ->whereDate('created_at', $attendanceDate)
                    ->get()
                    ->keyBy('student_id');
            }

            // 3. Deteksi nama kolom tanggal secara dinamis
            $dateColumn = $this->getPermissionDateColumn();

            // 4. Ambil surat izin/sakit yang disetujui (Approved) dari Portal Parent
            $approvedPermits = Permission::whereIn('student_id', $studentIds)
                ->whereDate($dateColumn, $attendanceDate)
                ->where('status', 'Approved')
                ->get()
                ->keyBy('student_id');

            // 5. Ambil data Berita Acara Mengajar khusus guru yang sedang login
            $journal = TeachingJournal::where('teacher_id', $teacher->id)
                ->where('class_id', $selectedClass->id)
                ->where('date', $attendanceDate)
                ->first();
        }

        // Ambil daftar kelas untuk dropdown filter
        $classes = SchoolClass::pluck('name', 'id')->toArray();

        return view('teacher.attendance.index', [
            'teacher'             => $teacher,
            'selectedClass'       => $selectedClass ? $selectedClass->id : '',
            'selectedClassModel'  => $selectedClass,
            'students'            => $students,
            'classes'             => $classes,
            'attendanceDate'      => $attendanceDate,
            'savedAttendances'    => $savedAttendances,
            'savedNotes'          => $savedNotes,
            'savedPermitIds'      => $savedPermitIds,
            'existingAttendances' => $savedAttendances,
            'gateLogs'            => $gateLogs,
            'approvedPermits'     => $approvedPermits,
            'journal'             => $journal,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required',
            'date'            => 'required|date',
            'attendances'     => 'required|array',
            'subject'         => 'nullable|string|max:255',
            'topic'           => 'nullable|string',
            'late_reason'     => 'nullable|string',
            'photo_evidence'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $teacher = Teacher::where('user_id', auth()->id())->first();
        $classId = $request->school_class_id;
        $date    = $request->date;

        // Pengecekan nama kolom kelas pada tabel class_attendances secara aman & dinamis
        $classColumn = Schema::hasColumn('class_attendances', 'class_id') ? 'class_id' : 'school_class_id';
        
        $formattedDate = Carbon::parse($date)->translatedFormat('l, d F Y');
        $currentTime   = Carbon::now('Asia/Jakarta')->format('H:i');
        $teacherName   = $teacher ? $teacher->name : 'Guru Mata Pelajaran';

        // 1. Simpan Absensi Siswa & Kirim Notifikasi WhatsApp Dinamis
        foreach ($request->attendances as $studentId => $status) {
            $note = $request->notes[$studentId] ?? null;

            // Simpan atau perbarui records absensi kelas
            ClassAttendance::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    $classColumn => $classId,
                    'student_id' => $studentId,
                    'date'       => $date,
                ],
                [
                    'status' => $status,
                    'notes'  => $status === 'TDK' ? $note : null,
                ]
            );

            // =========================================================
            // INTEGRASI WHATSAPP NOTIFIKASI KELAS (SAFE-MODE)
            // =========================================================
            $student = Student::find($studentId);
            if ($student) {
                // Pencarian nomor HP Orang Tua secara aman
                $parentPhone = null;

                if (method_exists($student, 'parent') && $student->parent) {
                    $parentPhone = $student->parent->phone 
                        ?? $student->parent->whatsapp 
                        ?? $student->parent->no_hp 
                        ?? null;
                } elseif (method_exists($student, 'guardian') && $student->guardian) {
                    $parentPhone = $student->guardian->phone 
                        ?? $student->guardian->whatsapp 
                        ?? $student->guardian->no_hp 
                        ?? null;
                }

                if (!$parentPhone) {
                    $parentPhone = $student->parent_phone 
                        ?? $student->phone 
                        ?? $student->no_hp 
                        ?? null;
                }

                // Kirim Notifikasi WA jika Nomor HP Orang Tua ditemukan
                if ($parentPhone) {
                    $statusClean = strtolower(trim($status));
                    $message = "";

                    if (in_array($statusClean, ['hadir', 'h', 'present'])) {
                        // 1. Pesan Siswa Hadir
                        $message  = "🔔 *[PORTAL SEKOLAH PINTAR SMA XYZ JAKARTA]*\n\n";
                        $message .= "Yth. Bapak/Ibu Wali Murid dari *{$student->name}*,\n\n";
                        $message .= "Diberitahukan bahwa putra/putri Anda dikonfirmasi *HADIR* di dalam kelas pada jam pelajaran oleh Guru Kelas ({$teacherName}).\n\n";
                        $message .= "📅 *Tanggal:* {$formattedDate}\n";
                        $message .= "⏰ *Waktu Absen:* {$currentTime} WIB\n\n";
                        $message .= "Terima kasih atas perhatian Anda.\n";
                        $message .= "_SMA XYZ JAKARTA - Smart School System_";

                    } elseif (in_array($statusClean, ['sakit', 's', 'sick'])) {
                        // 2. Pesan Siswa Sakit
                        $message  = "🔔 *[PORTAL SEKOLAH PINTAR SMA XYZ JAKARTA]*\n\n";
                        $message .= "Yth. Bapak/Ibu Wali Murid dari *{$student->name}*,\n\n";
                        $message .= "Informasi Kehadiran Kelas: Status putra/putri Anda hari ini dicatat *SAKIT* oleh Guru Kelas ({$teacherName}).\n\n";
                        $message .= "📅 *Tanggal:* {$formattedDate}\n\n";
                        $message .= "Semoga putra/putri Anda lekas sembuh.\n";
                        $message .= "_SMA XYZ JAKARTA - Smart School System_";

                    } elseif (in_array($statusClean, ['izin', 'i', 'permit'])) {
                        // 3. Pesan Siswa Izin
                        $message  = "🔔 *[PORTAL SEKOLAH PINTAR SMA XYZ JAKARTA]*\n\n";
                        $message .= "Yth. Bapak/Ibu Wali Murid dari *{$student->name}*,\n\n";
                        $message .= "Informasi Kehadiran Kelas: Status putra/putri Anda hari ini dicatat *IZIN* oleh Guru Kelas ({$teacherName}).\n\n";
                        $message .= "📅 *Tanggal:* {$formattedDate}\n\n";
                        $message .= "Terima kasih.\n";
                        $message .= "_SMA XYZ JAKARTA - Smart School System_";

                    } elseif (in_array($statusClean, ['alpha', 'alpa', 'a'])) {
                        // 4. Pesan Siswa Alpha (Tanpa Keterangan)
                        $message  = "⚠️ *[PERINGATAN KEHADIRAN - SMA XYZ JAKARTA]*\n\n";
                        $message .= "Yth. Bapak/Ibu Wali Murid dari *{$student->name}*,\n\n";
                        $message .= "Diberitahukan bahwa putra/putri Anda *TIDAK HADIR TANPA KETERANGAN (ALPHA)* di dalam kelas saat jam pelajaran berlangsung oleh Guru ({$teacherName}).\n\n";
                        $message .= "📅 *Tanggal:* {$formattedDate}\n\n";
                        $message .= "📌 *Mohon segera hubungi pihak sekolah / Wali Kelas untuk mengonfirmasi keberadaan putra/putri Anda.*\n";
                        $message .= "_SMA XYZ JAKARTA - Smart School System_";

                    } elseif (in_array($statusClean, ['tdk', 'tidak_di_kelas'])) {
                        // 5. Pesan Khusus TDK (Siswa Tap Gerbang tetapi Tidak Ada di Kelas)
                        $noteText = $note ? "\n📝 *Catatan Guru:* {$note}" : "";

                        $message  = "🚨 *[PERINGATAN PENTING - SMA XYZ JAKARTA]*\n\n";
                        $message .= "Yth. Bapak/Ibu Wali Murid dari *{$student->name}*,\n\n";
                        $message .= "PERHATIAN: Putra/putri Anda terdata sudah *Tap Masuk Gerbang Sekolah*, tetapi *TIDAK BERADA DI KELAS (TDK)* saat jam pelajaran berlangsung oleh Guru ({$teacherName}).{$noteText}\n\n";
                        $message .= "📅 *Tanggal:* {$formattedDate}\n";
                        $message .= "⏰ *Waktu Pengecekan:* {$currentTime} WIB\n\n";
                        $message .= "📌 *Mohon segera hubungi putra/putri Anda atau Wali Kelas demi keamanan siswa.*\n";
                        $message .= "_SMA XYZ JAKARTA - Smart School System_";
                    }

                    // Kirim pesan WA melalui WhatsAppService jika template pesan terbentuk
                    if (!empty($message)) {
                        WhatsAppService::sendMessage($parentPhone, $message);
                    }
                }
            }
        }

        // 2. Simpan Berita Acara & Alasan Terlambat Buka Kelas
        $journal = TeachingJournal::firstOrNew([
            'teacher_id' => $teacher->id,
            'class_id'   => $classId,
            'date'       => $date,
        ]);

        $journal->subject            = $request->subject ?? $request->subject_name;
        $journal->topic              = $request->topic;
        $journal->activities_summary = $request->activities_summary ?? $request->notes_journal;

        if ($request->filled('late_reason') && Schema::hasColumn('teaching_journals', 'late_reason')) {
            $journal->late_reason = $request->late_reason;
        }

        // Pengecekan aman agar tidak error jika kolom opened_at belum ada di DB
        if ($request->filled('opened_at') && Schema::hasColumn('teaching_journals', 'opened_at')) {
            $journal->opened_at = $request->opened_at;
        }

        if ($request->hasFile('photo_evidence')) {
            if ($journal->photo_evidence && Storage::disk('public')->exists($journal->photo_evidence)) {
                Storage::disk('public')->delete($journal->photo_evidence);
            }
            $path = $request->file('photo_evidence')->store('teaching_evidence', 'public');
            $journal->photo_evidence = $path;
        }

        $journal->save();

        return redirect()->back()->with('success', 'Presensi dan Notifikasi WA berhasil dikirim ke Orang Tua!');
    }

    public function recap(Request $request)
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        $selectedClassId = $request->get('class_id');
        $classCode = $request->get('class_code');
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));
        $selectedDate = $request->get('date', date('Y-m-d'));

        // Memastikan nilai viewType terbaca dari request 'view'
        $viewType = $request->input('view', 'monthly');

        $selectedClass = null;
        if ($selectedClassId) {
            $selectedClass = SchoolClass::find($selectedClassId);
        } elseif ($classCode) {
            $selectedClass = SchoolClass::where('id', $classCode)->orWhere('name', $classCode)->first();
        } else {
            $selectedClass = SchoolClass::first();
        }

        $students = collect();
        $recapData = [];
        $dailyAttendances = collect();
        $teachingJournal = null;

        if ($selectedClass && $teacher) {
            $students = Student::where('class_id', $selectedClass->id)
                ->orderBy('name', 'asc')
                ->get();

            $hasTeacherIdColumn = Schema::hasColumn('class_attendances', 'teacher_id');

            // 1. Ambil data rekap bulanan KHUSUS GURU TERSEBUT
            foreach ($students as $student) {
                $query = ClassAttendance::where('student_id', $student->id)
                    ->where('class_id', $selectedClass->id)
                    ->whereMonth('date', $selectedMonth)
                    ->whereYear('date', $selectedYear);

                if ($hasTeacherIdColumn) {
                    $query->where('teacher_id', $teacher->id);
                }

                $attendances = $query->get();

                $recapData[$student->id] = [
                    'hadir' => $attendances->whereIn('status', ['Hadir', 'H', 'present'])->count(),
                    'sakit' => $attendances->whereIn('status', ['Sakit', 'S', 'sick'])->count(),
                    'izin'  => $attendances->whereIn('status', ['Izin', 'I', 'permit'])->count(),
                    'alpha' => $attendances->whereIn('status', ['Alpa', 'Alpha', 'A', 'alpha'])->count(),
                    'tdk'   => $attendances->where('status', 'TDK')->count(),
                    'total' => $attendances->count(),
                ];
            }

            // 2. Ambil data log harian KHUSUS GURU TERSEBUT
            $dailyQuery = ClassAttendance::with(['student', 'permit'])
                ->where('class_id', $selectedClass->id)
                ->where('date', $selectedDate);

            if ($hasTeacherIdColumn) {
                $dailyQuery->where('teacher_id', $teacher->id);
            }

            $dailyAttendances = $dailyQuery->get()->keyBy('student_id');

            // 3. Berita Acara Mengajar KBM khusus guru tersebut
            $teachingJournal = TeachingJournal::where('teacher_id', $teacher->id)
                ->where('class_id', $selectedClass->id)
                ->where('date', $selectedDate)
                ->first();
        }

        $classes = SchoolClass::pluck('name', 'id')->toArray();

        return view('teacher.attendance.recap', [
            'teacher'            => $teacher,
            'selectedClass'      => $selectedClass ? $selectedClass->id : '',
            'selectedClassModel' => $selectedClass,
            'students'           => $students,
            'classes'            => $classes,
            'selectedMonth'      => $selectedMonth,
            'selectedYear'       => $selectedYear,
            'selectedDate'       => $selectedDate,
            'viewType'           => $viewType,
            'recapData'          => $recapData,
            'dailyAttendances'   => $dailyAttendances,
            'teachingJournal'    => $teachingJournal,
        ]);
    }
}