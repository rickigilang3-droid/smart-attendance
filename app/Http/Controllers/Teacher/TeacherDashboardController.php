<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\LessonSchedule;
use App\Models\ClassAttendance;
use App\Models\Announcement;
use App\Models\Permission;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Pengumuman Sekolah Aktif Khusus Guru / All
        $announcements = Announcement::where('is_active', true)
            ->whereIn('target', ['all', 'teacher'])
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // 2. Ambil data guru dari user yang sedang login
        $teacher = Teacher::where('user_id', auth()->id())->first();
        if (!$teacher) {
            return view('teacher.dashboard', [
                'teacher'            => null,
                'todaySchedules'     => collect(),
                'weeklySchedules'    => collect(),
                'announcements'      => $announcements,
                'pendingPermissions' => collect(),
                'onTimeStreak'       => 0,
                'performanceScore'   => 100,
                'badgeTitle'         => 'Guru Teladan Disiplin',
                'todayName'          => Carbon::now()->isoFormat('D MMMM Y')
            ]);
        }

        // 3. Pemetaan Hari Bahasa Indonesia
        $daysMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];
        $currentDayIndo = $daysMap[date('l')] ?? 'Senin';
        $todayDate      = date('Y-m-d');
        $tomorrowDate   = Carbon::tomorrow()->format('Y-m-d');

        // 4. Ambil Jadwal Mengajar Hari Ini
        $todaySchedules = LessonSchedule::with('schoolClass')
            ->where('teacher_id', $teacher->id)
            ->where('day', $currentDayIndo)
            ->orderBy('start_time', 'asc')
            ->get();

        $hasTeacherId = Schema::hasColumn('class_attendances', 'teacher_id');
        foreach ($todaySchedules as $schedule) {
            $classId = $schedule->class_id ?? $schedule->school_class_id ?? null;
            
            $query = ClassAttendance::where('class_id', $classId)
                ->where('date', $todayDate);
            if ($hasTeacherId) {
                $query->where('teacher_id', $teacher->id);
            }
            $schedule->is_completed = $query->exists();
        }

        // 5. Ambil Semua Jadwal Mengajar Seminggu
        $weeklySchedules = LessonSchedule::with('schoolClass')
            ->where('teacher_id', $teacher->id)
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('start_time', 'asc')
            ->get();

        // 6. ISOLASI SURAT IZIN/SAKIT: Hanya Tampilkan ke Guru yang Mengajar Siswa Tersebut pada Hari Izin
        $hasStartDate  = Schema::hasColumn('permissions', 'start_date');
        $hasEndDate    = Schema::hasColumn('permissions', 'end_date');
        $hasPermitDate = Schema::hasColumn('permissions', 'permission_date');

        // Ambil kelas tempat guru bertugas (Wali Kelas)
        $homeroomClassId = $teacher->class_id ?? $teacher->school_class_id ?? null;

        $candidatePermissions = Permission::where(function ($query) use ($todayDate, $tomorrowDate, $hasStartDate, $hasEndDate, $hasPermitDate) {
            if ($hasStartDate && $hasEndDate) {
                $query->whereDate('start_date', '<=', $tomorrowDate)
                      ->whereDate('end_date', '>=', $todayDate);
            } elseif ($hasPermitDate) {
                $query->whereDate('permission_date', '>=', $todayDate)
                      ->whereDate('permission_date', '<=', $tomorrowDate);
            } else {
                $query->whereDate('created_at', '>=', $todayDate);
            }
        })
        ->with(['student', 'student.schoolClass'])
        ->latest()
        ->get();

        $validPermissionIds = [];
        foreach ($candidatePermissions as $permit) {
            if (!$permit->student) continue;

            $studentClassId = $permit->student->class_id ?? $permit->student->school_class_id ?? null;
            if (!$studentClassId) continue;

            // A. Jika Guru adalah Wali Kelas siswa tersebut -> Selalu berhak melihat
            if ($homeroomClassId && $homeroomClassId == $studentClassId) {
                $validPermissionIds[] = $permit->id;
                continue;
            }

            // B. Tentukan tanggal mulai dan selesai izin
            $sDateStr = $permit->start_date ?? $permit->permission_date ?? $permit->created_at;
            $eDateStr = $permit->end_date ?? $sDateStr;

            $sDate = Carbon::parse($sDateStr);
            $eDate = Carbon::parse($eDateStr);

            // Cari semua hari (Indo) dalam rentang izin (misal: ['Jumat', 'Sabtu', 'Minggu', 'Senin'])
            $permitDays = [];
            for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                $permitDays[] = $daysMap[$d->format('l')] ?? 'Senin';
            }
            $permitDays = array_unique($permitDays);

            // C. Cek apakah Guru ini mengajar di kelas siswa pada HARI-HARI izin tersebut
            $isTeachingOnPermitDays = LessonSchedule::where(function ($sQ) use ($studentClassId) {
                    if (Schema::hasColumn('lesson_schedules', 'class_id')) {
                        $sQ->where('class_id', $studentClassId);
                    }
                    if (Schema::hasColumn('lesson_schedules', 'school_class_id')) {
                        $sQ->orWhere('school_class_id', $studentClassId);
                    }
                })
                ->where('teacher_id', $teacher->id)
                ->whereIn('day', $permitDays)
                ->exists();

            if ($isTeachingOnPermitDays) {
                $validPermissionIds[] = $permit->id;
            }
        }

        $pendingPermissions = Permission::whereIn('id', $validPermissionIds)
            ->with(['student', 'student.schoolClass'])
            ->latest()
            ->get();

        // 7. Pengecekan status persetujuan terikat spesifik ke teacher_id
        foreach ($pendingPermissions as $permit) {
            $startDate = $permit->start_date ?? $permit->permission_date ?? $todayDate;
            $endDate   = $permit->end_date ?? $startDate;
            $isApprovedByThisTeacher = ClassAttendance::where('student_id', $permit->student_id)
                ->where('teacher_id', $teacher->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('status', [$permit->type ?? $permit->permission_type ?? 'Izin', 'Sakit', 'Izin', 'S', 'I'])
                ->exists();
            $permit->teacher_status = $isApprovedByThisTeacher ? 'Approved' : 'Pending';
        }

        // 8. LOGIKA GAMIFIKASI & STREAKS KEDISIPLINAN GURU
        $onTimeStreak = ClassAttendance::where('teacher_id', $teacher->id)
            ->distinct('date')
            ->count('date');
        $totalSessions = ClassAttendance::where('teacher_id', $teacher->id)->count();
        $performanceScore = 100;
        if ($totalSessions > 0) {
            $performanceScore = 98; // Skor bawaan tinggi untuk apresiasi kedisiplinan
        }
        $badgeTitle = 'Guru Teladan Disiplin';
        if ($performanceScore < 85) {
            $badgeTitle = 'Guru Cukup Disiplin';
        }

        return view('teacher.dashboard', compact(
            'teacher', 
            'todaySchedules', 
            'weeklySchedules', 
            'announcements', 
            'pendingPermissions',
            'onTimeStreak',
            'performanceScore',
            'badgeTitle'
        ));
    }

    /**
     * 📩 HALAMAN KHUSUS PERIZINAN & SURAT SAKIT SISWA (SIDEBAR)
     */
    public function permissionsIndex()
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        if (!$teacher) {
            return redirect()->back()->with('error', 'Profil guru tidak ditemukan.');
        }

        $daysMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];

        $today    = Carbon::today()->format('Y-m-d');
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $homeroomClassId = $teacher->class_id ?? $teacher->school_class_id ?? null;

        $candidatePermissions = Permission::where(function ($query) use ($today, $tomorrow) {
            if (Schema::hasColumn('permissions', 'start_date') && Schema::hasColumn('permissions', 'end_date')) {
                $query->whereDate('start_date', '<=', $tomorrow)
                      ->whereDate('end_date', '>=', $today);
            } elseif (Schema::hasColumn('permissions', 'permission_date')) {
                $query->whereDate('permission_date', '>=', $today)
                      ->whereDate('permission_date', '<=', $tomorrow);
            } else {
                $query->whereDate('created_at', '>=', $today);
            }
        })
        ->with(['student', 'student.schoolClass'])
        ->latest()
        ->get();

        $validPermissionIds = [];
        foreach ($candidatePermissions as $permit) {
            if (!$permit->student) continue;

            $studentClassId = $permit->student->class_id ?? $permit->student->school_class_id ?? null;
            if (!$studentClassId) continue;

            // A. Wali Kelas berhak melihat
            if ($homeroomClassId && $homeroomClassId == $studentClassId) {
                $validPermissionIds[] = $permit->id;
                continue;
            }

            // B. Cek hari mengajar guru dalam rentang izin
            $sDateStr = $permit->start_date ?? $permit->permission_date ?? $permit->created_at;
            $eDateStr = $permit->end_date ?? $sDateStr;

            $sDate = Carbon::parse($sDateStr);
            $eDate = Carbon::parse($eDateStr);

            $permitDays = [];
            for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                $permitDays[] = $daysMap[$d->format('l')] ?? 'Senin';
            }
            $permitDays = array_unique($permitDays);

            $isTeachingOnPermitDays = LessonSchedule::where(function ($sQ) use ($studentClassId) {
                    if (Schema::hasColumn('lesson_schedules', 'class_id')) {
                        $sQ->where('class_id', $studentClassId);
                    }
                    if (Schema::hasColumn('lesson_schedules', 'school_class_id')) {
                        $sQ->orWhere('school_class_id', $studentClassId);
                    }
                })
                ->where('teacher_id', $teacher->id)
                ->whereIn('day', $permitDays)
                ->exists();

            if ($isTeachingOnPermitDays) {
                $validPermissionIds[] = $permit->id;
            }
        }

        $permissions = Permission::whereIn('id', $validPermissionIds)
            ->with(['student', 'student.schoolClass'])
            ->latest()
            ->paginate(10);

        // Status per guru
        $hasPermitId = Schema::hasColumn('class_attendances', 'permit_id');
        foreach ($permissions as $permit) {
            $startDate = $permit->start_date ?? $permit->permission_date ?? $permit->created_at;
            $endDate   = $permit->end_date ?? $startDate;
            $startDateFormatted = Carbon::parse($startDate)->format('Y-m-d');
            $endDateFormatted   = Carbon::parse($endDate)->format('Y-m-d');

            $isApprovedByTeacher = ClassAttendance::where('student_id', $permit->student_id)
                ->where('teacher_id', $teacher->id)
                ->where(function ($q) use ($permit, $hasPermitId, $startDateFormatted, $endDateFormatted) {
                    if ($hasPermitId) {
                        $q->where('permit_id', $permit->id)
                          ->orWhereBetween('date', [$startDateFormatted, $endDateFormatted]);
                    } else {
                        $q->whereBetween('date', [$startDateFormatted, $endDateFormatted]);
                    }
                })
                ->exists();

            $permit->teacher_status = $isApprovedByTeacher ? 'Approved' : 'Pending';
        }

        return view('teacher.permissions.index', compact('permissions', 'teacher'));
    }

    /**
     * 🚀 MODE BUKA KELAS (TEACHER CLASSROOM SESSION START)
     */
    public function openClass(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:lesson_schedules,id'
        ]);

        $schedule = LessonSchedule::with('schoolClass')->findOrFail($request->input('schedule_id'));
        $now = Carbon::now();

        // Toleransi keterlambatan masuk kelas (10 menit setelah jam mulai)
        $scheduledStart = Carbon::parse($schedule->start_time);
        $isLateClass = $now->gt($scheduledStart->copy()->addMinutes(10));
        $statusMsg = $isLateClass ? 'Terlambat Buka Kelas' : 'Tepat Waktu';

        // Deteksi Route Presensi yang Aktif di System
        $targetRoute = \Route::has('teacher.class-attendance.index') 
            ? 'teacher.class-attendance.index' 
            : (\Route::has('teacher.attendance.index') ? 'teacher.attendance.index' : 'teacher.dashboard');

        $classCode = $schedule->schoolClass->code ?? $schedule->schoolClass->name ?? $schedule->class_id ?? $schedule->school_class_id ?? null;

        $redirect = redirect()->route($targetRoute, [
            'class_code'   => $classCode,
            'class_id'     => $schedule->class_id ?? $schedule->school_class_id ?? null,
            'schedule_id'  => $schedule->id,
            'opened_at'    => $now->format('H:i'),
            'class_status' => $statusMsg
        ]);

        if ($isLateClass) {
            $diffMinutes = $now->diffInMinutes($scheduledStart);
            return $redirect->with('warning', '⚠️ PERHATIAN: Anda TERLAMBAT membuka kelas ' . ($schedule->subject ?? 'Mata Pelajaran') . ' (' . ($schedule->schoolClass->name ?? 'Kelas') . ') selama ' . $diffMinutes . ' menit! Sesi dibuka pada ' . $now->format('H:i') . ' WIB.');
        }

        return $redirect->with('success', '✅ Sesi Kelas ' . ($schedule->subject ?? 'Mata Pelajaran') . ' (' . ($schedule->schoolClass->name ?? 'Kelas') . ') Berhasil Dibuka tepat waktu pada ' . $now->format('H:i') . ' WIB!');
    }

    /**
     * Verifikasi (Setujui / Tolak) Surat Izin/Sakit Khusus Guru yang Login
     */
    public function verifyPermission(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $teacher = Teacher::where('user_id', auth()->id())->first();
        if (!$teacher) {
            return redirect()->back()->with('error', 'Profil guru tidak ditemukan.');
        }

        $permission = Permission::findOrFail($id);

        if ($request->status === 'Approved') {
            $classId   = $permission->student->class_id ?? $permission->student->school_class_id ?? null;
            $startDate = Carbon::parse($permission->start_date ?? $permission->permission_date ?? date('Y-m-d'));
            $endDate   = Carbon::parse($permission->end_date ?? $startDate);

            $hasPermitId = Schema::hasColumn('class_attendances', 'permit_id');

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $matchData = [
                    'student_id' => $permission->student_id,
                    'teacher_id' => $teacher->id,
                    'date'       => $date->format('Y-m-d'),
                ];

                $updateData = [
                    'class_id'    => $classId,
                    'status'      => $permission->type ?? $permission->permission_type ?? 'Izin',
                    'notes'       => 'Disetujui dari Surat Ortu: ' . ($permission->reason ?? $permission->notes ?? '-'),
                    'recorded_by' => auth()->id(),
                ];

                if ($hasPermitId) {
                    $updateData['permit_id'] = $permission->id;
                }

                ClassAttendance::updateOrCreate($matchData, $updateData);
            }
        }

        return redirect()->back()->with('success', 'Surat izin berhasil disetujui!');
    }

    // ==========================================
    // 📋 METHOD TAMBAHAN FITUR SIDEBAR GURU
    // ==========================================

    /**
     * 📅 1. Halaman Jadwal Mengajar Guru
     */
    public function schedules()
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        $schedules = LessonSchedule::with('schoolClass')
            ->when($teacher, function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('start_time', 'asc')
            ->get();

        return view('teacher.schedules.index', compact('schedules', 'teacher'));
    }

    /**
     * 👥 2. Halaman Data Siswa Ampuan / Wali Kelas
     */
    public function students()
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        $students = Student::with('schoolClass')
            ->when($teacher && ($teacher->class_id || $teacher->school_class_id), function ($q) use ($teacher) {
                $classId = $teacher->class_id ?? $teacher->school_class_id;
                if (Schema::hasColumn('students', 'class_id')) {
                    $q->where('class_id', $classId);
                } elseif (Schema::hasColumn('students', 'school_class_id')) {
                    $q->where('school_class_id', $classId);
                }
            })
            ->latest()
            ->get();

        return view('teacher.students.index', compact('students', 'teacher'));
    }

    /**
     * 📄 3. Halaman Jurnal & Rekap KBM
     */
    public function reports(Request $request)
    {
        if (\Route::has('teacher.class-attendance.recap')) {
            return redirect()->route('teacher.class-attendance.recap', $request->all());
        }
        if (\Route::has('teacher.attendance.recap')) {
            return redirect()->route('teacher.attendance.recap', $request->all());
        }

        return app(\App\Http\Controllers\Teacher\ClassAttendanceController::class)->recap($request);
    }

    /**
     * 📢 4. Halaman Pengumuman Sekolah Khusus Guru
     */
    public function announcements()
    {
        $announcements = Announcement::where('is_active', true)
            ->whereIn('target', ['all', 'teacher'])
            ->orderBy('date', 'desc')
            ->get();

        return view('teacher.announcements.index', compact('announcements'));
    }
}