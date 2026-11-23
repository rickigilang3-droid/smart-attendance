<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ClassAttendance;
use App\Models\GateAttendance;
use App\Models\LessonSchedule;
use App\Models\Permission;
use App\Models\Student;
use App\Models\TeachingJournal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today()->format('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Ekstrak nomor HP dan nama bersih
        $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone ?? $user->username ?? '');
        $cleanName = trim(preg_replace('/^(Orang\s+Tua\s*\/\s*Wali|Orang\s+Tua|Wali|Bpk|Ibu|Bapak)\s+/i', '', $user->name));
        $cleanName = trim(str_ireplace(['Orang Tua / Wali', 'Orang Tua', 'Wali', 'Bpk', 'Ibu', 'Bapak'], '', $cleanName));

        // =========================================================================
        // 🎯 LOGIKA PENCOCOKAN SISWA TERHUBUNG PRESISI (HIERARKI PRIORITAS)
        // =========================================================================
        $student = $this->getStudentByParentUser($user);

        // Inisialisasi Data Presensi & Jadwal
        $gateLog = null;
        $todaySchedules = collect();
        $journal = null;
        $permissions = collect();
        $monthlyRecap = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpha' => 0,
            'tdk' => 0,
        ];

        if ($student) {
            $classId = $student->class_id ?? $student->school_class_id ?? null;

            // Data Smart Gate
            $gateLog = GateAttendance::where('student_id', $student->id)
                ->whereDate('created_at', $today)
                ->first();

            // Data Jadwal Pelajaran
            $dayName = Carbon::now()->locale('id')->isoFormat('dddd');

            if ($classId) {
                $querySchedule = LessonSchedule::with(['teacher', 'schoolClass']);

                if (Schema::hasColumn('lesson_schedules', 'class_id')) {
                    $querySchedule->where('class_id', $classId);
                } elseif (Schema::hasColumn('lesson_schedules', 'school_class_id')) {
                    $querySchedule->where('school_class_id', $classId);
                }

                if (Schema::hasColumn('lesson_schedules', 'day')) {
                    $querySchedule->where('day', $dayName);
                }

                $schedules = $querySchedule->orderBy('start_time', 'asc')->get();

                $todaySchedules = $schedules->map(function ($sched) use ($student, $today, $classId) {
                    $attendanceQuery = ClassAttendance::where('student_id', $student->id)
                        ->whereDate('date', $today);

                    if ($sched->teacher_id) {
                        $attendanceQuery->where('teacher_id', $sched->teacher_id);
                    } else {
                        $attendanceQuery->where('class_id', $classId);
                    }

                    $attendance = $attendanceQuery->first();

                    $sched->attendance_status = $attendance->status ?? 'Belum Diabsen';
                    $sched->attendance_notes = $attendance->notes ?? null;

                    if (! $sched->teacher) {
                        $sched->teacher_name = $sched->teacher_name ?? $sched->guru_name ?? '-';
                    } else {
                        $sched->teacher_name = $sched->teacher->name ?? $sched->teacher->user->name ?? '-';
                    }

                    if ($sched->start_time) {
                        $sched->formatted_start = Carbon::parse($sched->start_time)->format('H:i');
                    }
                    if ($sched->end_time) {
                        $sched->formatted_end = Carbon::parse($sched->end_time)->format('H:i');
                    }

                    return $sched;
                });

                // Jurnal Mengajar
                $journal = TeachingJournal::where('class_id', $classId)
                    ->whereDate('date', $today)
                    ->latest()
                    ->first();
            }

            // Surat Izin & Sakit
            $permissions = Permission::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Rekap Bulanan
            $monthlyAttendances = ClassAttendance::where('student_id', $student->id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->get();

            $monthlyRecap['hadir'] = $monthlyAttendances->whereIn('status', ['Hadir', 'HADIR', 'H'])->count();
            $monthlyRecap['sakit'] = $monthlyAttendances->whereIn('status', ['Sakit', 'SAKIT', 'S'])->count();
            $monthlyRecap['izin'] = $monthlyAttendances->whereIn('status', ['Izin', 'IZIN', 'I'])->count();
            $monthlyRecap['alpha'] = $monthlyAttendances->whereIn('status', ['Alpha', 'ALPHA', 'A'])->count();
            $monthlyRecap['tdk'] = $monthlyAttendances->whereIn('status', ['TDK', 'tdk'])->count();
        }

        // Pengumuman Ringkas Dashboard
        $announcementsQuery = Announcement::query();

        if (Schema::hasColumn('announcements', 'target')) {
            $announcementsQuery->where(function ($q) {
                $q->whereIn('target', ['all', 'parent', 'All', 'Parent', 'Semua', 'Orang Tua', 'ortu', 'Ortu'])
                    ->orWhereNull('target');
            });
        }

        if (Schema::hasColumn('announcements', 'is_active')) {
            $announcementsQuery->where('is_active', true);
        }

        $announcements = $announcementsQuery->latest()->take(5)->get();

        return view('parent.dashboard', compact(
            'student',
            'today',
            'gateLog',
            'todaySchedules',
            'journal',
            'permissions',
            'monthlyRecap',
            'announcements'
        ));
    }

    // =========================================================================
    // 🚀 METHOD HALAMAN SAMPING (SIDEBAR ROUTES)
    // =========================================================================

    public function gateLogs()
    {
        $user = auth()->user();
        $student = $this->getStudentByParentUser($user);

        $gateLogs = collect();
        if ($student) {
            $gateLogs = GateAttendance::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('parent.gate_logs.index', compact('student', 'gateLogs'));
    }

    public function permissions()
    {
        $user = auth()->user();
        $student = $this->getStudentByParentUser($user);

        $permissions = collect();
        if ($student) {
            $permissions = Permission::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('parent.permissions.index', compact('student', 'permissions'));
    }

    public function reports()
    {
        $user = auth()->user();
        $student = $this->getStudentByParentUser($user);

        $attendances = collect();
        if ($student) {
            $attendances = ClassAttendance::where('student_id', $student->id)
                ->orderBy('date', 'desc')
                ->paginate(20);
        }

        return view('parent.reports.index', compact('student', 'attendances'));
    }

    public function announcements()
    {
        $query = Announcement::query();

        if (Schema::hasColumn('announcements', 'target')) {
            $query->where(function ($q) {
                $q->whereIn('target', ['all', 'parent', 'All', 'Parent', 'Semua', 'Orang Tua', 'ortu', 'Ortu'])
                    ->orWhereNull('target');
            });
        }

        if (Schema::hasColumn('announcements', 'is_active')) {
            $query->where('is_active', true);
        }

        $announcements = $query->latest()->paginate(10);

        return view('parent.announcements.index', compact('announcements'));
    }

    public function storePermission(Request $request)
    {
        $student = $this->getStudentByParentUser($request->user());
        abort_unless($student, 403);
        abort_unless((int) $request->input('student_id') === $student->id, 403);

        $validated = $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'type' => 'required|in:Sakit,Izin',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'letter_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('letter_file')) {
            $filePath = $request->file('letter_file')->store('permission_letters', 'public');
        }

        Permission::create([
            'student_id' => $validated['student_id'],
            'parent_user_id' => auth()->id(),
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'letter_file' => $filePath,
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Surat '.$validated['type'].' berhasil diunggah dan menunggu konfirmasi Guru.');
    }

    // Helper untuk mengambil data siswa terhubung
    private function getStudentByParentUser($user)
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone ?? $user->phone_number ?? $user->no_hp ?? $user->username ?? '');
        $cleanName = trim(preg_replace('/^(Orang\s+Tua\s*\/\s*Wali|Orang\s+Tua|Wali|Bpk|Ibu|Bapak)\s+/i', '', $user->name));
        $cleanName = trim(str_ireplace(['Orang Tua / Wali', 'Orang Tua', 'Wali', 'Bpk', 'Ibu', 'Bapak', '(Orang Tua)'], '', $cleanName));

        $student = null;

        // PRIORITAS 0: Cek tabel relasi `parents` (pivot / direct relationship)
        if (Schema::hasTable('parents')) {
            $parentIdRecord = \Illuminate\Support\Facades\DB::table('parents')
                ->where('user_id', $user->id)
                ->first();
            if ($parentIdRecord && !empty($parentIdRecord->student_id)) {
                $student = Student::with('schoolClass')->find($parentIdRecord->student_id);
            }
        }

        // PRIORITAS 1: Cek Foreign Key Direct Relation pada students
        if (Schema::hasColumn('students', 'parent_user_id')) {
            $student = Student::with('schoolClass')
                ->where('parent_user_id', $user->id)
                ->latest()
                ->first();
        } elseif (Schema::hasColumn('students', 'user_id')) {
            $student = Student::with('schoolClass')
                ->where('user_id', $user->id)
                ->latest()
                ->first();
        }

        // PRIORITAS 2: Cek Nomor HP
        if (! $student && ! empty($cleanPhone)) {
            $student = Student::with('schoolClass')
                ->where(function ($q) use ($cleanPhone) {
                    if (Schema::hasColumn('students', 'parent_phone')) {
                        $q->orWhere('parent_phone', $cleanPhone);
                    }
                    if (Schema::hasColumn('students', 'guardian_phone')) {
                        $q->orWhere('guardian_phone', $cleanPhone);
                    }
                })
                ->latest()
                ->first();
        }

        // PRIORITAS 3: Match Nama
        if (! $student && ! empty($cleanName) && strlen($cleanName) >= 3) {
            $student = Student::with('schoolClass')
                ->where(function ($q) use ($cleanName) {
                    $q->where('name', $cleanName);
                    if (Schema::hasColumn('students', 'father_name')) {
                        $q->orWhere('father_name', $cleanName);
                    }
                    if (Schema::hasColumn('students', 'mother_name')) {
                        $q->orWhere('mother_name', $cleanName);
                    }
                    if (Schema::hasColumn('students', 'guardian_name')) {
                        $q->orWhere('guardian_name', $cleanName);
                    }
                })
                ->latest()
                ->first();
        }

        return $student;
    }
}
