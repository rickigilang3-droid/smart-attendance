<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\ClassAttendance;
use App\Models\GateAttendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use App\Models\LessonSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrincipalDashboardController extends Controller
{
    /**
     * 1. Dashboard Supervisi Utama Kepala Sekolah
     */
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $dayName = Carbon::now()->locale('id')->isoFormat('dddd');

        // Total Master Data
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalClasses = SchoolClass::count();

        // Rekapitulasi Presensi Gerbang Siswa Hari Ini (Smart Gate)
        $todayGateLogs = GateAttendance::whereNotNull('student_id')
            ->whereDate('created_at', $today)
            ->get();
        $gatePunctualCount = $todayGateLogs->whereIn('status', ['Tepat Waktu', 'Hadir', 'On Time', 'present'])->count();
        $gateLateCount = $todayGateLogs->whereIn('status', ['Terlambat', 'Late', 'late'])->count();

        // Rekapitulasi Presensi Gerbang Guru Hari Ini (Smart Gate)
        $todayTeacherGateLogs = GateAttendance::with('teacher')
            ->whereNotNull('teacher_id')
            ->whereDate('created_at', $today)
            ->orderBy('scan_in', 'asc')
            ->get();
        $teacherGatePunctualCount = $todayTeacherGateLogs->whereIn('status', ['Tepat Waktu', 'Hadir', 'On Time', 'present'])->count();
        $teacherGateLateCount = $todayTeacherGateLogs->whereIn('status', ['Terlambat', 'Late', 'late'])->count();

        // Rekapitulasi Presensi Kelas Hari Ini
        $todayAttendances = ClassAttendance::whereDate('date', $today)->get();
        $recapToday = [
            'hadir' => $todayAttendances->whereIn('status', ['Hadir', 'HADIR', 'H', 'present'])->count(),
            'sakit' => $todayAttendances->whereIn('status', ['Sakit', 'SAKIT', 'S', 'sick'])->count(),
            'izin'  => $todayAttendances->whereIn('status', ['Izin', 'IZIN', 'I', 'permit'])->count(),
            'alpha' => $todayAttendances->whereIn('status', ['Alpha', 'ALPHA', 'Alpa', 'A', 'alpha'])->count(),
        ];

        // Status KBM Kelas Hari Ini
        $todaySchedules = LessonSchedule::with(['schoolClass', 'teacher'])
            ->where('day', $dayName)
            ->orderBy('start_time', 'asc')
            ->get();

        // Ambil 1 Jurnal Terbaru per Kelas + Guru agar tidak ada duplikasi kelas
        $latestJournalIds = TeachingJournal::whereDate('date', $today)
            ->selectRaw('MAX(id) as id')
            ->groupBy('class_id', 'teacher_id')
            ->pluck('id');

        $rawJournals = TeachingJournal::with(['schoolClass', 'teacher'])
            ->whereIn('id', $latestJournalIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Olah data jurnal agar status keterlambatan dihitung presisi berdasarkan jam Buka Kelas
        $todayJournals = $rawJournals->map(function ($journal) use ($dayName) {
            $openTime = $journal->opened_at 
                ? Carbon::parse($journal->opened_at)->format('H:i') 
                : Carbon::parse($journal->created_at)->format('H:i');

            // Cocokkan jadwal pelajaran spesifik untuk guru, kelas, dan hari ini
            $schedule = LessonSchedule::where('class_id', $journal->class_id)
                ->where('teacher_id', $journal->teacher_id)
                ->where('day', $dayName)
                ->first();

            $startTime = $schedule ? Carbon::parse($schedule->start_time)->format('H:i') : '07:15';
            $isLate = !empty($journal->late_reason) || ($openTime > $startTime);

            $journal->evaluated_status = $isLate ? 'TERLAMBAT' : 'TEPAT WAKTU';
            $journal->open_time_formatted = $openTime;
            $journal->schedule_start_formatted = $startTime;

            return $journal;
        });

        return view('principal.dashboard', compact(
            'today',
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'gatePunctualCount',
            'gateLateCount',
            'todayTeacherGateLogs',
            'teacherGatePunctualCount',
            'teacherGateLateCount',
            'recapToday',
            'todaySchedules',
            'todayJournals'
        ));
    }

    /**
     * 2. Monitoring KBM Real-time (Hanya Guru yang Sudah Buka Kelas & Rekap Bulanan)
     */
    public function kbmMonitoring(Request $request)
    {
        $viewType      = $request->get('view', 'daily');
        $selectedDate  = $request->get('date', date('Y-m-d'));
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear  = $request->get('year', date('Y'));

        $today   = Carbon::parse($selectedDate)->format('Y-m-d');
        $dayName = Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd');

        // Ambil HANYA jurnal kelas yang benar-benar sudah dibuka guru pada tanggal terpilih
        $openedJournals = TeachingJournal::with(['schoolClass', 'teacher'])
            ->whereDate('date', $today)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($journal) use ($dayName) {
                $schedule = LessonSchedule::where('class_id', $journal->class_id)
                    ->where('teacher_id', $journal->teacher_id)
                    ->where('day', $dayName)
                    ->first();

                $openTime = $journal->opened_at
                    ? Carbon::parse($journal->opened_at)->format('H:i')
                    : Carbon::parse($journal->created_at)->format('H:i');

                $startTime = $schedule ? Carbon::parse($schedule->start_time)->format('H:i') : '07:15';
                $endTime   = $schedule ? Carbon::parse($schedule->end_time)->format('H:i') : '-';

                $startCarbon = Carbon::parse($startTime);
                $openCarbon  = Carbon::parse($openTime);

                $isLate = !empty($journal->late_reason) || $openCarbon->gt($startCarbon);
                $lateMinutes = $openCarbon->gt($startCarbon) ? $startCarbon->diffInMinutes($openCarbon) : 0;

                $journal->subject_name = $journal->subject ?? ($schedule->subject ?? 'Mata Pelajaran');
                $journal->time_range   = $schedule ? "{$startTime} - {$endTime}" : $openTime;
                $journal->open_time    = $openTime;
                $journal->is_late      = $isLate;
                $journal->late_minutes = $lateMinutes;

                return $journal;
            });

        $journals = $openedJournals->keyBy(function ($item) {
            return $item->class_id . '_' . $item->teacher_id;
        });

        $schedules = LessonSchedule::with(['schoolClass', 'teacher'])
            ->where('day', $dayName)
            ->whereIn('teacher_id', $openedJournals->pluck('teacher_id'))
            ->whereIn('class_id', $openedJournals->pluck('class_id'))
            ->orderBy('start_time', 'asc')
            ->get();

        // Data Rekap Bulanan
        $monthlyRecap = collect();
        if ($viewType === 'monthly') {
            $monthlyJournals = TeachingJournal::whereMonth('date', $selectedMonth)
                ->whereYear('date', $selectedYear)
                ->get();

            $monthlyRecap = Teacher::with('user')->get()->map(function ($teacher) use ($monthlyJournals) {
                $tJournals = $monthlyJournals->where('teacher_id', $teacher->id);

                $lateCount = $tJournals->filter(function ($j) {
                    $openTime = $j->opened_at
                        ? Carbon::parse($j->opened_at)->format('H:i')
                        : Carbon::parse($j->created_at)->format('H:i');
                    return !empty($j->late_reason) || ($openTime > '07:15');
                })->count();

                $teacher->teaching_journals_count = $tJournals->count();
                $teacher->punctual_count = max(0, $tJournals->count() - $lateCount);
                $teacher->late_count = $lateCount;

                return $teacher;
            });
        }

        return view('principal.kbm.index', compact(
            'viewType',
            'today',
            'dayName',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'openedJournals',
            'schedules',
            'journals',
            'monthlyRecap'
        ));
    }

    public function monitoring(Request $request)
    {
        return $this->kbmMonitoring($request);
    }

    /**
     * 3. Kedisiplinan Guru (Buka Kelas & Rekap Absen Gerbang Smart Gate)
     */
    public function teachersDiscipline(Request $request)
    {
        $month        = $request->get('month', date('m'));
        $year         = $request->get('year', date('Y'));
        $selectedDate = $request->get('date', date('Y-m-d'));

        $teachers = Teacher::with(['user'])->orderBy('name', 'asc')->get();

        // A. Data Jurnal Buka Kelas Bulanan
        $journalsData = TeachingJournal::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        // B. Data Absensi Gerbang Guru (Smart Gate) Bulanan & Harian
        $monthlyGateData = GateAttendance::whereNotNull('teacher_id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $dailyGateLogs = GateAttendance::with('teacher')
            ->whereNotNull('teacher_id')
            ->whereDate('date', $selectedDate)
            ->orderBy('scan_in', 'asc')
            ->get();

        $disciplineRecap = [];
        foreach ($teachers as $teacher) {
            // 1. Hitung Rekap Buka Kelas
            $teacherJournals = $journalsData->where('teacher_id', $teacher->id);
            $lateCount = 0;
            foreach ($teacherJournals as $j) {
                $openTime = $j->opened_at 
                    ? Carbon::parse($j->opened_at)->format('H:i') 
                    : Carbon::parse($j->created_at)->format('H:i');

                if (!empty($j->late_reason) || $openTime > '07:15') {
                    $lateCount++;
                }
            }

            // 2. Hitung Rekap Absen Gerbang (Smart Gate)
            $teacherGates = $monthlyGateData->where('teacher_id', $teacher->id);
            $gateLateCount = $teacherGates->whereIn('status', ['late', 'Terlambat', 'TERLAMBAT'])->count();
            $gatePunctualCount = $teacherGates->whereIn('status', ['present', 'Tepat Waktu', 'TEPAT WAKTU', 'Hadir'])->count();

            $disciplineRecap[$teacher->id] = [
                'name'                => $teacher->name ?? $teacher->user->name ?? '-',
                'nip'                 => $teacher->nip ?? '-',
                'subject'             => $teacher->subject ?? '-',
                // Statistik Buka Kelas
                'total_journals'      => $teacherJournals->count(),
                'late_count'          => $lateCount,
                'punctual_count'      => max(0, $teacherJournals->count() - $lateCount),
                // Statistik Absen Gerbang
                'gate_total'          => $teacherGates->count(),
                'gate_punctual_count' => $gatePunctualCount,
                'gate_late_count'     => $gateLateCount,
            ];
        }

        return view('principal.teachers.index', compact(
            'teachers',
            'disciplineRecap',
            'dailyGateLogs',
            'selectedDate',
            'month',
            'year'
        ));
    }

    public function discipline(Request $request)
    {
        return $this->teachersDiscipline($request);
    }

    /**
     * 4. Laporan & Rekapitulasi Presensi
     */
    public function reports(Request $request)
    {
        $selectedClassId = $request->get('class_id');
        $month = $request->get('month', date('m'));
        $year  = $request->get('year', date('Y'));

        $classes = SchoolClass::all();
        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : $classes->first();

        $students = collect();
        $recap = [];

        if ($selectedClass) {
            $students = Student::where('class_id', $selectedClass->id)->orderBy('name', 'asc')->get();

            foreach ($students as $student) {
                $attendances = ClassAttendance::where('student_id', $student->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();

                $recap[$student->id] = [
                    'hadir' => $attendances->whereIn('status', ['Hadir', 'H', 'present'])->count(),
                    'sakit' => $attendances->whereIn('status', ['Sakit', 'S', 'sick'])->count(),
                    'izin'  => $attendances->whereIn('status', ['Izin', 'I', 'permit'])->count(),
                    'alpha' => $attendances->whereIn('status', ['Alpa', 'Alpha', 'A', 'alpha'])->count(),
                    'total' => $attendances->count()
                ];
            }
        }

        return view('principal.reports.index', compact('classes', 'selectedClass', 'students', 'recap', 'month', 'year'));
    }
}