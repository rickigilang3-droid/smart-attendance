<?php
    $userRoleName = Auth::user()->role->name ?? Auth::user()->role ?? 'admin';
    $role = strtolower((string) $userRoleName);
    $isAdmin     = in_array($role, ['admin', 'administrator', '1']);
    $isTeacher   = in_array($role, ['guru', 'teacher', '2']);
    $isParent    = in_array($role, ['parent', 'ortu', 'orang tua', '3']);
    $isPrincipal = in_array($role, ['principal', 'kepala sekolah', 'kepsek', '4']);

    // Dashboard sesuai role (dipakai oleh logo)
    $dashboardRoute = $isTeacher ? 'teacher.dashboard' : ($isParent ? 'parent.dashboard' : ($isPrincipal ? 'principal.dashboard' : 'admin.dashboard'));
    $dashboardUrl = Route::has($dashboardRoute) ? route($dashboardRoute) : url('/');

    // Route rekap guru
    $teacherRecapRoute = Route::has('teacher.class-attendance.recap')
        ? 'teacher.class-attendance.recap'
        : (Route::has('teacher.attendance.recap') ? 'teacher.attendance.recap' : 'teacher.reports.index');

    // Tema warna per role (disamakan dengan halaman login)
    $theme = match (true) {
        $isTeacher   => ['label' => 'Guru & Wali Kelas',   'a' => '#059669', 'b' => '#0d9488', 'soft' => '#ecfdf5', 'dark' => '#064e3b'],
        $isParent    => ['label' => 'Orang Tua / Wali',    'a' => '#0284c7', 'b' => '#06b6d4', 'soft' => '#f0f9ff', 'dark' => '#0c4a6e'],
        $isPrincipal => ['label' => 'Kepala Sekolah',      'a' => '#d97706', 'b' => '#ea580c', 'soft' => '#fffbeb', 'dark' => '#78350f'],
        default      => ['label' => 'Administrator',       'a' => '#4f46e5', 'b' => '#7c3aed', 'soft' => '#eef2ff', 'dark' => '#312e81'],
    };

    // Definisi menu: route, pola active, ikon, label
    $menus = [];
    $panelTitle = '';
    if ($isAdmin) {
        $panelTitle = 'Panel Administrator';
        $menus = [
            ['route' => 'admin.dashboard',                   'active' => ['admin.dashboard'],                  'icon' => '🏠',   'label' => 'Dashboard Utama'],
            ['route' => 'admin.classes.index',               'active' => ['admin.classes.*'],                  'icon' => '🏫',   'label' => 'Kelas & Tingkat'],
            ['route' => 'admin.teachers.index',              'active' => ['admin.teachers.*'],                 'icon' => '👨‍🏫', 'label' => 'Data Guru & Pengajar'],
            ['route' => 'admin.students.index',              'active' => ['admin.students.*'],                 'icon' => '👥',   'label' => 'Data Siswa'],
            ['route' => 'admin.organization-structure.index','active' => ['admin.organization-structure.*'],   'icon' => '🏛️',  'label' => 'Struktur Organisasi'],
            ['route' => 'admin.schedules.index',             'active' => ['admin.schedules.*'],                'icon' => '📅',   'label' => 'Jadwal Pelajaran'],
            ['route' => 'admin.announcements.index',         'active' => ['admin.announcements.*'],            'icon' => '📢',   'label' => 'Pengumuman Sekolah'],
            ['route' => 'admin.reports.index',               'active' => ['admin.reports.*'],                  'icon' => '📊',   'label' => 'Rekap & Laporan Kehadiran'],
            ['route' => 'admin.report-cards.index',          'active' => ['admin.report-cards.*'],             'icon' => '📄',   'label' => 'Cetak Rapor Siswa'],
        ];
    } elseif ($isTeacher) {
        $panelTitle = 'Panel Guru & Wali Kelas';
        $menus = [
            ['route' => 'teacher.dashboard',               'active' => ['teacher.dashboard'],                'icon' => '🏠', 'label' => 'Dashboard Guru'],
            ['route' => 'teacher.class-attendance.index',  'active' => ['teacher.class-attendance.index'],   'icon' => '🔓', 'label' => 'Buka Kelas & Absen'],
            ['route' => 'teacher.grades.index',            'active' => ['teacher.grades.*'],                 'icon' => '📝', 'label' => 'Input Nilai Siswa'],
            ['route' => 'teacher.permissions.index',       'active' => ['teacher.permissions.*'],            'icon' => '📩', 'label' => 'Izin & Sakit Siswa'],
            ['route' => 'teacher.schedules.index',         'active' => ['teacher.schedules.*'],              'icon' => '📅', 'label' => 'Jadwal Mengajar'],
            ['route' => 'teacher.students.index',          'active' => ['teacher.students.*'],               'icon' => '👥', 'label' => 'Data Siswa & Wali Kelas'],
            ['route' => $teacherRecapRoute,                'active' => ['teacher.class-attendance.recap', 'teacher.attendance.recap', 'teacher.reports.*'], 'icon' => '📄', 'label' => 'Jurnal & Rekap KBM'],
            ['route' => 'teacher.announcements.index',     'active' => ['teacher.announcements.*'],          'icon' => '📢', 'label' => 'Pengumuman Sekolah'],
        ];
    } elseif ($isParent) {
        $panelTitle = 'Panel Orang Tua / Wali';
        $menus = [
            ['route' => 'parent.dashboard',           'active' => ['parent.dashboard'],        'icon' => '🏠', 'label' => 'Dashboard Utama'],
            ['route' => 'parent.gate-logs.index',     'active' => ['parent.gate-logs.*'],      'icon' => '⏱️', 'label' => 'Monitoring Tap Gerbang'],
            ['route' => 'parent.permissions.index',   'active' => ['parent.permissions.*'],    'icon' => '✉️', 'label' => 'Pengajuan Izin / Sakit'],
            ['route' => 'parent.reports.index',       'active' => ['parent.reports.*'],        'icon' => '📊', 'label' => 'Rekap Kehadiran Anak'],
            ['route' => 'parent.announcements.index', 'active' => ['parent.announcements.*'],  'icon' => '📢', 'label' => 'Pengumuman Sekolah'],
        ];
    } elseif ($isPrincipal) {
        $panelTitle = 'Panel Kepala Sekolah';
        $menus = [
            ['route' => 'principal.dashboard',      'active' => ['principal.dashboard'],   'icon' => '🏠', 'label' => 'Dashboard Supervisi'],
            ['route' => 'principal.kbm.index',      'active' => ['principal.kbm.*'],       'icon' => '🏫', 'label' => 'Monitoring KBM Real-time'],
            ['route' => 'principal.teachers.index', 'active' => ['principal.teachers.*'],  'icon' => '👨‍🏫', 'label' => 'Kedisiplinan Guru'],
            ['route' => 'principal.reports.index',  'active' => ['principal.reports.*'],   'icon' => '📊', 'label' => 'Laporan & Rekapitulasi'],
        ];
    }

    // Warna ikon berselang-seling agar menu tampak hidup
    $tileColors = ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#0ea5e9', '#8b5cf6', '#f97316', '#14b8a6', '#ef4444'];
    $userName = Auth::user()->name ?? 'User';
    $logoUrl  = asset('storage/logo-sekolah/logo-sekolah.jpg');
    $logoFallback = 'https://cdn-icons-png.flaticon.com/512/2991/2991148.png';
?>


<style>
    [x-cloak] { display: none !important; }

    :root {
        --sb-a: <?php echo e($theme['a']); ?>;
        --sb-b: <?php echo e($theme['b']); ?>;
        --sb-soft: <?php echo e($theme['soft']); ?>;
        --sb-dark: <?php echo e($theme['dark']); ?>;
    }

    /* ---------- TOP BAR ---------- */
    .sb-topbar { position: sticky; top: 0; z-index: 40; background: rgba(255,255,255,.92); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; }
    .sb-topbar::after { content: ""; position: absolute; left: 0; right: 0; bottom: -1px; height: 3px; background: linear-gradient(90deg, var(--sb-a), var(--sb-b)); }
    .sb-topbar-in { max-width: 80rem; margin: 0 auto; padding: 0 16px; height: 64px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .sb-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .sb-burger { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; color: #334155; font: 700 12px/1 inherit; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,.06); transition: all .2s; }
    .sb-burger:hover { background: var(--sb-soft); color: var(--sb-a); border-color: var(--sb-a); transform: translateY(-1px); }
    .sb-burger svg { width: 20px; height: 20px; }
    .sb-burger span { display: none; }
    @media (min-width: 640px) { .sb-burger span { display: inline; } }
    .sb-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; min-width: 0; }
    .sb-brand img { width: 34px; height: 34px; object-fit: contain; border-radius: 10px; background: #fff; padding: 2px; box-shadow: 0 2px 6px rgba(0,0,0,.12); }
    .sb-brand b { font-size: 14px; font-weight: 900; letter-spacing: .04em; text-transform: uppercase; color: var(--sb-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sb-userbtn { display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px 6px 6px; border-radius: 999px; border: 1px solid #e2e8f0; background: #f8fafc; font: 700 12px/1 inherit; color: #334155; cursor: pointer; transition: background .2s; }
    .sb-userbtn:hover { background: #f1f5f9; }
    .sb-mini-avatar { width: 26px; height: 26px; border-radius: 999px; background: linear-gradient(135deg, var(--sb-a), var(--sb-b)); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; }

    /* ---------- DRAWER ---------- */
    .sb-root { position: fixed; inset: 0; z-index: 99999; }
    .sb-backdrop { position: absolute; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(3px); }
    .sb-panel { position: absolute; top: 0; bottom: 0; left: 0; width: min(330px, 88vw); background: #f8fafc; display: flex; flex-direction: column; box-shadow: 0 25px 60px rgba(0,0,0,.45); border-radius: 0 24px 24px 0; overflow: hidden; font-family: inherit; }

    .sb-fade { transition: opacity .3s ease; }
    .sb-o0 { opacity: 0; }
    .sb-o1 { opacity: 1; }
    .sb-slide { transition: transform .3s cubic-bezier(.4,0,.2,1); }
    .sb-off { transform: translateX(-100%); }
    .sb-on { transform: translateX(0); }

    /* Header berwarna */
    .sb-head { position: relative; padding: 22px 20px 20px; color: #fff; background: linear-gradient(135deg, var(--sb-dark) 0%, var(--sb-a) 60%, var(--sb-b) 100%); overflow: hidden; }
    .sb-head::before { content: ""; position: absolute; width: 180px; height: 180px; right: -50px; top: -60px; border-radius: 50%; background: rgba(255,255,255,.12); }
    .sb-head::after { content: ""; position: absolute; width: 110px; height: 110px; left: -30px; bottom: -50px; border-radius: 50%; background: rgba(255,255,255,.08); }
    .sb-head > * { position: relative; z-index: 1; }
    .sb-head-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .sb-head-brand { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .sb-head-logo { width: 46px; height: 46px; border-radius: 14px; background: #fff; padding: 4px; object-fit: contain; box-shadow: 0 6px 14px rgba(0,0,0,.25); flex-shrink: 0; }
    .sb-head-title { margin: 0; font-size: 12px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; line-height: 1.3; color: #fff; }
    .sb-head-sub { margin: 2px 0 0; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: rgba(255,255,255,.8); }
    .sb-close { width: 34px; height: 34px; border-radius: 12px; border: 1px solid rgba(255,255,255,.3); background: rgba(255,255,255,.15); color: #fff; font-size: 14px; cursor: pointer; transition: background .2s, transform .2s; flex-shrink: 0; }
    .sb-close:hover { background: rgba(255,255,255,.3); transform: rotate(90deg); }

    /* Kartu profil */
    .sb-profile { margin-top: 18px; display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 16px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.25); backdrop-filter: blur(6px); }
    .sb-avatar { width: 42px; height: 42px; border-radius: 14px; background: #fff; color: var(--sb-a); display: flex; align-items: center; justify-content: center; font-size: 17px; font-weight: 900; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,.2); }
    .sb-pname { margin: 0; font-size: 13px; font-weight: 800; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sb-prole { display: inline-block; margin-top: 4px; padding: 2px 9px; border-radius: 999px; background: rgba(255,255,255,.22); color: #fff; font-size: 10px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
    .sb-pinfo { min-width: 0; }

    /* Daftar menu */
    .sb-nav { flex: 1; overflow-y: auto; padding: 16px 14px 8px; }
    .sb-nav::-webkit-scrollbar { width: 6px; }
    .sb-nav::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    .sb-section { display: flex; align-items: center; gap: 8px; padding: 0 8px 10px; font-size: 10px; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; color: #94a3b8; }
    .sb-section::after { content: ""; flex: 1; height: 1px; background: #e2e8f0; }

    .sb-item { position: relative; display: flex; align-items: center; gap: 12px; padding: 9px 10px; margin-bottom: 4px; border-radius: 14px; text-decoration: none; color: #475569; font-size: 13px; font-weight: 700; border: 1px solid transparent; transition: all .18s ease; }
    .sb-item:hover { background: #fff; color: #0f172a; border-color: #e2e8f0; transform: translateX(3px); box-shadow: 0 4px 12px rgba(15,23,42,.06); }
    .sb-tile { width: 36px; height: 36px; border-radius: 11px; display: inline-flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; background: color-mix(in srgb, var(--c) 15%, #fff); box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--c) 25%, #fff); transition: transform .18s, background .18s; }
    .sb-item:hover .sb-tile { transform: scale(1.08) rotate(-4deg); }
    .sb-label { flex: 1; line-height: 1.25; }
    .sb-chev { font-size: 16px; color: #cbd5e1; transition: transform .18s, color .18s; }
    .sb-item:hover .sb-chev { transform: translateX(2px); color: var(--sb-a); }

    .sb-item.is-active { background: #fff; color: var(--sb-a); border-color: color-mix(in srgb, var(--sb-a) 30%, #fff); box-shadow: 0 6px 16px color-mix(in srgb, var(--sb-a) 22%, transparent); }
    .sb-item.is-active::before { content: ""; position: absolute; left: -14px; top: 18%; bottom: 18%; width: 5px; border-radius: 0 6px 6px 0; background: linear-gradient(180deg, var(--sb-a), var(--sb-b)); }
    .sb-item.is-active .sb-tile { background: linear-gradient(135deg, var(--sb-a), var(--sb-b)); box-shadow: 0 4px 10px color-mix(in srgb, var(--sb-a) 40%, transparent); }
    .sb-item.is-active .sb-chev { color: var(--sb-a); }

    .sb-item.is-disabled { opacity: .45; cursor: not-allowed; }
    .sb-item.is-disabled:hover { background: transparent; border-color: transparent; transform: none; box-shadow: none; }

    /* Tombol simulator */
    .sb-gate { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin: 12px 0 6px; padding: 12px 14px; border-radius: 16px; text-decoration: none; color: #fff; font-size: 13px; font-weight: 800; background: linear-gradient(135deg, var(--sb-a), var(--sb-b)); box-shadow: 0 8px 18px color-mix(in srgb, var(--sb-a) 40%, transparent); transition: transform .18s, box-shadow .18s; }
    .sb-gate:hover { transform: translateY(-2px); box-shadow: 0 12px 24px color-mix(in srgb, var(--sb-a) 50%, transparent); }
    .sb-gate-l { display: flex; align-items: center; gap: 10px; }
    .sb-gate-ic { width: 32px; height: 32px; border-radius: 10px; background: rgba(255,255,255,.22); display: inline-flex; align-items: center; justify-content: center; }
    .sb-live { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 999px; background: rgba(255,255,255,.25); font: 800 9px/1 ui-monospace, monospace; letter-spacing: .08em; }
    .sb-live i { width: 6px; height: 6px; border-radius: 50%; background: #4ade80; animation: sbPulse 1.4s infinite; }
    @keyframes sbPulse { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: .4; transform: scale(1.5); } }

    /* Footer */
    .sb-foot { padding: 14px; background: #fff; border-top: 1px solid #e2e8f0; }
    .sb-logout { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 11px; border-radius: 14px; border: 1px solid #fecdd3; background: #fff1f2; color: #e11d48; font: 800 12px/1 inherit; letter-spacing: .04em; text-transform: uppercase; cursor: pointer; transition: all .18s; }
    .sb-logout:hover { background: #e11d48; color: #fff; border-color: #e11d48; box-shadow: 0 6px 16px rgba(225,29,72,.35); }
    .sb-copy { margin: 10px 0 0; text-align: center; font-size: 10px; font-weight: 600; color: #94a3b8; }
</style>

<nav x-data="{ sidebarOpen: false }"
     x-effect="document.body.style.overflow = sidebarOpen ? 'hidden' : ''"
     class="sb-topbar">

    <!-- ================= TOP BAR ================= -->
    <div class="sb-topbar-in">
        <div class="sb-left">
            <button @click="sidebarOpen = true" type="button" class="sb-burger" title="Buka Menu Navigasi">
                <svg stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>Menu</span>
            </button>

            <a href="<?php echo e($dashboardUrl); ?>" class="sb-brand">
                <img src="<?php echo e($logoUrl); ?>" onerror="this.onerror=null; this.src='<?php echo e($logoFallback); ?>';" alt="Logo SMA XYZ JAKARTA">
                <b>SMA XYZ JAKARTA</b>
            </a>
        </div>

        <!-- Dropdown User -->
        <div>
            <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                 <?php $__env->slot('trigger', null, []); ?> 
                    <button class="sb-userbtn" type="button">
                        <span class="sb-mini-avatar"><?php echo e(strtoupper(substr($userName, 0, 1))); ?></span>
                        <span><?php echo e($userName); ?></span>
                        <svg style="width:16px;height:16px;fill:#94a3b8;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                 <?php $__env->endSlot(); ?>
                 <?php $__env->slot('content', null, []); ?> 
                    <div style="padding: 8px 16px; border-bottom: 1px solid #f1f5f9; font-size: 11px; color: #94a3b8;">
                        Logged in as
                        <span style="display:block; font-weight:700; color:#334155;"><?php echo e(Auth::user()->email ?? ''); ?></span>
                    </div>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault(); this.closest(\'form\').submit();','style' => 'font-size:12px; font-weight:700; color:#e11d48;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault(); this.closest(\'form\').submit();','style' => 'font-size:12px; font-weight:700; color:#e11d48;']); ?>
                            Log Out 🚪
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                    </form>
                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
        </div>
    </div>

    <!-- ================= SIDEBAR DRAWER (dipindah ke <body> agar selalu di atas semua konten) ================= -->
    <template x-teleport="body">
        <div x-show="sidebarOpen" x-cloak class="sb-root" role="dialog" aria-modal="true"
             @keydown.escape.window="sidebarOpen = false">

            <!-- Backdrop -->
            <div class="sb-backdrop"
                 x-show="sidebarOpen"
                 x-transition:enter="sb-fade" x-transition:enter-start="sb-o0" x-transition:enter-end="sb-o1"
                 x-transition:leave="sb-fade" x-transition:leave-start="sb-o1" x-transition:leave-end="sb-o0"
                 @click="sidebarOpen = false"></div>

            <!-- Panel -->
            <aside class="sb-panel"
                   x-show="sidebarOpen"
                   @click.stop
                   x-transition:enter="sb-slide" x-transition:enter-start="sb-off" x-transition:enter-end="sb-on"
                   x-transition:leave="sb-slide" x-transition:leave-start="sb-on" x-transition:leave-end="sb-off">

                <!-- Header berwarna + profil -->
                <div class="sb-head">
                    <div class="sb-head-top">
                        <div class="sb-head-brand">
                            <img class="sb-head-logo" src="<?php echo e($logoUrl); ?>" onerror="this.onerror=null; this.src='<?php echo e($logoFallback); ?>';" alt="Logo">
                            <div>
                                <h2 class="sb-head-title">Portal Sekolah Pintar</h2>
                                <p class="sb-head-sub">SMA XYZ Jakarta</p>
                            </div>
                        </div>
                        <button @click="sidebarOpen = false" type="button" class="sb-close" title="Tutup">✕</button>
                    </div>

                    <div class="sb-profile">
                        <div class="sb-avatar"><?php echo e(strtoupper(substr($userName, 0, 1))); ?></div>
                        <div class="sb-pinfo">
                            <p class="sb-pname"><?php echo e($userName); ?></p>
                            <span class="sb-prole"><?php echo e($theme['label']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Daftar menu -->
                <div class="sb-nav">
                    <div class="sb-section"><?php echo e($panelTitle); ?></div>

                    <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $url = Route::has($menu['route']) ? route($menu['route']) : null;
                            $isActive = request()->routeIs(...$menu['active']);
                            $c = $tileColors[$i % count($tileColors)];
                        ?>
                        <?php if($url): ?>
                            <a href="<?php echo e($url); ?>" class="sb-item <?php echo e($isActive ? 'is-active' : ''); ?>" style="--c: <?php echo e($c); ?>;">
                                <span class="sb-tile"><?php echo e($menu['icon']); ?></span>
                                <span class="sb-label"><?php echo e($menu['label']); ?></span>
                                <span class="sb-chev">›</span>
                            </a>
                        <?php else: ?>
                            <span class="sb-item is-disabled" style="--c: <?php echo e($c); ?>;" title="Halaman belum tersedia">
                                <span class="sb-tile"><?php echo e($menu['icon']); ?></span>
                                <span class="sb-label"><?php echo e($menu['label']); ?></span>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if($isAdmin && Route::has('admin.gate-attendance.index')): ?>
                        <a href="<?php echo e(route('admin.gate-attendance.index')); ?>" class="sb-gate">
                            <span class="sb-gate-l">
                                <span class="sb-gate-ic">👆</span>
                                Smart Gate Simulator
                            </span>
                            <span class="sb-live"><i></i>LIVE</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Footer -->
                <div class="sb-foot">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="sb-logout">🚪 Keluar dari Sistem</button>
                    </form>
                    <p class="sb-copy">&copy; <?php echo e(date('Y')); ?> SMA XYZ JAKARTA</p>
                </div>
            </aside>
        </div>
    </template>
</nav><?php /**PATH C:\laragon\www\smart-attendance\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>