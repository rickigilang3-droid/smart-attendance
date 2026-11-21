<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('schoolClass');

        // Filter pencarian jika ada kata kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        // Data paginasi untuk tabel
        $students = $query->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Ambil seluruh data siswa secara lengkap untuk modal detail & auto-suggest
        $allStudents = Student::with('schoolClass:id,name')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.students.index', compact('students', 'allStudents'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn'                => 'required|unique:students,nisn',
            'nis'                 => 'nullable|string',
            'name'                => 'required|string|max:255',
            'gender'              => 'required|in:L,P',
            'class_id'            => 'required|exists:classes,id',
            'parent_phone'        => 'required|string',
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Data Pribadi
            'nik'                 => 'nullable|string',
            'kk'                  => 'nullable|string',
            'pob'                 => 'nullable|string',
            'dob'                 => 'nullable|date',
            'religion'            => 'nullable|string',
            'address'             => 'nullable|string',
            'rt_rw'               => 'nullable|string',
            'village_subdistrict' => 'nullable|string',
            'residence_status'    => 'nullable|string',
            'transportation'      => 'nullable|string',
            'child_number'        => 'nullable|integer',
            'total_siblings'      => 'nullable|integer',

            // Periodik & Sekolah
            'height'              => 'nullable|numeric',
            'weight'              => 'nullable|numeric',
            'distance_to_school'  => 'nullable|string',
            'travel_time'         => 'nullable|string',
            'previous_school'     => 'nullable|string',
            'diploma_skhu_number' => 'nullable|string',

            // Orang Tua & Kontak
            'father_name'         => 'nullable|string',
            'father_job'          => 'nullable|string',
            'father_income'       => 'nullable|string',
            'mother_name'         => 'nullable|string',
            'mother_job'          => 'nullable|string',
            'mother_income'       => 'nullable|string',
            'parent_email'        => 'nullable|email',

            // Kesejahteraan & Beasiswa
            'kip_pip_number'      => 'nullable|string',
            'pkh_kps_number'      => 'nullable|string',
            'achievement_history' => 'nullable|string',
            'scholarship'         => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students-photos', 'public');
        }

        $student = Student::create($data);

        // =========================================================================
        // 🚀 OTOMATIS BUATKAN AKUN USER ORANG TUA UNTUK LOGIN PORTAL
        // =========================================================================
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->parent_phone);

        if (!empty($cleanPhone)) {
            $parentName = 'Orang Tua / Wali ' . $student->name;
            $defaultEmail = $cleanPhone . '@parent.com';

            $userQuery = User::query();
            $hasPhoneColumn = Schema::hasColumn('users', 'phone');
            $hasUsernameColumn = Schema::hasColumn('users', 'username');

            $userQuery->where(function ($q) use ($cleanPhone, $defaultEmail, $hasPhoneColumn, $hasUsernameColumn) {
                if ($hasPhoneColumn) {
                    $q->orWhere('phone', $cleanPhone);
                }
                if ($hasUsernameColumn) {
                    $q->orWhere('username', $cleanPhone);
                }
                $q->orWhere('email', $defaultEmail);
            });

            $parentUser = $userQuery->first();

            if (!$parentUser) {
                $userData = [
                    'name'     => $parentName,
                    'email'    => $request->input('parent_email', $defaultEmail),
                    'password' => Hash::make($cleanPhone), // Password default: Nomor HP
                ];

                if ($hasPhoneColumn) {
                    $userData['phone'] = $cleanPhone;
                }

                if ($hasUsernameColumn) {
                    $userData['username'] = $cleanPhone;
                }

                // Cek skema role (role vs role_id)
                if (Schema::hasColumn('users', 'role_id')) {
                    // Cari ID role 'parent' atau default 3
                    $roleId = DB::table('roles')->where('name', 'parent')->value('id') ?? 3;
                    $userData['role_id'] = $roleId;
                }

                if (Schema::hasColumn('users', 'role')) {
                    $userData['role'] = 'parent';
                }

                $parentUser = User::create($userData);
            } else {
                $parentUser->update(['name' => $parentName]);
            }

            // Hubungkan foreign key ke user orang tua
            if (Schema::hasColumn('students', 'parent_user_id')) {
                $student->update(['parent_user_id' => $parentUser->id]);
            } elseif (Schema::hasColumn('students', 'user_id')) {
                $student->update(['user_id' => $parentUser->id]);
            }
        }

        return redirect()->route('admin.students.index')->with('success', 'Data siswa lengkap berhasil disimpan! Akun Orang Tua otomatis aktif.');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $classes = SchoolClass::orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'nisn'                => 'required|unique:students,nisn,' . $id,
            'nis'                 => 'nullable|string',
            'name'                => 'required|string|max:255',
            'gender'              => 'required|in:L,P',
            'class_id'            => 'required|exists:classes,id',
            'parent_phone'        => 'required|string',
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Data Pribadi
            'nik'                 => 'nullable|string',
            'kk'                  => 'nullable|string',
            'pob'                 => 'nullable|string',
            'dob'                 => 'nullable|date',
            'religion'            => 'nullable|string',
            'address'             => 'nullable|string',
            'rt_rw'               => 'nullable|string',
            'village_subdistrict' => 'nullable|string',
            'residence_status'    => 'nullable|string',
            'transportation'      => 'nullable|string',
            'child_number'        => 'nullable|integer',
            'total_siblings'      => 'nullable|integer',

            // Periodik & Sekolah
            'height'              => 'nullable|numeric',
            'weight'              => 'nullable|numeric',
            'distance_to_school'  => 'nullable|string',
            'travel_time'         => 'nullable|string',
            'previous_school'     => 'nullable|string',
            'diploma_skhu_number' => 'nullable|string',

            // Orang Tua & Kontak
            'father_name'         => 'nullable|string',
            'father_job'          => 'nullable|string',
            'father_income'       => 'nullable|string',
            'mother_name'         => 'nullable|string',
            'mother_job'          => 'nullable|string',
            'mother_income'       => 'nullable|string',
            'parent_email'        => 'nullable|email',

            // Kesejahteraan & Beasiswa
            'kip_pip_number'      => 'nullable|string',
            'pkh_kps_number'      => 'nullable|string',
            'achievement_history' => 'nullable|string',
            'scholarship'         => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('students-photos', 'public');
        }

        $student->update($data);

        // Synchronize/buatkan akun orang tua jika ada perubahan nomor HP
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->parent_phone);
        if (!empty($cleanPhone)) {
            $parentName = 'Orang Tua / Wali ' . $student->name;
            $defaultEmail = $cleanPhone . '@parent.com';

            $userQuery = User::query();
            $hasPhoneColumn = Schema::hasColumn('users', 'phone');
            $hasUsernameColumn = Schema::hasColumn('users', 'username');

            $userQuery->where(function ($q) use ($cleanPhone, $defaultEmail, $hasPhoneColumn, $hasUsernameColumn) {
                if ($hasPhoneColumn) {
                    $q->orWhere('phone', $cleanPhone);
                }
                if ($hasUsernameColumn) {
                    $q->orWhere('username', $cleanPhone);
                }
                $q->orWhere('email', $defaultEmail);
            });

            $parentUser = $userQuery->first();

            if (!$parentUser) {
                $userData = [
                    'name'     => $parentName,
                    'email'    => $defaultEmail,
                    'password' => Hash::make($cleanPhone),
                ];

                if ($hasPhoneColumn) {
                    $userData['phone'] = $cleanPhone;
                }

                if ($hasUsernameColumn) {
                    $userData['username'] = $cleanPhone;
                }

                if (Schema::hasColumn('users', 'role_id')) {
                    $roleId = DB::table('roles')->where('name', 'parent')->value('id') ?? 3;
                    $userData['role_id'] = $roleId;
                }

                if (Schema::hasColumn('users', 'role')) {
                    $userData['role'] = 'parent';
                }

                $parentUser = User::create($userData);
            } else {
                $parentUser->update(['name' => $parentName]);
            }

            if (Schema::hasColumn('students', 'parent_user_id')) {
                $student->update(['parent_user_id' => $parentUser->id]);
            } elseif (Schema::hasColumn('students', 'user_id')) {
                $student->update(['user_id' => $parentUser->id]);
            }
        }

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil dihapus!');
    }
}