<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Ambil nama role secara aman (mendukung relasi $user->role->name maupun string $user->role)
        $userRoleName = $user->role->name ?? $user->role ?? '';

        // 3. Ubah role user menjadi lowercase untuk perbandingan case-insensitive
        $userRole = strtolower(trim((string) $userRoleName));

        // 4. Jika user adalah Admin/Administrator, selalu beri akses penuh tanpa terlempar
        if (in_array($userRole, ['admin', 'administrator'])) {
            return $next($request);
        }

        // 5. Ubah daftar role yang diizinkan dari route ke lowercase
        $allowedRoles = array_map(function ($r) {
            return strtolower(trim($r));
        }, $roles);

        // 6. Cek apakah role user ada dalam daftar role yang diizinkan
        if ($userRole !== '' && in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        abort(403);
    }
}
