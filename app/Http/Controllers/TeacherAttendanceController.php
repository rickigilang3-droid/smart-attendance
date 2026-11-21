<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeacherAttendanceController extends Controller
{
    /**
     * Menampilkan halaman pencatatan presensi siswa.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $classes = [
            'X-IPA-1' => 'Kelas X IPA 1',
            'X-IPA-2' => 'Kelas X IPA 2',
            'XI-IPS-1' => 'Kelas XI IPS 1',
        ];

        $selectedClass = (string) $request->input('class_code', 'X-IPA-1');
        $attendanceDate = (string) $request->input('date', date('Y-m-d'));

        $studentsQuery = User::whereHas('role', function ($query) {
            $query->where('name', 'Student');
        })->get();

        /** @var array<int, array{id: int, nis: string, name: string, gender: string}> $students */
        $students = [];

        if ($studentsQuery->count() > 0) {
            foreach ($studentsQuery as $s) {
                $students[] = [
                    'id' => (int) $s->id,
                    'nis' => (string) ($s->nis ?? '2024' . sprintf('%03d', $s->id)),
                    'name' => (string) $s->name,
                    'gender' => (string) ($s->gender ?? 'L'),
                ];
            }
        } else {
            $students = [
                ['id' => 1, 'nis' => '2024001', 'name' => 'Ahmad Rizky Pratama', 'gender' => 'L'],
                ['id' => 2, 'nis' => '2024002', 'name' => 'Anisa Rahmawati', 'gender' => 'P'],
                ['id' => 3, 'nis' => '2024003', 'name' => 'Budi Santoso', 'gender' => 'L'],
                ['id' => 4, 'nis' => '2024004', 'name' => 'Citra Dewi', 'gender' => 'P'],
                ['id' => 5, 'nis' => '2024005', 'name' => 'Dwi Cahyono', 'gender' => 'L'],
            ];
        }

        $savedAttendances = Attendance::where('class_code', $selectedClass)
            ->where('date', $attendanceDate)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('teacher.attendance.index', compact('classes', 'selectedClass', 'attendanceDate', 'students', 'savedAttendances'));
    }

    /**
     * Menyimpan atau memperbarui data presensi siswa.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'class_code' => 'required|string',
            'date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        $teacherId = Auth::id() ?? 1;

        /** @var array<int, string> $attendanceData */
        $attendanceData = $request->input('attendance', []);

        foreach ($attendanceData as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'class_code' => $request->input('class_code'),
                    'date' => $request->input('date'),
                    'student_id' => $studentId,
                ],
                [
                    'teacher_id' => $teacherId,
                    'status' => $status,
                ]
            );
        }

        return redirect()->back()->with('success', 'Data presensi kehadiran berhasil disimpan!');
    }
}