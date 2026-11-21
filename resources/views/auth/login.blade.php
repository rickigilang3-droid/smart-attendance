@php
    $role = strtolower($selectedRole ?? 'guest');

    // Konfigurasi Warna & Label Role
    $roleConfig = match($role) {
        'teacher'   => [
            'title'       => 'Portal Guru & Pengajar',
            'subtitle'    => 'Masuk NIP Anda untuk presensi & KBM',
            'label'       => 'NIP GURU',
            'field'       => 'nip',
            'type'        => 'text',
            'placeholder' => 'Masukkan NIP Guru',
            'color'       => '#059669',
            'bg_badge'    => '#dcfce7',
            'text_badge'  => '#166534',
        ],
        'admin'     => [
            'title'       => 'System Administrator',
            'subtitle'    => 'Masuk kelola master data & sistem',
            'label'       => 'EMAIL SEKOLAH',
            'field'       => 'email',
            'type'        => 'email',
            'placeholder' => 'nama@sekolah.sch.id',
            'color'       => '#4f46e5',
            'bg_badge'    => '#e0e7ff',
            'text_badge'  => '#3730a3',
        ],
        'principal' => [
            'title'       => 'Portal Kepala Sekolah',
            'subtitle'    => 'Masuk untuk laporan & supervisi KBM',
            'label'       => 'EMAIL SEKOLAH',
            'field'       => 'email',
            'type'        => 'email',
            'placeholder' => 'nama@sekolah.sch.id',
            'color'       => '#d97706',
            'bg_badge'    => '#fef3c7',
            'text_badge'  => '#92400e',
        ],
        'parent'    => [
            'title'       => 'Portal Orang Tua / Wali',
            'subtitle'    => 'Masuk Nomor HP pantau kehadiran',
            'label'       => 'NOMOR HP ORANG TUA',
            'field'       => 'phone',
            'type'        => 'tel',
            'placeholder' => 'Contoh: 081234567001',
            'color'       => '#0284c7',
            'bg_badge'    => '#e0f2fe',
            'text_badge'  => '#075985',
        ],
        default     => [
            'title'       => 'Portal Akses Pengguna',
            'subtitle'    => 'Masukkan kredensial akun Anda',
            'label'       => 'EMAIL SEKOLAH',
            'field'       => 'email',
            'type'        => 'email',
            'placeholder' => 'nama@sekolah.sch.id',
            'color'       => '#4f46e5',
            'bg_badge'    => '#e0e7ff',
            'text_badge'  => '#3730a3',
        ],
    };

    $themeColor = $roleConfig['color'];

    // Gambar background lokal: bg-school-1, bg-school-2, bg-school-3 (ekstensi apa pun)
    $bgSlides = [];
    foreach ([1, 2, 3] as $i) {
        $found = glob(public_path("storage/logo-sekolah/bg-school-{$i}.*"));
        if (!empty($found)) {
            $bgSlides[] = asset('storage/logo-sekolah/' . rawurlencode(basename($found[0])));
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Login - SMA XYZ JAKARTA</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; height: 100vh; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #0f172a;" class="font-sans antialiased">

    <!-- BACKGROUND SLIDER (JS BIASA, GAMBAR LOKAL) -->
    <div id="bg-slider" style="position: fixed; inset: 0; z-index: 0; pointer-events: none;">
        @foreach($bgSlides as $i => $img)
            <div class="bg-slide"
                 style="position: absolute; inset: 0; background-image: url('{{ $img }}'); background-size: cover; background-position: center; opacity: {{ $i === 0 ? 1 : 0 }}; transition: opacity 1.2s ease-in-out;">
            </div>
        @endforeach
    </div>

    <!-- DARK OVERLAY TRANSPARAN HALUS (AGAR FOTO BG TERLIHAT JELAS) -->
    <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(2px); z-index: 1; pointer-events: none;"></div>

    <!-- MAIN COMPACT LOGIN CARD (MAKSIMAL 420PX DI SEMUA ROLE PORTAL) -->
    <div style="position: relative; z-index: 10; width: 100%; max-width: 420px; background: #ffffff; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6); overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.4);">

        <!-- HEADER KARTU TERPADU (DARK TOP BANNER) -->
        <div style="background: #0f172a; padding: 20px 20px 16px 20px; text-align: center; border-bottom: 4px solid {{ $themeColor }};">

            <!-- LOGO SEKOLAH -->
            <div style="width: 52px; height: 52px; margin: 0 auto 8px auto; background: #ffffff; border-radius: 14px; padding: 5px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;">
                <img
                    src="{{ asset('storage/logo-sekolah/logo-sekolah.jpg') }}"
                    onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/2991/2991148.png';"
                    alt="Logo Sekolah"
                    style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px;"
                >
            </div>

            <h1 style="color: #ffffff; font-size: 15px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; margin: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.5);">
                PORTAL SEKOLAH PINTAR
            </h1>
            <h2 style="color: #94a3b8; font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin: 2px 0 0 0;">
                SMA XYZ JAKARTA
            </h2>
            <p style="color: #64748b; font-size: 10px; font-weight: 500; margin: 3px 0 0 0;">
                Sistem Informasi Akademik & Presensi Kehadiran
            </p>
        </div>

        <!-- ISI FORM & SWITCHER PORTAL -->
        <div style="padding: 18px 22px 18px 22px;">

            <!-- BADGE ROLE LOGIN AKTIF -->
            <div style="text-align: center; margin-bottom: 14px;">
                <span style="display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 10.5px; font-weight: 700; background-color: {{ $roleConfig['bg_badge'] }}; color: {{ $roleConfig['text_badge'] }}; margin-bottom: 4px;">
                    ● {{ $roleConfig['title'] }}
                </span>
                <p style="color: #64748b; font-size: 11px; margin: 0; font-weight: 500;">
                    {{ $roleConfig['subtitle'] }}
                </p>
            </div>

            <!-- STATUS PESAN PEMBERITAHUAN -->
            <x-auth-session-status class="mb-2 text-xs text-center" :status="session('status')" />

            <!-- FORM INPUT -->
            <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 12px;" autocomplete="off">
                @csrf

                <!-- INPUT UTAMA (DINAMIS SESUAI ROLE) -->
                <div>
                    <label style="display: block; font-size: 9.5px; font-weight: 700; color: #475569; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 3px;">
                        {{ $roleConfig['label'] }}
                    </label>
                    <div style="position: relative;">
                        <div style="position: absolute; top: 0; bottom: 0; left: 0; padding-left: 10px; display: flex; align-items: center; pointer-events: none; color: #94a3b8;">
                            <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($role === 'teacher')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2" />
                                @elseif($role === 'parent')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                @endif
                            </svg>
                        </div>
                        <input
                            id="{{ $roleConfig['field'] }}"
                            type="{{ $roleConfig['type'] }}"
                            name="{{ $roleConfig['field'] }}"
                            value="{{ old($roleConfig['field']) }}"
                            required
                            autofocus
                            placeholder="{{ $roleConfig['placeholder'] }}"
                            style="width: 100%; padding: 8px 10px 8px 32px; font-size: 11.5px; color: #0f172a; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; outline: none;"
                            class="focus:border-indigo-500 focus:bg-white transition"
                        />
                    </div>
                    <x-input-error :messages="$errors->get($roleConfig['field'])" class="mt-1 text-xs" />
                </div>

                <!-- INPUT PASSWORD -->
                <div>
                    <label style="display: block; font-size: 9.5px; font-weight: 700; color: #475569; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 3px;">
                        PASSWORD
                    </label>
                    <div style="position: relative;">
                        <div style="position: absolute; top: 0; bottom: 0; left: 0; padding-left: 10px; display: flex; align-items: center; pointer-events: none; color: #94a3b8;">
                            <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="••••••••"
                            style="width: 100%; padding: 8px 10px 8px 32px; font-size: 11.5px; color: #0f172a; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; outline: none;"
                            class="focus:border-indigo-500 focus:bg-white transition"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                </div>

                <!-- CHECKBOX & FORGOT PASSWORD -->
                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px;">
                    <label style="display: flex; align-items: center; cursor: pointer; color: #475569;">
                        <input id="remember_me" type="checkbox" name="remember" style="border-radius: 4px; border: 1px solid #cbd5e1; width: 13px; height: 13px;">
                        <span style="margin-left: 5px;">Ingat Saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: #64748b; font-weight: 600; text-decoration: none;" class="hover:underline">
                            Lupa Password?
                        </a>
                    @endif
                </div>

                <!-- TOMBOL SUBMIT -->
                <button
                    type="submit"
                    style="width: 100%; padding: 10px; border-radius: 10px; border: none; background-color: {{ $themeColor }}; color: #ffffff; font-weight: 800; font-size: 11.5px; letter-spacing: 0.05em; text-transform: uppercase; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.15);"
                    class="hover:opacity-90 active:scale-[0.99] transition duration-150"
                >
                    Masuk Sistem
                </button>
            </form>

            <!-- SWITCHER ROLE NAVIGATION -->
            <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #e2e8f0; text-align: center;">
                <p style="font-size: 8.5px; font-weight: 800; color: #94a3b8; letter-spacing: 0.08em; text-transform: uppercase; margin: 0 0 6px 0;">
                    PILIH PORTAL LOGIN:
                </p>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; padding: 3px; background: #f1f5f9; border-radius: 10px;">
                    <a href="{{ route('login.role', 'admin') }}" style="padding: 5px 3px; border-radius: 7px; font-size: 10.5px; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 4px; {{ $role === 'admin' ? 'background: #ffffff; color: #4f46e5; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: {{ $role === 'admin' ? '#4f46e5' : '#cbd5e1' }};"></span> Admin
                    </a>
                    <a href="{{ route('login.role', 'teacher') }}" style="padding: 5px 3px; border-radius: 7px; font-size: 10.5px; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 4px; {{ $role === 'teacher' ? 'background: #ffffff; color: #059669; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: {{ $role === 'teacher' ? '#059669' : '#cbd5e1' }};"></span> Guru
                    </a>
                    <a href="{{ route('login.role', 'principal') }}" style="padding: 5px 3px; border-radius: 7px; font-size: 10.5px; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 4px; {{ $role === 'principal' ? 'background: #ffffff; color: #d97706; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: {{ $role === 'principal' ? '#d97706' : '#cbd5e1' }};"></span> Kepsek
                    </a>
                    <a href="{{ route('login.role', 'parent') }}" style="padding: 5px 3px; border-radius: 7px; font-size: 10.5px; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 4px; {{ $role === 'parent' ? 'background: #ffffff; color: #0284c7; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: {{ $role === 'parent' ? '#0284c7' : '#cbd5e1' }};"></span> Ortu
                    </a>
                </div>
            </div>

        </div>

        <!-- FOOTER COPYRIGHT -->
        <div style="background: #f8fafc; padding: 8px; text-align: center; font-size: 9.5px; font-weight: 600; color: #94a3b8; border-top: 1px solid #f1f5f9;">
            &copy; {{ date('Y') }} SMA XYZ JAKARTA. All rights reserved.
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Slider background (JS biasa, tanpa Alpine)
            const slides = document.querySelectorAll("#bg-slider .bg-slide");
            if (slides.length > 1) {
                let current = 0;
                setInterval(function () {
                    slides[current].style.opacity = 0;
                    current = (current + 1) % slides.length;
                    slides[current].style.opacity = 1;
                }, 5000);
            }

            // Pembersih autofill email pada input nomor HP
            const phoneInput = document.getElementById("phone");
            if (phoneInput) {
                if (phoneInput.value.includes("@")) {
                    phoneInput.value = "";
                }
                phoneInput.addEventListener("input", function () {
                    if (this.value.includes("@")) {
                        this.value = "";
                    }
                });
            }
        });
    </script>
</body>
</html>