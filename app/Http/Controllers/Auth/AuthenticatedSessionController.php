<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login khusus berdasarkan role.
     *
     * @param string $role
     * @return View
     */
    public function createRoleLogin(string $role): View
    {
        $roleName = ucfirst($role);

        return view('auth.login', [
            'selectedRole' => $roleName
        ]);
    }

    /**
     * Display the login view.
     *
     * @return View
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Cek Apakah Login Menggunakan NIP (Jalur Portal Guru / Dual Role Principal yang Mengajar)
        if ($request->filled('nip')) {
            $request->validate([
                'nip'      => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            // Cari data guru berdasarkan NIP
            $teacher = Teacher::where('nip', trim($request->nip))->first();

            // Validasi keberadaan NIP dan relasi ke User
            if (!$teacher || !$teacher->user) {
                throw ValidationException::withMessages([
                    'nip' => 'NIP tidak terdaftar dalam sistem.',
                ]);
            }

            // Coba autentikasi menggunakan email user milik guru tersebut
            if (!Auth::attempt(['email' => $teacher->user->email, 'password' => $request->password], $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'nip' => 'Password yang Anda masukkan salah.',
                ]);
            }

            $request->session()->regenerate();

            // Selalu arahkan ke dashboard guru jika login via NIP
            return redirect()->intended(route('teacher.dashboard'));
        }

        // 2. Cek Apakah Login Menggunakan Nomor HP (Jalur Portal Orang Tua)
        $phoneInput = $request->input('phone') ?? $request->input('no_hp');

        if (!empty($phoneInput)) {
            $request->validate([
                'password' => ['required', 'string'],
            ]);

            $rawInput = trim($phoneInput);
            
            // Normalisasi nomor HP (hapus karakter non-angka dan ubah awalan 62/8 menjadi format baku)
            $cleanInput = preg_replace('/[^0-9]/', '', $rawInput);
            if (str_starts_with($cleanInput, '62')) {
                $cleanInput = '0' . substr($cleanInput, 2);
            }

            $user = null;

            // A. Cari siswa berdasarkan parent_phone di tabel Student
            $student = Student::where('parent_phone', $rawInput)
                ->orWhere('parent_phone', $cleanInput)
                ->orWhereRaw("REPLACE(REPLACE(parent_phone, ' ', ''), '-', '') = ?", [$cleanInput])
                ->first();

            if ($student) {
                $userId = $student->parent_user_id ?? $student->user_id ?? null;
                if ($userId) {
                    $user = User::find($userId);
                }

                // Jika akun User orang tua belum ada/terikat, buatkan otomatis
                if (!$user) {
                    $parentRole = Role::whereIn('name', ['Parent', 'Orang Tua', 'Ortu'])->first();
                    $parentRoleId = $parentRole ? $parentRole->id : 4;

                    $email = 'ortu.' . ($student->nis ?? $student->id) . '@school.com';

                    $userData = [
                        'name'     => 'Orang Tua / Wali ' . $student->name,
                        'password' => Hash::make($request->password), // Set password pertama kali
                        'role_id'  => $parentRoleId,
                    ];

                    if (Schema::hasColumn('users', 'phone')) {
                        $userData['phone'] = $cleanInput;
                    }
                    if (Schema::hasColumn('users', 'no_hp')) {
                        $userData['no_hp'] = $cleanInput;
                    }

                    $user = User::updateOrCreate(['email' => $email], $userData);

                    // Hubungkan ID User ke data Siswa
                    if (Schema::hasColumn('students', 'parent_user_id')) {
                        $student->update(['parent_user_id' => $user->id]);
                    } else {
                        $student->update(['user_id' => $user->id]);
                    }
                }
            }

            // B. Fallback: Cari langsung di tabel User jika di Student belum ditemukan
            if (!$user) {
                $hasUserPhone = Schema::hasColumn('users', 'phone');
                $hasUserNoHp = Schema::hasColumn('users', 'no_hp');

                if ($hasUserPhone || $hasUserNoHp) {
                    $user = User::where(function ($q) use ($rawInput, $cleanInput, $hasUserPhone, $hasUserNoHp) {
                        if ($hasUserPhone) {
                            $q->where('phone', $rawInput)
                              ->orWhere('phone', $cleanInput)
                              ->orWhereRaw("REPLACE(REPLACE(phone, ' ', ''), '-', '') = ?", [$cleanInput]);
                        }
                        if ($hasUserNoHp) {
                            $q->orWhere('no_hp', $rawInput)
                              ->orWhere('no_hp', $cleanInput)
                              ->orWhereRaw("REPLACE(REPLACE(no_hp, ' ', ''), '-', '') = ?", [$cleanInput]);
                        }
                    })->first();
                }
            }

            if (!$user) {
                throw ValidationException::withMessages([
                    'phone' => 'Nomor HP orang tua tidak terdaftar dalam sistem.',
                ]);
            }

            // Coba autentikasi menggunakan email user terkait
            if (!Auth::attempt(['email' => $user->email, 'password' => $request->password], $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'phone' => 'Password yang Anda masukkan salah.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('parent.dashboard'));
        }

        // 3. Jalur Login Standard (Email + Password untuk Admin/Role Lain)
        $loginRequest = app(LoginRequest::class);
        $loginRequest->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Mengambil nama role user
        $roleName = '';
        if (isset($user->role)) {
            $roleName = is_object($user->role) ? ($user->role->name ?? '') : (string) $user->role;
        }

        // Redirect otomatis berdasarkan Role User
        return match (ucfirst($roleName)) {
            'Admin'     => redirect()->intended(route('admin.dashboard')),
            'Teacher'   => redirect()->intended(route('teacher.dashboard')),
            'Guru'      => redirect()->intended(route('teacher.dashboard')),
            'Principal' => redirect()->intended(route('principal.dashboard')),
            'Kepsek'    => redirect()->intended(route('principal.dashboard')),
            'Parent'    => redirect()->intended(route('parent.dashboard')),
            'Orangtua'  => redirect()->intended(route('parent.dashboard')),
            'Ortu'      => redirect()->intended(route('parent.dashboard')),
            default     => redirect()->intended('/dashboard'),
        };
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}