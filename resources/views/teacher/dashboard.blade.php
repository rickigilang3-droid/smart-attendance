<x-app-layout>
    {{-- ============ STYLE TERISOLASI (tidak bergantung pada build Tailwind) ============ --}}
    <style>
        .tc-page {
            --a: #059669; --b: #0d9488; --dark: #064e3b;
            min-height: 100vh; padding: 20px 16px 40px; font-family: inherit;
            background:
                radial-gradient(900px 400px at 0% 0%, rgba(16,185,129,.16), transparent 60%),
                radial-gradient(700px 400px at 100% 10%, rgba(13,148,136,.13), transparent 60%),
                #f1f5f9;
        }
        @media (min-width: 640px)  { .tc-page { padding: 28px 24px 48px; } }
        @media (min-width: 1024px) { .tc-page { padding: 32px 32px 56px; } }
        .tc-wrap { max-width: 80rem; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

        /* ---------- ALERT ---------- */
        .tc-alert { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 18px; border-radius: 18px; background: #ecfdf5; border: 1px solid #a7f3d0; border-left: 6px solid #10b981; color: #065f46; font-size: 12px; font-weight: 700; box-shadow: 0 6px 16px rgba(16,185,129,.15); }
        .tc-alert-l { display: flex; align-items: center; gap: 10px; }
        .tc-alert button { border: none; background: transparent; color: #059669; font-weight: 900; cursor: pointer; font-size: 14px; }
        .tc-alert button:hover { color: #064e3b; }

        /* ---------- HERO PROFIL ---------- */
        .tc-hero { position: relative; overflow: hidden; border-radius: 28px; padding: 26px; color: #fff;
            background: linear-gradient(120deg, #064e3b 0%, #059669 55%, #0d9488 100%);
            box-shadow: 0 20px 40px -12px rgba(5,150,105,.5);
            display: flex; flex-direction: column; gap: 20px; }
        @media (min-width: 768px) { .tc-hero { flex-direction: row; align-items: center; justify-content: space-between; padding: 30px 36px; } }
        .tc-hero::before { content: ""; position: absolute; width: 320px; height: 320px; right: -80px; top: -140px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .tc-hero::after { content: ""; position: absolute; width: 200px; height: 200px; left: 35%; bottom: -130px; border-radius: 50%; background: rgba(255,255,255,.08); }
        .tc-hero > * { position: relative; z-index: 1; }
        .tc-prof { display: flex; align-items: center; gap: 20px; min-width: 0; }
        .tc-avatar { position: relative; flex-shrink: 0; width: 88px; height: 88px; cursor: pointer; }
        .tc-avatar img { width: 100%; height: 100%; border-radius: 24px; object-fit: cover; border: 4px solid rgba(255,255,255,.85); box-shadow: 0 10px 24px rgba(0,0,0,.3); transition: opacity .2s, transform .2s; background: #fff; }
        .tc-avatar:hover img { opacity: .85; transform: scale(1.03); }
        .tc-avatar-ov { position: absolute; inset: 0; border-radius: 24px; background: rgba(0,0,0,.45); color: #fff; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .2s; }
        .tc-avatar:hover .tc-avatar-ov { opacity: 1; }
        .tc-check { position: absolute; right: -4px; bottom: -4px; width: 24px; height: 24px; border-radius: 50%; background: #22c55e; border: 3px solid #fff; color: #fff; font-size: 11px; font-weight: 900; display: flex; align-items: center; justify-content: center; }
        .tc-chips { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .tc-chip { padding: 4px 12px; border-radius: 999px; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.35); font-size: 10.5px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
        .tc-nip { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.85); }
        .tc-hero h1 { margin: 8px 0 4px; font-size: 26px; font-weight: 900; line-height: 1.15; letter-spacing: -.01em; color: #fff; }
        @media (min-width: 640px) { .tc-hero h1 { font-size: 32px; } }
        .tc-subj { margin: 0; font-size: 13px; color: rgba(255,255,255,.85); }
        .tc-subj b { display: inline-block; margin-left: 4px; padding: 2px 10px; border-radius: 8px; background: #fff; color: var(--a); font-weight: 800; }
        .tc-edit { display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 14px; border: none; background: #fff; color: var(--a); font: 800 12px/1 inherit; cursor: pointer; box-shadow: 0 8px 20px rgba(0,0,0,.2); transition: transform .18s, box-shadow .18s; }
        .tc-edit:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(0,0,0,.28); }

        /* ---------- STAT CARDS ---------- */
        .tc-stats { display: grid; grid-template-columns: 1fr; gap: 18px; }
        @media (min-width: 640px)  { .tc-stats { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .tc-stats { grid-template-columns: repeat(4, 1fr); gap: 22px; } }
        .tc-stat { position: relative; overflow: hidden; border-radius: 24px; padding: 20px; min-height: 160px; display: flex; flex-direction: column; justify-content: space-between; transition: transform .2s, box-shadow .2s; }
        .tc-stat:hover { transform: translateY(-4px); }
        .tc-stat-top { position: relative; z-index: 1; display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
        .tc-label { margin: 0; font-size: 11px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .tc-val { margin: 6px 0 0; font-size: 32px; font-weight: 900; line-height: 1.1; letter-spacing: -.02em; }
        .tc-val small { font-size: 13px; font-weight: 800; opacity: .85; margin-left: 4px; letter-spacing: 0; }
        .tc-ico { width: 42px; height: 42px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .tc-note { position: relative; z-index: 1; margin: 14px 0 0; font-size: 11.5px; font-weight: 600; }

        /* Kartu berwarna (gradient) */
        .tc-stat.grad { color: #fff; }
        .tc-stat.grad::after { content: ""; position: absolute; width: 150px; height: 150px; right: -45px; bottom: -60px; border-radius: 50%; background: rgba(255,255,255,.14); }
        .tc-stat.grad .tc-ico { background: rgba(255,255,255,.22); border: 1px solid rgba(255,255,255,.3); backdrop-filter: blur(6px); }
        .tc-stat.grad .tc-label, .tc-stat.grad .tc-note { color: rgba(255,255,255,.92); }
        .tc-stat.grad .tc-note u { font-weight: 800; }
        .tc-stat.amber { background: linear-gradient(135deg, #f59e0b, #ea580c); box-shadow: 0 14px 28px rgba(234,88,12,.32); }
        .tc-stat.amber:hover { box-shadow: 0 20px 36px rgba(234,88,12,.42); }
        .tc-stat.green { background: linear-gradient(135deg, #059669, #0f766e); box-shadow: 0 14px 28px rgba(5,150,105,.32); }
        .tc-stat.green:hover { box-shadow: 0 20px 36px rgba(5,150,105,.42); }

        /* Kartu putih */
        .tc-stat.white { background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(15,23,42,.05); }
        .tc-stat.white::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 4px; background: linear-gradient(90deg, var(--c), color-mix(in srgb, var(--c) 50%, #fff)); }
        .tc-stat.white:hover { box-shadow: 0 16px 30px color-mix(in srgb, var(--c) 22%, transparent); }
        .tc-stat.white .tc-label { color: #64748b; }
        .tc-stat.white .tc-ico { background: linear-gradient(135deg, var(--c), color-mix(in srgb, var(--c) 60%, #fff)); box-shadow: 0 8px 16px color-mix(in srgb, var(--c) 35%, transparent); }
        .tc-stat.white .tc-val { color: #0f172a; font-size: 28px; }
        .tc-stat.white .tc-val small { color: #94a3b8; }
        .tc-stat.white .tc-note { color: #64748b; }
        .tc-pct { font-size: 26px; font-weight: 900; color: var(--c); }
        .tc-row { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 6px; }
        .tc-bar { position: relative; z-index: 1; width: 100%; height: 10px; border-radius: 999px; background: #e2e8f0; overflow: hidden; margin-top: 12px; }
        .tc-bar > div { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--c), color-mix(in srgb, var(--c) 55%, #fff)); transition: width .7s ease; }
        .tc-link { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 4px; align-self: flex-end; margin-top: 12px; padding: 6px 12px; border-radius: 999px; background: color-mix(in srgb, var(--c) 12%, #fff); color: var(--c); font-size: 11px; font-weight: 800; text-decoration: none; transition: background .18s, transform .18s; }
        .tc-link:hover { background: color-mix(in srgb, var(--c) 22%, #fff); transform: translateX(3px); }

        /* ---------- GRID ANALITIK ---------- */
        .tc-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
        @media (min-width: 1024px) { .tc-grid { grid-template-columns: 2fr 1fr; } }
        .tc-card { background: #fff; border-radius: 26px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 6px 20px rgba(15,23,42,.05); display: flex; flex-direction: column; gap: 18px; }
        .tc-card-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .tc-title { margin: 0; font-size: 17px; font-weight: 800; color: #0f172a; }
        .tc-sub { margin: 4px 0 0; font-size: 12px; color: #64748b; }
        .tc-month { padding: 6px 14px; border-radius: 999px; font-size: 11px; font-weight: 800; color: #fff; background: linear-gradient(135deg, var(--a), var(--b)); box-shadow: 0 4px 10px rgba(5,150,105,.35); }

        .tc-weeks { display: flex; flex-direction: column; gap: 16px; padding-top: 4px; }
        .tc-week { padding: 14px 16px; border-radius: 18px; background: #f8fafc; border: 1px solid #f1f5f9; transition: background .18s, transform .18s; }
        .tc-week:hover { background: #fff; transform: translateX(3px); box-shadow: 0 8px 18px rgba(15,23,42,.06); }
        .tc-week-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; font-size: 12px; font-weight: 800; color: #334155; }
        .tc-week-top span:last-child { padding: 3px 10px; border-radius: 999px; font-size: 11px; background: color-mix(in srgb, var(--w) 12%, #fff); color: var(--w); }
        .tc-track { width: 100%; height: 12px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
        .tc-track > div { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--w), color-mix(in srgb, var(--w) 60%, #fff)); }

        /* ---------- DONUT ---------- */
        .tc-donut-wrap { display: flex; align-items: center; justify-content: center; margin: 8px 0 4px; }
        .tc-donut { position: relative; width: 170px; height: 170px; display: flex; align-items: center; justify-content: center; }
        .tc-donut svg { width: 100%; height: 100%; transform: rotate(-90deg); filter: drop-shadow(0 8px 14px rgba(16,185,129,.3)); }
        .tc-donut-c { position: absolute; text-align: center; }
        .tc-donut-c b { display: block; font-size: 32px; font-weight: 900; color: #0f172a; line-height: 1; }
        .tc-donut-c span { display: inline-block; margin-top: 6px; padding: 2px 10px; border-radius: 999px; background: #d1fae5; color: #047857; font-size: 9.5px; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
        .tc-leg { display: flex; flex-direction: column; gap: 10px; }
        .tc-leg-i { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 14px; font-size: 12px; font-weight: 700; border: 1px solid; }
        .tc-leg-i span:first-child { display: flex; align-items: center; gap: 8px; }
        .tc-leg-i i { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .tc-leg-i.ok { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; } .tc-leg-i.ok i { background: #10b981; }
        .tc-leg-i.no { background: #fff1f2; border-color: #fecdd3; color: #9f1239; } .tc-leg-i.no i { background: #f43f5e; }
        .tc-leg-i b { font-weight: 900; }

        /* ---------- MODAL ---------- */
        .tc-modal { position: fixed; inset: 0; z-index: 99990; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); }
        .tc-modal.hidden { display: none; }
        .tc-modal-box { width: 100%; max-width: 440px; background: #fff; border-radius: 28px; overflow: hidden; box-shadow: 0 30px 60px rgba(0,0,0,.4); }
        .tc-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; color: #fff; background: linear-gradient(120deg, #064e3b, #059669 60%, #0d9488); }
        .tc-modal-head h3 { margin: 0; font-size: 15px; font-weight: 800; color: #fff; }
        .tc-modal-x { width: 32px; height: 32px; border-radius: 10px; border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.15); color: #fff; font-size: 14px; cursor: pointer; transition: background .18s; }
        .tc-modal-x:hover { background: rgba(255,255,255,.3); }
        .tc-modal-body { padding: 22px; display: flex; flex-direction: column; gap: 16px; }
        .tc-modal-body label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 700; color: #475569; }
        .tc-file { width: 100%; box-sizing: border-box; padding: 14px; border: 2px dashed #a7f3d0; border-radius: 16px; background: #f0fdf4; font-size: 12px; color: #64748b; }
        .tc-file::file-selector-button { margin-right: 14px; padding: 8px 16px; border: none; border-radius: 10px; background: linear-gradient(135deg, #059669, #0d9488); color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; }
        .tc-modal-act { display: flex; justify-content: flex-end; gap: 10px; padding-top: 4px; }
        .tc-btn-cancel { padding: 10px 18px; border: none; border-radius: 12px; background: #f1f5f9; color: #64748b; font: 800 12px/1 inherit; cursor: pointer; }
        .tc-btn-cancel:hover { background: #e2e8f0; }
        .tc-btn-save { padding: 10px 20px; border: none; border-radius: 12px; background: linear-gradient(135deg, #059669, #0d9488); color: #fff; font: 800 12px/1 inherit; cursor: pointer; box-shadow: 0 8px 16px rgba(5,150,105,.35); transition: transform .18s; }
        .tc-btn-save:hover { transform: translateY(-2px); }
    </style>

    <div class="tc-page">
        <div class="tc-wrap">

            {{-- Alert Notifikasi Sukses --}}
            @if(session('success'))
                <div class="tc-alert">
                    <div class="tc-alert-l">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            {{-- Header / Kartu Profil Guru --}}
            <div class="tc-hero">
                <div class="tc-prof">
                    <div class="tc-avatar" onclick="document.getElementById('modalFoto').classList.remove('hidden')">
                        <img src="{{ optional($teacher)->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=059669&background=ECFDF5' }}"
                             alt="{{ optional($teacher)->name ?? auth()->user()->name }}">
                        <div class="tc-avatar-ov">📷 Ubah</div>
                        <span class="tc-check" title="Status Aktif">✓</span>
                    </div>
                    <div>
                        <div class="tc-chips">
                            <span class="tc-chip">PORTAL GURU</span>
                            <span class="tc-nip">• NIP: {{ optional($teacher)->nip ?? '-' }}</span>
                        </div>
                        <h1>{{ optional($teacher)->name ?? auth()->user()->name }}</h1>
                        <p class="tc-subj">Guru Pengampu: <b>{{ optional($teacher)->subject ?? 'Mata Pelajaran' }}</b></p>
                    </div>
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('modalFoto').classList.remove('hidden')" class="tc-edit">
                        ⚙️ Edit Profil
                    </button>
                </div>
            </div>

            {{-- 🏆 4 CARD METRIK UTAMA --}}
            <div class="tc-stats">

                <!-- CARD 1: ON-TIME STREAK -->
                <div class="tc-stat grad amber">
                    <div class="tc-stat-top">
                        <div>
                            <p class="tc-label">KBM On-Time Streak</p>
                            <h3 class="tc-val">🔥 {{ $onTimeStreak ?? 0 }} <small>Hari</small></h3>
                        </div>
                        <div class="tc-ico">⚡</div>
                    </div>
                    <p class="tc-note">Konsistensi waktu buka kelas</p>
                </div>

                <!-- CARD 2: SKOR KEPATUHAN -->
                <div class="tc-stat grad green">
                    <div class="tc-stat-top">
                        <div>
                            <p class="tc-label">Skor Kepatuhan</p>
                            <h3 class="tc-val">⭐ {{ $performanceScore ?? 100 }} <small>/ 100</small></h3>
                        </div>
                        <div class="tc-ico">🏆</div>
                    </div>
                    <p class="tc-note">Predikat: <u>{{ $badgeTitle ?? 'Guru Teladan Disiplin' }}</u></p>
                </div>

                <!-- CARD 3: PROGRES KBM HARI INI -->
                @php
                    $totalToday = $todaySchedules->count();
                    $completedToday = $todaySchedules->where('is_completed', true)->count();
                    $percentage = $totalToday > 0 ? round(($completedToday / $totalToday) * 100) : 100;
                @endphp
                <div class="tc-stat white" style="--c: #0d9488;">
                    <div>
                        <div class="tc-stat-top">
                            <p class="tc-label">Progres KBM Hari Ini</p>
                            <div class="tc-ico">🎯</div>
                        </div>
                        <div class="tc-row">
                            <div>
                                <h3 class="tc-val">{{ $completedToday }} <small>/ {{ $totalToday }} Sesi</small></h3>
                                <p class="tc-note" style="margin-top:4px;">Terpresensi sempurna</p>
                            </div>
                            <span class="tc-pct">{{ $percentage }}%</span>
                        </div>
                    </div>
                    <div class="tc-bar"><div style="width: {{ $percentage }}%"></div></div>
                </div>

                <!-- CARD 4: PERLU TINJAUAN ORTU -->
                <div class="tc-stat white" style="--c: #e11d48;">
                    <div>
                        <div class="tc-stat-top">
                            <p class="tc-label">Perlu Tinjauan Ortu</p>
                            <div class="tc-ico">📩</div>
                        </div>
                        <h3 class="tc-val" style="margin-top:8px;">{{ isset($pendingPermissions) ? count($pendingPermissions) : 0 }} <small>Pengajuan</small></h3>
                        <p class="tc-note" style="margin-top:4px;">Surat sakit / izin masuk</p>
                    </div>
                    <a href="{{ route('teacher.permissions.index') }}" class="tc-link">Tinjau di Sidebar →</a>
                </div>

            </div>

            {{-- 📊 SECTION ANALITIK KETEPATAN WAKTU & REKAP KBM --}}
            <div class="tc-grid">

                <!-- GRAFIK KETEPATAN WAKTU MENGAJAR (BULAN INI) -->
                <div class="tc-card">
                    <div class="tc-card-head">
                        <div>
                            <h2 class="tc-title">📈 Rekap Ketepatan Waktu KBM Bulanan</h2>
                            <p class="tc-sub">Statistik rasio buka kelas tepat waktu vs terlambat dalam bulan berjalan</p>
                        </div>
                        <span class="tc-month">Oktober 2026</span>
                    </div>

                    <div class="tc-weeks">
                        <div class="tc-week" style="--w: #10b981;">
                            <div class="tc-week-top"><span>Minggu Ke-1</span><span>100% Tepat Waktu (5/5 Sesi)</span></div>
                            <div class="tc-track"><div style="width: 100%"></div></div>
                        </div>

                        <div class="tc-week" style="--w: #10b981;">
                            <div class="tc-week-top"><span>Minggu Ke-2</span><span>100% Tepat Waktu (6/6 Sesi)</span></div>
                            <div class="tc-track"><div style="width: 100%"></div></div>
                        </div>

                        <div class="tc-week" style="--w: #f59e0b;">
                            <div class="tc-week-top"><span>Minggu Ke-3</span><span>80% Tepat Waktu (4/5 Sesi, 1 Telat)</span></div>
                            <div class="tc-track"><div style="width: 80%"></div></div>
                        </div>

                        <div class="tc-week" style="--w: #10b981;">
                            <div class="tc-week-top"><span>Minggu Ke-4 (Berjalan)</span><span>100% Tepat Waktu (4/4 Sesi)</span></div>
                            <div class="tc-track"><div style="width: 100%"></div></div>
                        </div>
                    </div>
                </div>

                <!-- RINGKASAN RASIO DISIPLIN (DONUT CHART) -->
                <div class="tc-card">
                    <div>
                        <h2 class="tc-title">Rasio Kehadiran KBM</h2>
                        <p class="tc-sub">Persentase disiplin tatap muka</p>
                    </div>

                    <div class="tc-donut-wrap">
                        <div class="tc-donut">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="tcGrad" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#34d399" />
                                        <stop offset="100%" stop-color="#059669" />
                                    </linearGradient>
                                </defs>
                                <path stroke-width="4" stroke="#ffe4e6" fill="none"
                                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path stroke-width="4" stroke-dasharray="95, 100" stroke-linecap="round" stroke="url(#tcGrad)" fill="none"
                                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="tc-donut-c">
                                <b>95%</b>
                                <span>Sangat Baik</span>
                            </div>
                        </div>
                    </div>

                    <div class="tc-leg">
                        <div class="tc-leg-i ok">
                            <span><i></i> Tepat Waktu</span>
                            <b>19 Sesi</b>
                        </div>
                        <div class="tc-leg-i no">
                            <span><i></i> Terlambat</span>
                            <b>1 Sesi</b>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Modal Upload Foto Profil --}}
    <div id="modalFoto" class="tc-modal hidden">
        <div class="tc-modal-box">
            <div class="tc-modal-head">
                <h3>Ubah Foto Profil Guru</h3>
                <button type="button" onclick="document.getElementById('modalFoto').classList.add('hidden')" class="tc-modal-x">✕</button>
            </div>
            <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data" class="tc-modal-body">
                @csrf
                <div>
                    <label>Pilih Foto Baru (JPG, PNG, WEBP max 2MB)</label>
                    <input type="file" name="photo" accept="image/*" required class="tc-file">
                </div>
                <div class="tc-modal-act">
                    <button type="button" onclick="document.getElementById('modalFoto').classList.add('hidden')" class="tc-btn-cancel">Batal</button>
                    <button type="submit" class="tc-btn-save">Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>