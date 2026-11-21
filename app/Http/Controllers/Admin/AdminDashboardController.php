<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\GateAttendance;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Set zona waktu WIB secara tegas
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // 1. Data Ringkasan Umum
        $totalStudents = Student::count();
        $totalRombel   = SchoolClass::count();
        $totalTeachers = User::whereHas('role', function($q) {
            $q->where('name', 'Teacher');
        })->count();
        $totalUsers    = User::count();

        // 2. Data Kehadiran Gerbang Hari Ini (Real-time)
        $todayAttendances = GateAttendance::whereDate('date', $today)->get();
        $presentStudents  = $todayAttendances->whereNotNull('student_id')->count();

        $lateThreshold = '07:15:00';
        $totalLate     = $todayAttendances->where('scan_in', '>', $lateThreshold)->count();
        $totalOnTime   = max(0, $presentStudents - $totalLate);
        $totalAbsent   = max(0, $totalStudents - $presentStudents);

        // Persentase Kehadiran Hari Ini
        $attendancePercentage = $totalStudents > 0 
            ? round(($presentStudents / $totalStudents) * 100, 1) 
            : 0;

        // 3. FIX: Ambil Log Scan Terakhir Hari Ini (Memuat data Siswa DAN Guru)
        $recentScanLogs = GateAttendance::with(['student.schoolClass', 'teacher'])
            ->whereDate('date', $today)
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        // 4. Data Grafik Trend Kehadiran 7 Hari Terakhir
        $chartDates  = [];
        $chartOnTime = [];
        $chartLate   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now('Asia/Jakarta')->subDays($i)->format('Y-m-d');
            $formattedDate = Carbon::parse($date)->translatedFormat('d M');

            $dayLogs     = GateAttendance::whereDate('date', $date)->get();
            $lateCount   = $dayLogs->where('scan_in', '>', $lateThreshold)->count();
            $onTimeCount = max(0, $dayLogs->count() - $lateCount);

            $chartDates[]  = $formattedDate;
            $chartOnTime[] = $onTimeCount;
            $chartLate[]   = $lateCount;
        }

        return view('admin.index', compact(
            'totalStudents',
            'totalRombel',
            'totalTeachers',
            'totalUsers',
            'attendancePercentage',
            'presentStudents',
            'totalOnTime',
            'totalLate',
            'totalAbsent',
            'recentScanLogs',
            'chartDates',
            'chartOnTime',
            'chartLate'
        ));
    }
}