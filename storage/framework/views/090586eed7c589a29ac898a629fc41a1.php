<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    <style>
        .ad-page {
            --a: #4f46e5; --b: #7c3aed; --dark: #312e81;
            min-height: 100vh; padding: 20px 16px 40px; font-family: inherit;
            background:
                radial-gradient(900px 400px at 0% 0%, rgba(99,102,241,.14), transparent 60%),
                radial-gradient(700px 400px at 100% 10%, rgba(124,58,237,.12), transparent 60%),
                #f1f5f9;
        }
        @media (min-width: 640px)  { .ad-page { padding: 28px 24px 48px; } }
        @media (min-width: 1024px) { .ad-page { padding: 32px 32px 56px; } }
        .ad-wrap { max-width: 80rem; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

        /* ---------- HERO ---------- */
        .ad-hero { position: relative; overflow: hidden; border-radius: 28px; padding: 28px; color: #fff;
            background: linear-gradient(120deg, #312e81 0%, #4f46e5 50%, #7c3aed 100%);
            box-shadow: 0 20px 40px -12px rgba(79,70,229,.5);
            display: flex; flex-direction: column; gap: 20px; }
        @media (min-width: 768px) { .ad-hero { flex-direction: row; align-items: center; justify-content: space-between; padding: 32px 36px; } }
        .ad-hero::before { content: ""; position: absolute; width: 320px; height: 320px; right: -80px; top: -140px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .ad-hero::after { content: ""; position: absolute; width: 200px; height: 200px; left: 30%; bottom: -130px; border-radius: 50%; background: rgba(255,255,255,.08); }
        .ad-hero > * { position: relative; z-index: 1; }
        .ad-eyebrow { display: inline-flex; align-items: center; gap: 8px; padding: 5px 12px; border-radius: 999px; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.28); font-size: 11px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .ad-eyebrow i { width: 8px; height: 8px; border-radius: 50%; background: #4ade80; box-shadow: 0 0 0 0 rgba(74,222,128,.7); animation: adPing 1.6s infinite; }
        @keyframes adPing { 0% { box-shadow: 0 0 0 0 rgba(74,222,128,.7); } 70% { box-shadow: 0 0 0 9px rgba(74,222,128,0); } 100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); } }
        .ad-hero h1 { margin: 12px 0 6px; font-size: 28px; font-weight: 900; line-height: 1.15; letter-spacing: -.01em; color: #fff; }
        @media (min-width: 640px) { .ad-hero h1 { font-size: 34px; } }
        .ad-hero p { margin: 0; max-width: 520px; font-size: 13px; line-height: 1.6; color: rgba(255,255,255,.82); }
        .ad-date { display: inline-block; margin-top: 12px; font-size: 12px; font-weight: 700; color: rgba(255,255,255,.9); }
        .ad-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .ad-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 18px; border-radius: 14px; font-size: 12px; font-weight: 800; text-decoration: none; transition: transform .18s, box-shadow .18s, background .18s; }
        .ad-btn:hover { transform: translateY(-2px); }
        .ad-btn-glass { background: rgba(255,255,255,.16); color: #fff; border: 1px solid rgba(255,255,255,.35); backdrop-filter: blur(6px); }
        .ad-btn-glass:hover { background: rgba(255,255,255,.28); }
        .ad-btn-white { background: #fff; color: var(--a); box-shadow: 0 8px 20px rgba(0,0,0,.2); }
        .ad-btn-white:hover { box-shadow: 0 12px 26px rgba(0,0,0,.28); }

        /* ---------- STAT CARDS ---------- */
        .ad-stats { display: grid; grid-template-columns: 1fr; gap: 18px; }
        @media (min-width: 640px)  { .ad-stats { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .ad-stats { grid-template-columns: repeat(4, 1fr); gap: 22px; } }
        .ad-stat { position: relative; overflow: hidden; background: #fff; border-radius: 22px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(15,23,42,.05); transition: transform .2s, box-shadow .2s; }
        .ad-stat:hover { transform: translateY(-4px); box-shadow: 0 16px 30px color-mix(in srgb, var(--c) 22%, transparent); }
        .ad-stat::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 4px; background: linear-gradient(90deg, var(--c), color-mix(in srgb, var(--c) 55%, #fff)); }
        .ad-stat::after { content: ""; position: absolute; width: 120px; height: 120px; right: -40px; bottom: -50px; border-radius: 50%; background: color-mix(in srgb, var(--c) 9%, transparent); }
        .ad-stat-top { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; }
        .ad-stat-label { font-size: 11px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: #64748b; }
        .ad-ico { width: 44px; height: 44px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 21px; background: linear-gradient(135deg, var(--c), color-mix(in srgb, var(--c) 60%, #fff)); box-shadow: 0 8px 16px color-mix(in srgb, var(--c) 35%, transparent); }
        .ad-stat-val { position: relative; z-index: 1; margin-top: 14px; font-size: 32px; font-weight: 900; line-height: 1; color: #0f172a; letter-spacing: -.02em; }
        .ad-stat-val small { font-size: 16px; font-weight: 800; color: var(--c); margin-left: 4px; }
        .ad-stat-note { position: relative; z-index: 1; display: inline-block; margin: 12px 0 0; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; color: var(--c); background: color-mix(in srgb, var(--c) 11%, #fff); }

        /* ---------- MAIN GRID ---------- */
        .ad-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
        @media (min-width: 1024px) { .ad-grid { grid-template-columns: 2fr 1fr; } }
        .ad-card { background: #fff; border-radius: 26px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 6px 20px rgba(15,23,42,.05); display: flex; flex-direction: column; gap: 18px; }
        .ad-card-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .ad-title { margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; }
        .ad-sub { margin: 4px 0 0; font-size: 12px; color: #64748b; }
        .ad-legend { display: flex; gap: 8px; flex-wrap: wrap; }
        .ad-chip { display: inline-flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 999px; font-size: 11px; font-weight: 800; }
        .ad-chip i { width: 10px; height: 10px; border-radius: 4px; display: inline-block; }
        .ad-chip.ok { background: #ecfdf5; color: #047857; } .ad-chip.ok i { background: #10b981; }
        .ad-chip.warn { background: #fffbeb; color: #b45309; } .ad-chip.warn i { background: #f59e0b; }
        .ad-chart { position: relative; height: 320px; padding: 12px; border-radius: 20px; background: linear-gradient(180deg, #f8fafc, #fff); border: 1px solid #f1f5f9; }

        .ad-live { padding: 5px 12px; border-radius: 999px; font-size: 10px; font-weight: 900; letter-spacing: .1em; color: #fff; background: linear-gradient(135deg, var(--a), var(--b)); box-shadow: 0 4px 10px rgba(79,70,229,.4); animation: adBlink 2s infinite; }
        @keyframes adBlink { 0%,100% { opacity: 1; } 50% { opacity: .65; } }

        /* ---------- LOG ---------- */
        .ad-log { display: flex; flex-direction: column; gap: 10px; max-height: 24rem; overflow-y: auto; padding-right: 4px; }
        .ad-log::-webkit-scrollbar { width: 6px; }
        .ad-log::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .ad-item { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 14px; border-radius: 18px; background: #f8fafc; border: 1px solid #e2e8f0; border-left: 5px solid var(--s); transition: transform .18s, box-shadow .18s, background .18s; }
        .ad-item:hover { background: #fff; transform: translateX(3px); box-shadow: 0 8px 18px rgba(15,23,42,.08); }
        .ad-item-l { display: flex; align-items: flex-start; gap: 12px; min-width: 0; }
        .ad-dot { width: 12px; height: 12px; margin-top: 4px; border-radius: 50%; flex-shrink: 0; background: var(--s); box-shadow: 0 0 0 4px color-mix(in srgb, var(--s) 20%, transparent); }
        .ad-name-row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .ad-name { margin: 0; font-size: 13px; font-weight: 800; color: #0f172a; }
        .ad-role { padding: 2px 7px; border-radius: 6px; font-size: 9px; font-weight: 800; }
        .ad-role.guru { background: #f3e8ff; color: #7e22ce; }
        .ad-role.siswa { background: #dbeafe; color: #1d4ed8; }
        .ad-detail { margin: 2px 0 0; font-size: 11px; font-weight: 500; color: #64748b; }
        .ad-times { display: flex; align-items: center; gap: 6px; margin-top: 6px; flex-wrap: wrap; }
        .ad-time { padding: 2px 7px; border-radius: 6px; border: 1px solid; font-size: 10.5px; font-weight: 700; }
        .ad-time.in-ok { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
        .ad-time.in-late { color: #be123c; background: #fff1f2; border-color: #fecdd3; }
        .ad-time.out-ok { color: #b45309; background: #fffbeb; border-color: #fde68a; }
        .ad-time.out-no { color: #94a3b8; background: #f1f5f9; border-color: #e2e8f0; }
        .ad-badge { flex-shrink: 0; padding: 5px 11px; border-radius: 10px; font-size: 10px; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
        .ad-badge.pulang { background: #fef3c7; color: #92400e; }
        .ad-badge.late { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
        .ad-badge.masuk { background: #d1fae5; color: #065f46; }
        .ad-empty { padding: 36px 16px; text-align: center; font-size: 12px; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 20px; background: #f8fafc; }
        .ad-empty b { display: block; font-size: 30px; margin-bottom: 6px; }
    </style>

    <div class="ad-page">
        <div class="ad-wrap">

            
            <div class="ad-hero">
                <div>
                    <span class="ad-eyebrow"><i></i> Panel Administrator Utama</span>
                    <h1>Dashboard Administrator</h1>
                    <p>Kelola data master sekolah, pengguna, kelas, dan pantau rekapitulasi sistem hari ini.</p>
                    <span class="ad-date">📅 <?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d F Y')); ?></span>
                </div>
                <div class="ad-actions">
                    <a href="<?php echo e(route('admin.organization-structure.index')); ?>" class="ad-btn ad-btn-glass">
                        <span>🏛️ Struktur Organisasi</span>
                    </a>
                    <a href="<?php echo e(route('admin.gate-attendance.index')); ?>" class="ad-btn ad-btn-white">
                        <span>👆 Buka Gate Simulator</span>
                    </a>
                </div>
            </div>

            
            <div class="ad-stats">
                <div class="ad-stat" style="--c: #4f46e5;">
                    <div class="ad-stat-top">
                        <span class="ad-stat-label">Total Siswa</span>
                        <div class="ad-ico">👥</div>
                    </div>
                    <div class="ad-stat-val"><?php echo e(number_format($totalStudents ?? 0)); ?></div>
                    <p class="ad-stat-note">Terdaftar di <?php echo e($totalRombel ?? 0); ?> Rombel</p>
                </div>

                <div class="ad-stat" style="--c: #0284c7;">
                    <div class="ad-stat-top">
                        <span class="ad-stat-label">Guru & Staf</span>
                        <div class="ad-ico">👨‍🏫</div>
                    </div>
                    <div class="ad-stat-val"><?php echo e($totalTeachers ?? 0); ?><small>Guru</small></div>
                    <p class="ad-stat-note">Aktif Mengajar</p>
                </div>

                <div class="ad-stat" style="--c: #059669;">
                    <div class="ad-stat-top">
                        <span class="ad-stat-label">Kehadiran Hari Ini</span>
                        <div class="ad-ico">✅</div>
                    </div>
                    <div class="ad-stat-val"><?php echo e($attendancePercentage ?? 0); ?><small>%</small></div>
                    <p class="ad-stat-note"><?php echo e($presentStudents ?? 0); ?> Hadir (<?php echo e($totalLate ?? 0); ?> Terlambat)</p>
                </div>

                <div class="ad-stat" style="--c: #7c3aed;">
                    <div class="ad-stat-top">
                        <span class="ad-stat-label">Akun Terdaftar</span>
                        <div class="ad-ico">🔐</div>
                    </div>
                    <div class="ad-stat-val"><?php echo e(number_format($totalUsers ?? 0)); ?></div>
                    <p class="ad-stat-note">Admin, Guru, Ortu</p>
                </div>
            </div>

            
            <div class="ad-grid">

                
                <div class="ad-card">
                    <div class="ad-card-head">
                        <div>
                            <h2 class="ad-title">Trend Kehadiran Gerbang (7 Hari Terakhir) 📊</h2>
                            <p class="ad-sub">Perbandingan tepat waktu vs terlambat</p>
                        </div>
                        <div class="ad-legend">
                            <span class="ad-chip ok"><i></i> Tepat Waktu</span>
                            <span class="ad-chip warn"><i></i> Terlambat</span>
                        </div>
                    </div>
                    <div class="ad-chart">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>

                
                <div class="ad-card">
                    <div class="ad-card-head" style="align-items:center;">
                        <div>
                            <h2 class="ad-title">Tap Scan Terakhir 🕒</h2>
                            <p class="ad-sub">Aktivitas tap masuk/pulang hari ini</p>
                        </div>
                        <span class="ad-live">LIVE LOG</span>
                    </div>

                    <div class="ad-log">
                        <?php if(isset($recentScanLogs) && count($recentScanLogs) > 0): ?>
                            <?php $__currentLoopData = $recentScanLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isTeacher = !empty($log->teacher_id) || !empty($log->teacher);

                                    $personName = $isTeacher
                                        ? (optional($log->teacher)->name ?? 'Guru')
                                        : (optional($log->student)->name ?? 'Siswa');

                                    $personSubDetail = $isTeacher
                                        ? 'NIP: ' . (optional($log->teacher)->nip ?? '-')
                                        : 'NISN: ' . (optional($log->student)->nisn ?? '-') . ' • ' . (optional(optional($log->student)->schoolClass)->name ?? 'Tanpa Kelas');

                                    $timeIn = $log->scan_in ?? $log->time_in ?? ($log->created_at ? $log->created_at->format('H:i:s') : null);
                                    $hasUpdated = $log->updated_at && $log->created_at && $log->updated_at->ne($log->created_at);
                                    $timeOut = $log->scan_out ?? $log->time_out ?? ($hasUpdated ? $log->updated_at->format('H:i:s') : null);

                                    $isPulang = !empty($timeOut);

                                    $lateLimit = $isTeacher ? '07:00:00' : '07:15:00';
                                    $timeInFormatted = $timeIn ? \Carbon\Carbon::parse($timeIn)->format('H:i:s') : null;
                                    $statusDb = strtolower($log->status ?? '');

                                    $isLate = in_array($statusDb, ['terlambat', 'late', 'tl']) || ($timeInFormatted && $timeInFormatted > $lateLimit);

                                    $statusColor = $isPulang ? '#f59e0b' : ($isLate ? '#f43f5e' : '#10b981');
                                ?>
                                <div class="ad-item" style="--s: <?php echo e($statusColor); ?>;">
                                    <div class="ad-item-l">
                                        <div class="ad-dot"></div>

                                        <div>
                                            <div class="ad-name-row">
                                                <p class="ad-name"><?php echo e($personName); ?></p>
                                                <?php if($isTeacher): ?>
                                                    <span class="ad-role guru">Guru</span>
                                                <?php else: ?>
                                                    <span class="ad-role siswa">Siswa</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="ad-detail"><?php echo e($personSubDetail); ?></p>

                                            <div class="ad-times">
                                                <span class="ad-time <?php echo e($isLate ? 'in-late' : 'in-ok'); ?>">
                                                    In: <?php echo e($timeIn ? \Carbon\Carbon::parse($timeIn)->format('H:i:s') : '-'); ?> WIB
                                                </span>
                                                <span class="ad-time <?php echo e($isPulang ? 'out-ok' : 'out-no'); ?>">
                                                    Out: <?php echo e($timeOut ? \Carbon\Carbon::parse($timeOut)->format('H:i:s') : '-'); ?> WIB
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <?php if($isPulang): ?>
                                            <span class="ad-badge pulang">PULANG</span>
                                        <?php elseif($isLate): ?>
                                            <span class="ad-badge late">TERLAMBAT</span>
                                        <?php else: ?>
                                            <span class="ad-badge masuk">MASUK</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="ad-empty">
                                <b>🛰️</b>
                                Belum ada aktivitas tap gerbang hari ini.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');

        // Gradasi warna batang (hanya tampilan)
        const gradOk = ctx.createLinearGradient(0, 0, 0, 320);
        gradOk.addColorStop(0, '#34d399');
        gradOk.addColorStop(1, '#059669');
        const gradLate = ctx.createLinearGradient(0, 0, 0, 320);
        gradLate.addColorStop(0, '#fbbf24');
        gradLate.addColorStop(1, '#d97706');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartDates ?? [], 15, 512) ?>,
                datasets: [
                    {
                        label: 'Tepat Waktu',
                        data: <?php echo json_encode($chartOnTime ?? [], 15, 512) ?>,
                        backgroundColor: gradOk,
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 38,
                    },
                    {
                        label: 'Terlambat',
                        data: <?php echo json_encode($chartLate ?? [], 15, 512) ?>,
                        backgroundColor: gradLate,
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 38,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1b4b',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { weight: '700' },
                        boxPadding: 4
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#64748b', font: { weight: '600' } } },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#64748b' },
                        grid: { color: '#eef2ff' },
                        border: { display: false }
                    }
                }
            }
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\smart-attendance\resources\views/admin/index.blade.php ENDPATH**/ ?>