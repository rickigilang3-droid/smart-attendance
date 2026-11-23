<x-app-layout>
    {{-- ============ STYLE TERISOLASI (tidak bergantung pada build Tailwind) ============ --}}
    <style>
        .pr-page {
            --a: #0284c7; --b: #06b6d4; --dark: #0c4a6e;
            min-height: 100vh; padding: 20px 16px 40px; font-family: inherit;
            background:
                radial-gradient(900px 400px at 0% 0%, rgba(14,165,233,.16), transparent 60%),
                radial-gradient(700px 400px at 100% 10%, rgba(6,182,212,.13), transparent 60%),
                #f1f5f9;
        }
        @media (min-width: 640px)  { .pr-page { padding: 28px 24px 48px; } }
        @media (min-width: 1024px) { .pr-page { padding: 32px 32px 56px; } }
        .pr-wrap { max-width: 80rem; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

        /* ---------- HERO ---------- */
        .pr-hero { position: relative; overflow: hidden; border-radius: 28px; padding: 28px; color: #fff;
            background: linear-gradient(120deg, #0c4a6e 0%, #0284c7 55%, #06b6d4 100%);
            box-shadow: 0 20px 40px -12px rgba(2,132,199,.5);
            display: flex; flex-direction: column; gap: 20px; }
        @media (min-width: 768px) { .pr-hero { flex-direction: row; align-items: center; justify-content: space-between; padding: 32px 36px; } }
        .pr-hero::before { content: ""; position: absolute; width: 320px; height: 320px; right: -80px; top: -140px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .pr-hero::after { content: ""; position: absolute; width: 200px; height: 200px; left: 35%; bottom: -130px; border-radius: 50%; background: rgba(255,255,255,.08); }
        .pr-hero > * { position: relative; z-index: 1; }
        .pr-eyebrow { display: inline-block; padding: 5px 14px; border-radius: 999px; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.35); font-size: 10.5px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
        .pr-hero h1 { margin: 12px 0 6px; font-size: 26px; font-weight: 900; line-height: 1.15; letter-spacing: -.01em; color: #fff; }
        @media (min-width: 640px) { .pr-hero h1 { font-size: 32px; } }
        .pr-hero p { margin: 0; max-width: 540px; font-size: 13px; line-height: 1.6; color: rgba(255,255,255,.85); }

        .pr-kid { display: flex; align-items: center; gap: 14px; padding: 14px 18px; border-radius: 20px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.3); backdrop-filter: blur(8px); }
        .pr-kid-photo { position: relative; flex-shrink: 0; }
        .pr-kid-photo img, .pr-kid-ph { width: 56px; height: 56px; border-radius: 16px; object-fit: cover; border: 3px solid rgba(255,255,255,.8); box-shadow: 0 6px 14px rgba(0,0,0,.25); }
        .pr-kid-ph { background: rgba(255,255,255,.25); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 900; color: #fff; box-sizing: border-box; }
        .pr-kid-on { position: absolute; right: -3px; bottom: -3px; width: 14px; height: 14px; border-radius: 50%; background: #4ade80; border: 3px solid #0284c7; }
        .pr-kid small { display: block; font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: rgba(255,255,255,.8); }
        .pr-kid b { display: block; margin-top: 2px; font-size: 16px; font-weight: 900; color: #fff; }
        .pr-kid span { display: block; margin-top: 2px; font-size: 12px; font-weight: 600; color: rgba(255,255,255,.9); }

        /* ---------- ALERT & WARNING ---------- */
        .pr-alert { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 18px; border-radius: 18px; background: #ecfdf5; border: 1px solid #a7f3d0; border-left: 6px solid #10b981; color: #065f46; font-size: 12px; font-weight: 700; box-shadow: 0 6px 16px rgba(16,185,129,.15); }
        .pr-alert button { border: none; background: transparent; color: #059669; font-weight: 900; font-size: 14px; cursor: pointer; }
        .pr-alert button:hover { color: #064e3b; }
        .pr-warn { padding: 36px 24px; text-align: center; border-radius: 26px; background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fde68a; box-shadow: 0 8px 20px rgba(245,158,11,.15); }
        .pr-warn .e { font-size: 44px; }
        .pr-warn h3 { margin: 8px 0; font-size: 17px; font-weight: 900; color: #92400e; }
        .pr-warn p { margin: 0 auto; max-width: 460px; font-size: 12.5px; line-height: 1.6; color: #b45309; }

        /* ---------- CARD ---------- */
        .pr-card { background: #fff; border-radius: 26px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 6px 20px rgba(15,23,42,.05); display: flex; flex-direction: column; gap: 18px; }
        .pr-card-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .pr-title { margin: 0; font-size: 17px; font-weight: 800; color: #0f172a; }
        .pr-title.sm { font-size: 15px; }
        .pr-sub { margin: 4px 0 0; font-size: 12px; color: #64748b; }
        .pr-pill { padding: 6px 14px; border-radius: 999px; font-size: 11px; font-weight: 900; letter-spacing: .06em; color: #fff; background: linear-gradient(135deg, var(--a), var(--b)); box-shadow: 0 4px 10px rgba(2,132,199,.35); }
        .pr-pill.soft { background: #f1f5f9; color: #475569; box-shadow: none; letter-spacing: 0; font-weight: 800; }

        /* ---------- GATE ---------- */
        .pr-gate { display: grid; grid-template-columns: 1fr; gap: 16px; }
        @media (min-width: 768px) { .pr-gate { grid-template-columns: 1fr 1fr; } }
        .pr-gate-i { position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px; border-radius: 22px; background: linear-gradient(135deg, color-mix(in srgb, var(--c) 9%, #fff), #fff); border: 1px solid color-mix(in srgb, var(--c) 25%, #e2e8f0); transition: transform .2s, box-shadow .2s; }
        .pr-gate-i:hover { transform: translateY(-3px); box-shadow: 0 14px 28px color-mix(in srgb, var(--c) 20%, transparent); }
        .pr-gate-i::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: var(--c); }
        .pr-gate-l { display: flex; align-items: center; gap: 14px; }
        .pr-gate-ic { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; background: linear-gradient(135deg, var(--c), color-mix(in srgb, var(--c) 55%, #fff)); box-shadow: 0 8px 16px color-mix(in srgb, var(--c) 35%, transparent); }
        .pr-gate-lb { margin: 0; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #64748b; }
        .pr-gate-v { margin: 4px 0 0; font-size: 20px; font-weight: 900; color: #0f172a; }

        /* ---------- BADGES ---------- */
        .pr-bd { display: inline-block; padding: 5px 11px; border-radius: 10px; font-size: 10.5px; font-weight: 800; white-space: nowrap; }
        .pr-bd.ok { background: #d1fae5; color: #065f46; }
        .pr-bd.warn { background: #fef3c7; color: #92400e; }
        .pr-bd.info { background: #dbeafe; color: #1e40af; }
        .pr-bd.bad { background: #ffe4e6; color: #9f1239; }
        .pr-bd.mute { background: #e2e8f0; color: #475569; }
        .pr-bd.soft { background: #f1f5f9; color: #64748b; font-weight: 600; }
        .pr-bd.round { border-radius: 999px; }
        .pr-bd.pulse { animation: prPulse 1.8s infinite; }
        @keyframes prPulse { 0%,100% { opacity: 1; } 50% { opacity: .6; } }

        /* ---------- TABLE ---------- */
        .pr-tw { overflow-x: auto; border-radius: 18px; border: 1px solid #e2e8f0; }
        .pr-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 12px; }
        .pr-table thead tr { background: linear-gradient(90deg, #f0f9ff, #ecfeff); }
        .pr-table th { padding: 13px 14px; font-size: 10.5px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; color: var(--dark); border-bottom: 2px solid #bae6fd; white-space: nowrap; }
        .pr-table td { padding: 13px 14px; color: #475569; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .pr-table tbody tr { transition: background .15s; }
        .pr-table tbody tr:hover { background: #f0f9ff; }
        .pr-table tbody tr:last-child td { border-bottom: none; }
        .pr-table tr.is-break { background: #f8fafc; }
        .pr-table .r { text-align: right; }
        .pr-table .mono { font-family: ui-monospace, SFMono-Regular, monospace; white-space: nowrap; color: #334155; }
        .pr-table .strong { font-weight: 800; color: #0f172a; }
        .pr-table .empty { padding: 36px 16px; text-align: center; color: #94a3b8; }
        .pr-table .trunc { max-width: 20rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .pr-table .lnk { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 800; color: var(--a); text-decoration: none; }
        .pr-table .lnk:hover { text-decoration: underline; }
        .pr-table .muted { color: #cbd5e1; }
        .pr-table .dim { color: #94a3b8; }

        /* ---------- GRID BAWAH ---------- */
        .pr-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
        @media (min-width: 1024px) { .pr-grid { grid-template-columns: 1fr 2fr; } }

        .pr-recap { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .pr-rc { position: relative; overflow: hidden; padding: 16px 12px; border-radius: 20px; text-align: center; background: color-mix(in srgb, var(--c) 10%, #fff); border: 1px solid color-mix(in srgb, var(--c) 25%, #fff); transition: transform .2s; }
        .pr-rc:hover { transform: translateY(-3px); }
        .pr-rc::after { content: ""; position: absolute; width: 70px; height: 70px; right: -22px; bottom: -28px; border-radius: 50%; background: color-mix(in srgb, var(--c) 14%, transparent); }
        .pr-rc span { position: relative; z-index: 1; font-size: 10.5px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; color: var(--c); }
        .pr-rc p { position: relative; z-index: 1; margin: 6px 0 0; font-size: 30px; font-weight: 900; line-height: 1; color: color-mix(in srgb, var(--c) 80%, #000); }

        .pr-btn { display: inline-flex; align-items: center; gap: 6px; padding: 11px 18px; border: none; border-radius: 14px; background: linear-gradient(135deg, var(--a), var(--b)); color: #fff; font: 800 12px/1 inherit; cursor: pointer; box-shadow: 0 8px 18px rgba(2,132,199,.4); transition: transform .18s, box-shadow .18s; }
        .pr-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(2,132,199,.5); }

        /* ---------- PENGUMUMAN ---------- */
        .pr-ann-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
        @media (min-width: 768px)  { .pr-ann-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .pr-ann-grid { grid-template-columns: repeat(3, 1fr); } }
        .pr-ann { position: relative; overflow: hidden; padding: 18px; border-radius: 22px; background: linear-gradient(160deg, #f0f9ff, #fff); border: 1px solid #e0f2fe; transition: transform .2s, box-shadow .2s; }
        .pr-ann:hover { transform: translateY(-4px); box-shadow: 0 16px 30px rgba(2,132,199,.16); }
        .pr-ann::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 4px; background: linear-gradient(90deg, var(--a), var(--b)); }
        .pr-ann-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .pr-cat { padding: 3px 10px; border-radius: 8px; background: #e0f2fe; color: #075985; font-size: 10px; font-weight: 800; }
        .pr-date { font-size: 10.5px; color: #94a3b8; font-family: ui-monospace, monospace; }
        .pr-ann h3 { margin: 12px 0 0; font-size: 14px; font-weight: 800; color: #0f172a; }
        .pr-ann p { margin: 6px 0 0; font-size: 12px; line-height: 1.6; color: #475569; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .pr-empty { grid-column: 1 / -1; padding: 32px 16px; text-align: center; font-size: 12px; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 20px; background: #f8fafc; }

        /* ---------- MODAL ---------- */
        .pr-modal { position: fixed; inset: 0; z-index: 99990; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); }
        .pr-modal.hidden { display: none; }
        .pr-modal-box { width: 100%; max-width: 460px; max-height: 94vh; overflow-y: auto; background: #fff; border-radius: 28px; box-shadow: 0 30px 60px rgba(0,0,0,.4); }
        .pr-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; color: #fff; background: linear-gradient(120deg, #0c4a6e, #0284c7 60%, #06b6d4); }
        .pr-modal-head h3 { margin: 0; font-size: 15px; font-weight: 800; color: #fff; }
        .pr-modal-x { width: 32px; height: 32px; border-radius: 10px; border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.15); color: #fff; font-size: 14px; cursor: pointer; }
        .pr-modal-x:hover { background: rgba(255,255,255,.3); }
        .pr-form { padding: 22px; display: flex; flex-direction: column; gap: 14px; font-size: 12px; }
        .pr-form label { display: block; margin-bottom: 6px; font-weight: 700; color: #334155; }
        .pr-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .pr-input { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 12px; background: #f8fafc; font: 500 12px/1.4 inherit; color: #0f172a; outline: none; transition: border-color .18s, box-shadow .18s, background .18s; }
        .pr-input:focus { border-color: var(--a); background: #fff; box-shadow: 0 0 0 4px rgba(2,132,199,.15); }
        .pr-file { border: 2px dashed #bae6fd; background: #f0f9ff; }
        .pr-file::file-selector-button { margin-right: 12px; padding: 7px 14px; border: none; border-radius: 10px; background: linear-gradient(135deg, var(--a), var(--b)); color: #fff; font-size: 11px; font-weight: 700; cursor: pointer; }
        .pr-act { display: flex; gap: 10px; padding-top: 4px; }
        .pr-cancel { flex: 1; padding: 12px; border: none; border-radius: 14px; background: #f1f5f9; color: #475569; font: 800 12px/1 inherit; cursor: pointer; }
        .pr-cancel:hover { background: #e2e8f0; }
        .pr-send { flex: 1; padding: 12px; border: none; border-radius: 14px; background: linear-gradient(135deg, var(--a), var(--b)); color: #fff; font: 800 12px/1 inherit; cursor: pointer; box-shadow: 0 8px 16px rgba(2,132,199,.4); transition: transform .18s; }
        .pr-send:hover { transform: translateY(-2px); }
    </style>

    <div class="pr-page">
        <div class="pr-wrap">

            {{-- Banner Header Orang Tua --}}
            <div class="pr-hero">
                <div>
                    <span class="pr-eyebrow">Portal Orang Tua / Wali Siswa</span>
                    <h1>Selamat Datang, {{ auth()->user()->name }}</h1>
                    <p>Pantau kehadiran gerbang, presensi kelas, dan ajukan surat izin/sakit anak Anda secara digital.</p>
                </div>
                @if(isset($student) && $student)
                    <div class="pr-kid">
                        <!-- Foto Profil Siswa -->
                        <div class="pr-kid-photo">
                            @php
                                $photoPath = $student->photo ?? $student->avatar ?? $student->profile_photo ?? $student->user->photo ?? null;
                            @endphp

                            @if($photoPath)
                                <img src="{{ storage_media_url($photoPath) }}" alt="{{ $student->name }}">
                            @elseif($photoPath && filter_var($photoPath, FILTER_VALIDATE_URL))
                                <img src="{{ $photoPath }}" alt="{{ $student->name }}">
                            @else
                                <div class="pr-kid-ph">{{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}</div>
                            @endif
                            <span class="pr-kid-on"></span>
                        </div>
                        <!-- Informasi Siswa -->
                        <div>
                            <small>Siswa Terhubung</small>
                            <b>{{ $student->name }}</b>
                            <span>NISN: {{ $student->nisn ?? '-' }} • {{ $student->schoolClass->name ?? $student->schoolClass->class_name ?? 'Kelas -' }}</span>
                        </div>
                    </div>
                @endif
            </div>

            @if(session('success'))
                <div class="pr-alert">
                    <span>✅ {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @if(!isset($student) || !$student)
                <div class="pr-warn">
                    <div class="e">⚠️</div>
                    <h3>Data Siswa Belum Terhubung</h3>
                    <p>Sistem belum menemukan data siswa yang terhubung dengan akun Anda. Silakan hubungi Administrator sekolah untuk melakukan pemetaan data akun orang tua dan NISN siswa.</p>
                </div>
            @else
                {{-- 1. Status Tap Gerbang (Smart Gate) Hari Ini --}}
                <div class="pr-card">
                    <div class="pr-card-head">
                        <div>
                            <h2 class="pr-title">Kehadiran Gerbang Sekolah (Smart Gate) 🏫</h2>
                            <p class="pr-sub">Status tap kartu/fingerprint anak Anda di pintu gerbang utama hari ini ({{ \Carbon\Carbon::parse($today)->format('d M Y') }})</p>
                        </div>
                        <span class="pr-pill">HARI INI</span>
                    </div>
                    <div class="pr-gate">
                        {{-- Tap Masuk --}}
                        <div class="pr-gate-i" style="--c: #10b981;">
                            <div class="pr-gate-l">
                                <div class="pr-gate-ic">📥</div>
                                <div>
                                    <p class="pr-gate-lb">Jam Masuk Sekolah</p>
                                    <p class="pr-gate-v">
                                        @if(isset($gateLog) && $gateLog && $gateLog->scan_in)
                                            {{ \Carbon\Carbon::parse($gateLog->scan_in)->format('H:i:s') }} WIB
                                        @else
                                            Belum Tap Masuk
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div>
                                @if(isset($gateLog) && $gateLog && $gateLog->scan_in)
                                    @if(\Carbon\Carbon::parse($gateLog->scan_in)->format('H:i:s') > '07:15:00')
                                        <span class="pr-bd bad">Terlambat</span>
                                    @else
                                        <span class="pr-bd ok">Tepat Waktu</span>
                                    @endif
                                @else
                                    <span class="pr-bd mute">—</span>
                                @endif
                            </div>
                        </div>
                        {{-- Tap Pulang --}}
                        <div class="pr-gate-i" style="--c: #f59e0b;">
                            <div class="pr-gate-l">
                                <div class="pr-gate-ic">📤</div>
                                <div>
                                    <p class="pr-gate-lb">Jam Kepulangan</p>
                                    <p class="pr-gate-v">
                                        @if(isset($gateLog) && $gateLog && $gateLog->scan_out)
                                            {{ \Carbon\Carbon::parse($gateLog->scan_out)->format('H:i:s') }} WIB
                                        @else
                                            Belum Tap Pulang
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div>
                                @if(isset($gateLog) && $gateLog && $gateLog->scan_out)
                                    <span class="pr-bd warn">Sudah Pulang</span>
                                @else
                                    <span class="pr-bd mute">Di Sekolah</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Presensi Kelas Per Mata Pelajaran Hari Ini --}}
                <div class="pr-card">
                    <div class="pr-card-head">
                        <div>
                            <h2 class="pr-title">Presensi Kelas Per Mata Pelajaran 📖</h2>
                            <p class="pr-sub">Status kehadiran anak yang diinput oleh Guru Pengajar pada setiap jam pelajaran hari ini</p>
                        </div>
                    </div>
                    <div class="pr-tw">
                        <table class="pr-table">
                            <thead>
                                <tr>
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengajar</th>
                                    <th class="r">Status Absensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todaySchedules ?? [] as $sched)
                                    @php
                                        // Deteksi otomatis apakah jadwal ini adalah Istirahat / Ishoma
                                        $subjectName = strtolower($sched->subject_name ?? $sched->subject ?? $sched->lesson->name ?? '');
                                        $isBreakItem = (isset($sched->is_break) && $sched->is_break)
                                                       || str_contains($subjectName, 'istirahat')
                                                       || str_contains($subjectName, 'ishoma');
                                    @endphp

                                    <tr class="{{ $isBreakItem ? 'is-break' : '' }}">
                                        <td class="mono">
                                            {{ $sched->formatted_start ?? (isset($sched->start_time) ? substr($sched->start_time, 0, 5) : '00:00') }} -
                                            {{ $sched->formatted_end ?? (isset($sched->end_time) ? substr($sched->end_time, 0, 5) : '00:00') }}
                                        </td>
                                        <td class="strong">
                                            {{ $sched->subject_name ?? $sched->subject ?? $sched->lesson->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $isBreakItem ? '-' : ($sched->teacher_name ?? $sched->teacher->name ?? $sched->teacher->user->name ?? '-') }}
                                        </td>
                                        <td class="r">
                                            @if($isBreakItem)
                                                {{-- Menampilkan tanda strip untuk jam Istirahat & Ishoma --}}
                                                <span class="pr-bd mute">—</span>
                                            @elseif(in_array(($sched->attendance_status ?? ''), ['Hadir', 'H', 'HADIR']))
                                                <span class="pr-bd ok">✅ Hadir</span>
                                            @elseif(in_array(($sched->attendance_status ?? ''), ['Sakit', 'S', 'SAKIT']))
                                                <span class="pr-bd warn">😷 Sakit</span>
                                            @elseif(in_array(($sched->attendance_status ?? ''), ['Izin', 'I', 'IZIN']))
                                                <span class="pr-bd info">📩 Izin</span>
                                            @elseif(in_array(($sched->attendance_status ?? ''), ['Alpa', 'Alpha', 'A', 'ALPHA']))
                                                <span class="pr-bd bad">❌ Alpa</span>
                                            @else
                                                <span class="pr-bd soft">⏳ Belum Diabsen</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty">Tidak ada jadwal pelajaran untuk hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 3. Widget Rekap Presensi Bulanan & Riwayat Surat Izin/Sakit --}}
                <div class="pr-grid">

                    {{-- Rekap Presensi Bulan Ini --}}
                    <div class="pr-card">
                        <div class="pr-card-head" style="align-items:center;">
                            <h2 class="pr-title sm">Rekap Presensi Bulan Ini 📊</h2>
                            <span class="pr-pill soft">{{ date('F Y') }}</span>
                        </div>
                        <div class="pr-recap">
                            <div class="pr-rc" style="--c: #10b981;">
                                <span>Hadir</span>
                                <p>{{ $monthlyRecap['hadir'] ?? 0 }}</p>
                            </div>
                            <div class="pr-rc" style="--c: #f59e0b;">
                                <span>Sakit</span>
                                <p>{{ $monthlyRecap['sakit'] ?? 0 }}</p>
                            </div>
                            <div class="pr-rc" style="--c: #3b82f6;">
                                <span>Izin</span>
                                <p>{{ $monthlyRecap['izin'] ?? 0 }}</p>
                            </div>
                            <div class="pr-rc" style="--c: #f43f5e;">
                                <span>Alpha</span>
                                <p>{{ $monthlyRecap['alpha'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat Surat Izin & Sakit Digital --}}
                    <div class="pr-card">
                        <div class="pr-card-head" style="align-items:center;">
                            <div>
                                <h2 class="pr-title sm">Riwayat Surat Izin & Sakit Digital 📄</h2>
                                <p class="pr-sub">Status pengajuan permohonan izin/sakit anak ke wali kelas</p>
                            </div>
                            <button onclick="document.getElementById('modalPermission').classList.remove('hidden')" class="pr-btn">
                                ✍️ Ajukan Surat
                            </button>
                        </div>
                        <div class="pr-tw">
                            <table class="pr-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Alasan</th>
                                        <th>Berkas</th>
                                        <th class="r">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($permissions ?? [] as $perm)
                                        <tr>
                                            <td class="mono">
                                                {{ \Carbon\Carbon::parse($perm->start_date)->format('d/m/Y') }}
                                                @if($perm->start_date != $perm->end_date)
                                                    <span class="dim">- {{ \Carbon\Carbon::parse($perm->end_date)->format('d/m/Y') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="pr-bd {{ $perm->type == 'Sakit' ? 'warn' : 'info' }}">
                                                    {{ $perm->type }}
                                                </span>
                                            </td>
                                            <td class="trunc">
                                                {{ $perm->reason }}
                                            </td>
                                            <td>
                                                @if($perm->letter_file)
                                                    <a href="{{ storage_media_url($perm->letter_file) }}" target="_blank" class="lnk">
                                                        📎 Lihat File
                                                    </a>
                                                @else
                                                    <span class="muted">-</span>
                                                @endif
                                            </td>
                                            <td class="r">
                                                @if(in_array($perm->status, ['Approved', 'Disetujui']))
                                                    <span class="pr-bd ok round">✅ Disetujui</span>
                                                @elseif(in_array($perm->status, ['Rejected', 'Ditolak']))
                                                    <span class="pr-bd bad round">❌ Ditolak</span>
                                                @else
                                                    <span class="pr-bd warn round pulse">⏳ Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty">
                                                Belum ada riwayat pengajuan surat izin/sakit.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 4. Widget Pengumuman Resmi Sekolah --}}
                <div class="pr-card">
                    <div class="pr-card-head" style="align-items:center;">
                        <h2 class="pr-title">Pengumuman Sekolah Untuk Orang Tua 📢</h2>
                        <span class="pr-pill soft">Informasi Resmi</span>
                    </div>
                    <div class="pr-ann-grid">
                        @forelse($announcements ?? [] as $ann)
                            <div class="pr-ann">
                                <div class="pr-ann-top">
                                    <span class="pr-cat">{{ $ann->category ?? 'Umum' }}</span>
                                    <span class="pr-date">{{ $ann->date ?? $ann->created_at->format('d M Y') }}</span>
                                </div>
                                <h3>{{ $ann->title }}</h3>
                                <p>{{ $ann->content }}</p>
                            </div>
                        @empty
                            <div class="pr-empty">
                                Belum ada pengumuman khusus orang tua saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- MODAL FORM UNGGAH SURAT IZIN / SAKIT --}}
                <div id="modalPermission" class="pr-modal hidden">
                    <div class="pr-modal-box">
                        <div class="pr-modal-head">
                            <h3>Ajukan Surat Izin / Sakit</h3>
                            <button type="button" onclick="document.getElementById('modalPermission').classList.add('hidden')" class="pr-modal-x">✕</button>
                        </div>
                        <form action="{{ route('parent.permission.store') }}" method="POST" enctype="multipart/form-data" class="pr-form">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <div>
                                <label>Jenis Permohonan</label>
                                <select name="type" required class="pr-input">
                                    <option value="Sakit">Sakit</option>
                                    <option value="Izin">Izin</option>
                                </select>
                            </div>
                            <div class="pr-row2">
                                <div>
                                    <label>Mulai Tanggal</label>
                                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="pr-input">
                                </div>
                                <div>
                                    <label>Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="{{ date('Y-m-d') }}" required class="pr-input">
                                </div>
                            </div>
                            <div>
                                <label>Alasan / Keterangan</label>
                                <textarea name="reason" rows="3" required placeholder="Tuliskan alasan tidak hadir..." class="pr-input"></textarea>
                            </div>
                            <div>
                                <label>Unggah Surat Dokter / Surat Izin (JPG, PNG, PDF max 2MB)</label>
                                <input type="file" name="letter_file" accept=".jpg,.jpeg,.png,.pdf" required class="pr-input pr-file">
                            </div>
                            <div class="pr-act">
                                <button type="button" onclick="document.getElementById('modalPermission').classList.add('hidden')" class="pr-cancel">Batal</button>
                                <button type="submit" class="pr-send">Kirim Surat</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>