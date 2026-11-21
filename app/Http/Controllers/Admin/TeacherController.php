<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with('user');

        // Filter pencarian jika ada query 'search'
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%')
                  ->orWhere('subject', 'like', '%' . $search . '%');
            });
        }

        // Data paginasi untuk tabel utama
        $teachers = $query->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Ambil seluruh guru untuk instant search & auto-suggest lintas halaman
        $allTeachers = Teacher::select('id', 'name', 'nip', 'subject', 'phone')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.teachers.index', compact('teachers', 'allTeachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'     => 'required|unique:teachers,nip',
            'name'    => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
        ]);

        Teacher::create($request->all());

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru/pengajar berhasil ditambahkan!');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'nip'     => 'required|unique:teachers,nip,' . $teacher->id,
            'name'    => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
        ]);

        $teacher->update($request->all());

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru/pengajar berhasil diperbarui!');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Data guru/pengajar berhasil dihapus!');
    }
}