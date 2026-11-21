<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Smart School') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $role = strtolower(request()->route('role') ?? 'guest');
        
        // Penentuan Gradasi Background Layar Penuh berdasarkan Role
        $bgClass = match($role) {
            'teacher'   => 'bg-gradient-to-br from-emerald-100 via-teal-50 to-emerald-200',
            'admin'     => 'bg-gradient-to-br from-indigo-100 via-purple-50 to-indigo-200',
            'principal' => 'bg-gradient-to-br from-amber-100 via-orange-50 to-amber-200',
            'parent'    => 'bg-gradient-to-br from-sky-100 via-blue-50 to-cyan-200',
            default     => 'bg-gradient-to-br from-slate-100 via-gray-50 to-slate-200',
        };
    @endphp
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 {{ $bgClass }} transition-all duration-500 py-10 px-4">
            <!-- Logo Sekolah / Tut Wuri Handayani -->
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <!-- Card Box Login -->
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white/95 backdrop-blur-md shadow-xl overflow-hidden sm:rounded-3xl border border-white/60">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>