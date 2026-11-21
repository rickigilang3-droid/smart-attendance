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
        .pc-page {
            --a: #d97706; --b: #ea580c; --dark: #78350f;
            min-height: 100vh; padding: 20px 16px 40px; font-family: inherit;
            background:
                radial-gradient(900px 400px at 0% 0%, rgba(245,158,11,.16), transparent 60%),
                radial-gradient(700px 400px at 100% 10%, rgba(234,88,12,.12), transparent 60%),
                #f1f5f9;
        }
        @media (min-width: 640px)  { .pc-page { padding: 28px 24px 48px; } }
        @media (min-width: 1024px) { .pc-page { padding: 32px 32px 56px; } }
        .pc-wrap { max-width: 80rem; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

        /* ---------- HERO ---------- */
        .pc-hero { position: relative; overflow: hidden; border-radius: 28px; padding: 28px; color: #fff;
            background: linear-gradient(120deg, #78350f 0%, #d97706 55%, #ea580c 100%);
            box-shadow: 0 20px 40px -12px rgba(217,119,6,.5);
            display: flex; flex-direction: column; gap: 18px; }
        @media (min-width: 768px) { .pc-hero { flex-direction: row; align-items: center; justify-content: space-between; padding: 32px 36px; } }
        .pc-hero::before { content: ""; position: absolute; width: 320px; height: 320px; right: -80px; top: -140px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .pc-hero::after { content: ""; position: absolute; width: 200px; height: 200px; left: 38%; bottom: -130px; border-radius: 50%; background: rgba(255,255,255,.08); }
        .pc-hero > * { position: relative; z-index: 1; }
        .pc-eyebrow { display: inline-block; padding: 5px 14px; border-radius: 999px; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.35); font-size: 10.5px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
        .pc-hero h1 { margin: 12px 0 6px; font-size: 26px; font-weight: 900; line-height: 1.15; letter-spacing: -.01em; color: #fff; }
        @media (min-width: 640px) { .pc-hero h1 { font-size: 32px; } }
        .pc-hero p { margin: 0; max-width: 560px; font-size: 13px; line-height: 1.6; color: rgba(255,255,255,.88); }
        .pc-date { flex-shrink: 0; padding: 12px 20px; border-radius: 18px; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.35); backdrop-filter: blur(8px); font-size: 12px; font-weight: 700; color: rgba(255,255,255,.9); }
        .pc-date b { display: block; margin-top: 2px; font-size: 16px; font-weight: 900; color: #fff; }

        /* ---------- STAT CARDS ---------- */
        .pc-stats { display: grid; grid-template-columns: 1fr; gap: 18px; }
        @media (min-width: 640px)  { .pc-stats { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .pc-stats { grid-template-columns: repeat(4, 1fr); gap: 22px; } }
        .pc-stat { position: relative; overflow: hidden; background: #fff; border-radius: 22px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(15,23,42,.05); display: flex; flex-direction: column; gap: 10px; transition: transform .2s, box-shadow .2s; }
        .pc-stat:hover { transform: translateY(-4px); box-shadow: 0 16px 30px color-mix(in srgb, var(--c) 22%, transparent); }
        .pc-stat::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 4px; background: linear-gradient(90deg, var(--c), color-mix(in srgb, var(--c) 55%, #fff)); }
        .pc-stat::after { content: ""; position: absolute; width: 120px; height: 120px; right: -40px; bottom: -50px; border-radius: 50%; background: color-mix(in srgb, var(--c) 9%, transparent); }
        .pc-stat > * { position: relative; z-index: 1; }
        .pc-stat-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .pc-label { font-size: 11px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: #64748b; }
        .pc-ico { width: 44px; height: 44px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 21px; flex-shrink: 0; background: linear-gradient(135deg, var(--c), color-mix(in srgb, var(--c) 60%, #fff)); box-shadow: 0 8px 16px color-mix(in srgb, var(--c) 35%, transparent); }
        .pc-big { display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap; }
        .pc-big b { font-size: 32px; font-weight: 900; line-height: 1; letter-spacing: -.02em; color: #0f172a; }
        .pc-big span { font-size: 12px; font-weight: 700; color: #94a3b8; }
        .pc-note { margin: 0; font-size: 11.5px; font-weight: 500; color: #94a3b8; }
        .pc-bar { width: 100%; height: 10px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
        .pc-bar > div { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--c), color-mix(in srgb, var(--c) 55%, #fff)); transition: width .7s ease; }
        .pc-chips { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .pc-ch { padding: 5px 10px; border-radius: 10px; font-size: 11px; font-weight: 800; }
        .pc-ch.izin { background: #fef3c7; color: #92400e; }
        .pc-ch.sakit { background: #dbeafe; color: #1e40af; }
        .pc-ch.alpha { background: #ffe4e6; color: #9f1239; }

        /* ---------- CARD ---------- */
        .pc-card { background: #fff; border-radius: 26px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 6px 20px rgba(15,23,42,.05); display: flex; flex-direction: column; gap: 18px; }
        .pc-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; }
        .pc-title { margin: 0; display: flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 800; color: #0f172a; }
        .pc-title i { font-style: normal; width: 36px; height: 36px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 17px; background: linear-gradient(135deg, #fef3c7, #fed7aa); }
        .pc-sub { margin: 6px 0 0; font-size: 12px; color: #64748b; }
        .pc-pill { padding: 6px 14px; border-radius: 999px; font-size: 11px; font-weight: 900; letter-spacing: .04em; color: #fff; background: linear-gradient(135deg, var(--a), var(--b)); box-shadow: 0 4px 10px rgba(217,119,6,.35); display: inline-flex; align-items: center; gap: 7px; }
        .pc-pill i { width: 7px; height: 7px; border-radius: 50%; background: #fff; animation: pcBlink 1.4s infinite; }
        @keyframes pcBlink { 0%,100% { opacity: 1; } 50% { opacity: .3; } }
        .pc-pill.soft { background: #f1f5f9; color: #64748b; box-shadow: none; font-weight: 800; }

        /* ---------- TABLE ---------- */
        .pc-tw { overflow-x: auto; border-radius: 18px; border: 1px solid #e2e8f0; }
        .pc-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 12px; color: #475569; }
        .pc-table thead tr { background: linear-gradient(90deg, #fffbeb, #fff7ed); }
        .pc-table th { padding: 14px 16px; font-size: 10.5px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; color: var(--dark); border-bottom: 2px solid #fde68a; white-space: nowrap; }
        .pc-table td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .pc-table tbody tr { transition: background .15s; }
        .pc-table tbody tr:hover { background: #fffbeb; }
        .pc-table tbody tr:last-child td { border-bottom: none; }
        .pc-table .c { text-align: center; }
        .pc-table .st { min-width: 16rem; }
        .pc-table .empty { padding: 44px 16px; text-align: center; color: #94a3b8; }
        .pc-cls { font-weight: 800; color: #0f172a; font-size: 13px; }
        .pc-cls span { display: inline-block; margin-top: 4px; padding: 2px 9px; border-radius: 8px; background: #ffedd5; color: #9a3412; font-size: 10.5px; font-weight: 800; }
        .pc-tname { font-weight: 800; color: #0f172a; }
        .pc-nip { display: block; margin-top: 2px; font-size: 10px; color: #94a3b8; }
        .pc-topic { font-weight: 800; color: #0f172a; }
        .pc-sum { margin: 4px 0 0; font-size: 11px; line-height: 1.5; color: #64748b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

        .pc-ontime { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 12px; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 800; }
        .pc-late { padding: 10px 12px; border-radius: 16px; background: #fff1f2; border: 1px solid #fecdd3; border-left: 5px solid #f43f5e; text-align: left; display: flex; flex-direction: column; gap: 6px; }
        .pc-late-top { display: flex; align-items: center; justify-content: space-between; gap: 6px; }
        .pc-late-b { padding: 2px 9px; border-radius: 8px; background: #fecdd3; color: #881337; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; }
        .pc-late-t { font-size: 10px; font-weight: 800; color: #e11d48; }
        .pc-late-r { padding: 8px 10px; border-radius: 12px; background: rgba(255,255,255,.85); border: 1px solid #ffe4e6; font-size: 11px; font-weight: 500; font-style: italic; color: #9f1239; }
        .pc-late-n { font-size: 10px; font-style: italic; color: #be123c; }

        .pc-photo { display: inline-block; }
        .pc-photo img { width: 52px; height: 52px; object-fit: cover; border-radius: 14px; border: 3px solid #fff; box-shadow: 0 4px 12px rgba(15,23,42,.2); transition: transform .2s; }
        .pc-photo:hover img { transform: scale(1.12) rotate(-2deg); }
        .pc-nophoto { font-size: 10px; font-style: italic; color: #94a3b8; }

        /* ---------- GATE LOG ---------- */
        .pc-logs { display: grid; grid-template-columns: 1fr; gap: 12px; }
        @media (min-width: 768px) { .pc-logs { grid-template-columns: 1fr 1fr; } }
        .pc-log { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 14px; border-radius: 18px; background: #f8fafc; border: 1px solid #e2e8f0; border-left: 5px solid #10b981; transition: transform .18s, box-shadow .18s, background .18s; }
        .pc-log:hover { background: #fff; transform: translateX(3px); box-shadow: 0 8px 18px rgba(15,23,42,.08); }
        .pc-log-l { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .pc-log-av { width: 40px; height: 40px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; background: linear-gradient(135deg, #34d399, #059669); box-shadow: 0 6px 12px rgba(16,185,129,.3); }
        .pc-log h4 { margin: 0; font-size: 12.5px; font-weight: 800; color: #0f172a; }
        .pc-log p { margin: 2px 0 0; font-size: 10.5px; color: #64748b; }
        .pc-time { flex-shrink: 0; padding: 6px 12px; border-radius: 10px; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; font-size: 10.5px; font-weight: 800; font-family: ui-monospace, monospace; }
        .pc-empty { grid-column: 1 / -1; padding: 32px 16px; text-align: center; font-size: 12px; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 20px; background: #f8fafc; }
    </style>

    <div class="pc-page">
        <div class="pc-wrap">

            
            <div class="pc-hero">
                <div>
                    <span class="pc-eyebrow">Panel Kepala Sekolah</span>
                    <h1>🏛️ Dashboard Kepala Sekolah</h1>
                    <p>Pemantauan Kedisiplinan KBM, Presensi Siswa, dan Berita Acara Mengajar Guru secara Real-Time.</p>
                </div>
                <div class="pc-date">
                    Hari Ini
                    <b><?php echo e(\Carbon\Carbon::now()->isoFormat('D MMMM Y')); ?></b>
                </div>
            </div>

            
            <div class="pc-stats">
                
                <div class="pc-stat" style="--c: #10b981;">
                    <div class="pc-stat-top">
                        <span class="pc-label">Tingkat Kehadiran Siswa</span>
                        <span class="pc-ico">🎓</span>
                    </div>
                    <div class="pc-big">
                        <b><?php echo e($studentAttendanceRate ?? 0); ?>%</b>
                        <span>(<?php echo e($studentHadir ?? 0); ?>/<?php echo e($totalStudents ?? 0); ?> Siswa)</span>
                    </div>
                    <div class="pc-bar">
                        <div style="width: <?php echo e($studentAttendanceRate ?? 0); ?>%"></div>
                    </div>
                </div>

                
                <div class="pc-stat" style="--c: #f43f5e;">
                    <div class="pc-stat-top">
                        <span class="pc-label">Siswa Tidak Hadir</span>
                        <span class="pc-ico">⚠️</span>
                    </div>
                    <div class="pc-chips">
                        <span class="pc-ch izin">Izin: <?php echo e($recapToday['izin'] ?? $studentIzin ?? 0); ?></span>
                        <span class="pc-ch sakit">Sakit: <?php echo e($recapToday['sakit'] ?? $studentSakit ?? 0); ?></span>
                        <span class="pc-ch alpha">Alpha: <?php echo e($recapToday['alpha'] ?? $studentAlpha ?? 0); ?></span>
                    </div>
                    <p class="pc-note">Total tidak di kelas hari ini</p>
                </div>

                
                <div class="pc-stat" style="--c: #6366f1;">
                    <div class="pc-stat-top">
                        <span class="pc-label">Total Guru & Pengajar</span>
                        <span class="pc-ico">👨‍🏫</span>
                    </div>
                    <div class="pc-big"><b><?php echo e($totalTeachers ?? 0); ?></b></div>
                    <p class="pc-note">Terdaftar dalam sistem mengajar</p>
                </div>

                
                <div class="pc-stat" style="--c: #d97706;">
                    <div class="pc-stat-top">
                        <span class="pc-label">Jurnal KBM Terisi</span>
                        <span class="pc-ico">📝</span>
                    </div>
                    <div class="pc-big"><b><?php echo e(count($todayJournals ?? [])); ?> / <?php echo e($totalClasses ?? 0); ?></b></div>
                    <p class="pc-note">Kelas telah mengisi Berita Acara hari ini</p>
                </div>
            </div>

            
            <div class="pc-card">
                <div class="pc-card-head">
                    <div>
                        <h3 class="pc-title"><i>📖</i> Monitoring Berita Acara & Jurnal Mengajar Guru Hari Ini</h3>
                        <p class="pc-sub">Memantau keterlambatan pembukaan kelas, materi pelajaran, dan dokumentasi foto KBM.</p>
                    </div>
                    <span class="pc-pill"><i></i> Real-Time Tracker</span>
                </div>

                <div class="pc-tw">
                    <table class="pc-table">
                        <thead>
                            <tr>
                                <th>Kelas & Mapel</th>
                                <th>Guru Pengajar</th>
                                <th class="c">Status Buka Kelas</th>
                                <th>Materi & Ringkasan Pembelajaran</th>
                                <th class="c">Foto Bukti KBM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $todayJournals ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $journal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    
                                    <td class="pc-cls">
                                        <?php echo e($journal->schoolClass->name ?? 'Kelas'); ?>

                                        <br>
                                        <span><?php echo e($journal->subject ?? 'Mata Pelajaran'); ?></span>
                                    </td>

                                    
                                    <td>
                                        <div class="pc-tname"><?php echo e($journal->teacher->name ?? 'Guru'); ?></div>
                                        <span class="pc-nip">NIP: <?php echo e($journal->teacher->nip ?? '-'); ?></span>
                                    </td>

                                    
                                    <td class="c st">
                                        <?php if(($journal->evaluated_status ?? '') === 'TERLAMBAT' || !empty($journal->late_reason)): ?>
                                            <div class="pc-late">
                                                <div class="pc-late-top">
                                                    <span class="pc-late-b">🚨 Terlambat</span>
                                                    <span class="pc-late-t">
                                                        <?php echo e($journal->open_time_formatted ?? $journal->opened_at ?? '-'); ?> WIB
                                                    </span>
                                                </div>
                                                <?php if(!empty($journal->late_reason)): ?>
                                                    <div class="pc-late-r">
                                                        "<?php echo e($journal->late_reason); ?>"
                                                    </div>
                                                <?php else: ?>
                                                    <div class="pc-late-n">
                                                        Melewati jadwal jam masuk (<?php echo e($journal->schedule_start_formatted ?? '07:15'); ?> WIB)
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="pc-ontime">
                                                <span>🎉</span> Tepat Waktu (<?php echo e($journal->open_time_formatted ?? $journal->opened_at ?? '07:00'); ?> WIB)
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td>
                                        <div class="pc-topic"><?php echo e($journal->topic ?? 'Materi belum diisi'); ?></div>
                                        <p class="pc-sum"><?php echo e($journal->activities_summary ?? '-'); ?></p>
                                    </td>

                                    
                                    <td class="c">
                                        <?php if($journal->photo_evidence): ?>
                                            <a href="<?php echo e(Storage::url($journal->photo_evidence)); ?>" target="_blank" class="pc-photo">
                                                <img src="<?php echo e(Storage::url($journal->photo_evidence)); ?>" title="Klik untuk memperbesar foto KBM" alt="Foto KBM">
                                            </a>
                                        <?php else: ?>
                                            <span class="pc-nophoto">Belum unggah foto</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="empty">
                                        Belum ada jurnal berita acara mengajar yang diisi oleh guru hari ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="pc-card">
                <div class="pc-card-head">
                    <h3 class="pc-title"><i>🚪</i> Log Tap Gerbang Sekolah (Smart Gate) Hari Ini</h3>
                    <span class="pc-pill soft">10 Log Terakhir</span>
                </div>

                <div class="pc-logs">
                    <?php $__empty_1 = true; $__currentLoopData = $recentGateLogs ?? $todayGateLogs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="pc-log">
                            <div class="pc-log-l">
                                <div class="pc-log-av">👤</div>
                                <div>
                                    <h4><?php echo e($log->student->name ?? 'Siswa'); ?></h4>
                                    <p>
                                        NIS: <?php echo e($log->student->nis ?? '-'); ?> • <?php echo e($log->student->schoolClass->name ?? 'Kelas'); ?>

                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="pc-time">
                                    <?php echo e(\Carbon\Carbon::parse($log->created_at ?? $log->scan_in ?? $log->scan_in_at)->format('H:i')); ?> WIB
                                </span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="pc-empty">
                            Belum ada aktivitas tap gerbang dicatat hari ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\smart-attendance\resources\views/principal/dashboard.blade.php ENDPATH**/ ?>