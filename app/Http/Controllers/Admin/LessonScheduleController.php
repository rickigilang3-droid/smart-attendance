<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LessonScheduleController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil SEMUA data master kelas dengan aman (menggunakan kolom name)
        $classes = SchoolClass::orderBy('name', 'asc')->get();

        // 2. Query data jadwal pelajaran
        $schedulesQuery = LessonSchedule::with(['schoolClass', 'teacher']);

        // Filter berdasarkan kelas jika ada input pilihan filter kelas
        if ($request->filled('class_id')) {
            $classId = $request->input('class_id');
            $schedulesQuery->where(function ($q) use ($classId) {
                if (Schema::hasColumn('lesson_schedules', 'class_id')) {
                    $q->where('class_id', $classId);
                }
                if (Schema::hasColumn('lesson_schedules', 'school_class_id')) {
                    $q->orWhere('school_class_id', $classId);
                }
            });
        }

        $schedules = $schedulesQuery
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderBy('start_time', 'asc')
            ->get();

        return view('admin.schedules.index', compact('schedules', 'classes'));
    }

    public function create()
    {
        $classes  = SchoolClass::all();
        $teachers = Teacher::all();
        $days     = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return view('admin.schedules.create', compact('classes', 'teachers', 'days'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'subject'    => 'required|string|max:255',
            'day'        => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        LessonSchedule::create($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil disimpan!');
    }

    public function destroy(LessonSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil dihapus!');
    }
}