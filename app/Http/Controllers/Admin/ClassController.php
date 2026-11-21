<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ClassController extends Controller
{
    /**
     * Menampilkan daftar kelas
     */
    public function index()
    {
        $classes = SchoolClass::with(['homeroomTeacher'])
            ->withCount('students')
            ->latest()
            ->paginate(10);

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Menampilkan form tambah kelas baru
     */
    public function create()
    {
        $teachers = Teacher::orderBy('name', 'asc')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    /**
     * Menyimpan data kelas baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'teacher_id'  => 'nullable|exists:teachers,id',
        ]);

        $updateData = $validated;
        if (Schema::hasColumn('classes', 'homeroom_teacher_id') && isset($validated['teacher_id'])) {
            $updateData['homeroom_teacher_id'] = $validated['teacher_id'];
        }

        SchoolClass::create($updateData);

        return redirect()->route('admin.classes.index')->with('success', 'Data Kelas berhasil disimpan!');
    }

    /**
     * Menampilkan detail kelas beserta daftar siswa dan wali kelas
     */
    public function show($id)
    {
        $class = SchoolClass::with(['homeroomTeacher', 'students' => function ($query) {
            $query->orderBy('name', 'asc');
        }])->findOrFail($id);

        $teachers = Teacher::orderBy('name', 'asc')->get();

        return view('admin.classes.show', compact('class', 'teachers'));
    }

    /**
     * Menampilkan form edit kelas
     */
    public function edit($id)
    {
        $class = SchoolClass::findOrFail($id);
        $teachers = Teacher::orderBy('name', 'asc')->get();

        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    /**
     * Memperbarui data kelas yang sudah ada
     */
    public function update(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'grade_level' => 'required|string|max:50',
            'teacher_id'  => 'nullable|exists:teachers,id',
        ]);

        $updateData = $validated;
        if (Schema::hasColumn('classes', 'homeroom_teacher_id')) {
            $updateData['homeroom_teacher_id'] = $request->teacher_id;
        }

        $class->update($updateData);

        return redirect()->route('admin.classes.index')->with('success', 'Data Kelas berhasil diperbarui!');
    }

    /**
     * Menetapkan atau mengubah Wali Kelas secara langsung dari detail kelas
     */
    public function assignHomeroomTeacher(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $class = SchoolClass::findOrFail($id);

        $updateData = [];

        if (Schema::hasColumn('classes', 'teacher_id')) {
            $updateData['teacher_id'] = $request->teacher_id;
        }

        if (Schema::hasColumn('classes', 'homeroom_teacher_id')) {
            $updateData['homeroom_teacher_id'] = $request->teacher_id;
        }

        // Jika tidak ada deteksi kolom spesifik, default update teacher_id
        if (empty($updateData)) {
            $updateData['teacher_id'] = $request->teacher_id;
        }

        $class->update($updateData);

        return redirect()->back()->with('success', 'Wali Kelas berhasil diperbarui!');
    }

    /**
     * Menghapus data kelas
     */
    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Data Kelas berhasil dihapus!');
    }
}