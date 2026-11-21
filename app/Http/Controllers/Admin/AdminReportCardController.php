<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ClassAttendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class AdminReportCardController extends Controller
{
    // Halaman Monitoring Status Nilai & Daftar Rapor Siswa
    public function index(Request $request)
    {
        $classes = SchoolClass::all();
        $selectedClassId = $request->get('class_id', $classes->first()->id ?? null);
        $semester = $request->get('semester', 'Ganjil');

        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;
        $students = collect();

        if ($selectedClass) {
            $students = Student::where('class_id', $selectedClass->id)->orderBy('name', 'asc')->get();
        }

        // Monitoring status mapel yang sudah disubmit di kelas ini
        $submittedSubjects = Assessment::where('class_id', $selectedClassId)
            ->where('semester', $semester)
            ->where('status', 'submitted')
            ->select('subject_name')
            ->distinct()
            ->pluck('subject_name');

        return view('admin.report-card.index', compact('classes', 'selectedClass', 'students', 'semester', 'submittedSubjects'));
    }

    // Tampilan Detail / Preview Rapor Siswa
    public function show(Request $request, $studentId)
    {
        $student = Student::with('schoolClass')->findOrFail($studentId);
        $semester = $request->get('semester', 'Ganjil');

        // Ambil semua nilai dari seluruh mata pelajaran yang sudah disubmit guru
        $assessments = Assessment::where('student_id', $student->id)
            ->where('semester', $semester)
            ->where('status', 'submitted')
            ->get();

        // Rekapitulasi Presensi untuk Rapor (Hadir, Sakit, Izin, Alpha)
        $attendances = ClassAttendance::where('student_id', $student->id)->get();
        $attendanceRecap = [
            'sakit' => $attendances->whereIn('status', ['Sakit', 'S'])->count(),
            'izin'  => $attendances->whereIn('status', ['Izin', 'I'])->count(),
            'alpha' => $attendances->whereIn('status', ['Alpha', 'Alpa', 'A'])->count(),
        ];

        return view('admin.report-card.show', compact('student', 'semester', 'assessments', 'attendanceRecap'));
    }
}