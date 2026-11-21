<x-app-layout>
    <div x-data="{ activeTab: '{{ request('tab', 'gate') }}' }" class="p-6 space-y-6">
        
        <!-- Header & Filter Periode -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Kedisiplinan & Rekap Absensi Guru 👨‍🏫</h1>
                <p class="text-xs text-slate-500">Pemantauan absensi tap gerbang (Smart Gate) dan ketepatan waktu guru membuka kelas</p>
            </div>

            <!-- Filter Tanggal Harian & Bulan/Tahun -->
            <form method="GET" action="{{ route('principal.discipline') }}" class="flex flex-wrap items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm">
                <input type="hidden" name="tab" :value="activeTab">
                
                <div class="flex items-center gap-1.5 border-r border-slate-200 pr-2">
                    <span class="text-[11px] font-bold text-slate-500 pl-1">Tgl Log:</span>
                    <input type="date" name="date" value="{{ $selectedDate }}" class="text-xs px-2.5 py-1.5 rounded-xl border-slate-200 focus:ring-indigo-500">
                </div>

                <select name="month" class="text-xs px-3 py-1.5 rounded-xl border-slate-200 focus:ring-indigo-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <select name="year" class="text-xs px-3 py-1.5 rounded-xl border-slate-200 focus:ring-indigo-500">
                    @foreach([2025, 2026, 2027] as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-700 transition-all shadow-xs">
                    Filter
                </button>
            </form>
        </div>

        <!-- Ringkasan Statistik Cepat (KPI Cards) -->
        @php
            $totalGateToday    = $dailyGateLogs->count();
            $punctualGateToday = $dailyGateLogs->whereIn('status', ['present', 'Tepat Waktu', 'TEPAT WAKTU', 'Hadir'])->count();
            $lateGateToday     = $dailyGateLogs->whereIn('status', ['late', 'Terlambat', 'TERLAMBAT'])->count();
            $totalMonthGate    = collect($disciplineRecap)->sum('gate_total');
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase">Tap Gerbang ({{ \Carbon\Carbon::parse($selectedDate)->format('d/m') }})</p>
                    <p class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $totalGateToday }} <span class="text-xs font-medium text-slate-500">Guru</span></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base">🪪</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase">Datang Tepat Waktu</p>
                    <p class="text-xl font-extrabold text-emerald-600 mt-0.5">{{ $punctualGateToday }} <span class="text-xs font-medium text-slate-500">Guru</span></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base">✅</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase">Terlambat Gerbang</p>
                    <p class="text-xl font-extrabold text-rose-600 mt-0.5">{{ $lateGateToday }} <span class="text-xs font-medium text-slate-500">Guru</span></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-base">⏰</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase">Total Tap Bulan Ini</p>
                    <p class="text-xl font-extrabold text-amber-600 mt-0.5">{{ $totalMonthGate }} <span class="text-xs font-medium text-slate-500">Kehadiran</span></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-base">📊</div>
            </div>
        </div>

        <!-- Tab Switcher -->
        <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl border border-slate-200 w-fit">
            <button type="button"
                @click="activeTab = 'gate'"
                :class="activeTab === 'gate' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                🪪 Rekap Absen Gerbang (Smart Gate)
            </button>
            <button type="button"
                @click="activeTab = 'class'"
                :class="activeTab === 'class' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                🏫 Rekap Buka Kelas & Jurnal KBM
            </button>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 1: REKAP ABSENSI GERBANG GURU (SMART GATE)            -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'gate'" class="space-y-6">
            
            <!-- 1A. Log Tap Gerbang Harian Guru -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Log Tap Gerbang Guru — {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</h2>
                        <p class="text-[11px] text-slate-500">Data otomatis dari Smart Gate Simulator (Batas jam masuk guru: 07:00 WIB)</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 w-fit">
                        {{ $dailyGateLogs->count() }} Catatan Tap
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b text-slate-500 uppercase font-semibold">
                                <th class="p-3">Nama Guru</th>
                                <th class="p-3">NIP</th>
                                <th class="p-3 text-center">Jam Tap Masuk</th>
                                <th class="p-3 text-center">Jam Tap Pulang</th>
                                <th class="p-3 text-right">Status Kedatangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dailyGateLogs as $log)
                                @php
                                    $isLateGate = in_array(strtolower($log->status), ['late', 'terlambat']);
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="p-3 font-bold text-slate-800">{{ $log->teacher->name ?? '-' }}</td>
                                    <td class="p-3 font-mono text-slate-500">{{ $log->teacher->nip ?? '-' }}</td>
                                    <td class="p-3 text-center font-mono font-bold text-slate-700">
                                        {{ $log->scan_in ? \Carbon\Carbon::parse($log->scan_in)->format('H:i:s') . ' WIB' : '-' }}
                                    </td>
                                    <td class="p-3 text-center font-mono text-slate-600">
                                        {{ $log->scan_out ? \Carbon\Carbon::parse($log->scan_out)->format('H:i:s') . ' WIB' : 'Belum Tap Pulang' }}
                                    </td>
                                    <td class="p-3 text-right">
                                        @if($isLateGate)
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700">
                                                ⚠️ Terlambat
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                                                ✅ Tepat Waktu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">
                                        Belum ada guru yang melakukan tap absen gerbang pada tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 1B. Rekapitulasi Bulanan Absensi Gerbang Per Guru -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-800">Rekap Bulanan Kehadiran Gerbang — {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</h2>
                    <p class="text-[11px] text-slate-500">Akumulasi ketepatan waktu kehadiran seluruh guru di gerbang sekolah</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b text-slate-500 uppercase font-semibold">
                                <th class="p-3">Nama Guru</th>
                                <th class="p-3">NIP</th>
                                <th class="p-3 text-center">Total Hari Hadir</th>
                                <th class="p-3 text-center">✅ Datang Tepat Waktu</th>
                                <th class="p-3 text-center">⚠️ Terlambat Masuk</th>
                                <th class="p-3 text-right">Persentase Tepat Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($disciplineRecap as $rec)
                                @php
                                    $gTotal = $rec['gate_total'] ?? 0;
                                    $gPunctual = $rec['gate_punctual_count'] ?? 0;
                                    $gLate = $rec['gate_late_count'] ?? 0;
                                    $gRate = $gTotal > 0 ? round(($gPunctual / $gTotal) * 100) : 100;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="p-3 font-bold text-slate-800">{{ $rec['name'] }}</td>
                                    <td class="p-3 font-mono text-slate-500">{{ $rec['nip'] }}</td>
                                    <td class="p-3 text-center font-semibold text-slate-700">{{ $gTotal }} Hari</td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold">
                                            {{ $gPunctual }} Hari
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-bold">
                                            {{ $gLate }} Hari
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <span class="px-3 py-1 rounded-xl text-xs font-extrabold {{ $gRate >= 85 ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">
                                            {{ $gRate }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-400">Belum ada data guru terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 2: REKAP BUKA KELAS & JURNAL KBM                      -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'class'" style="display: none;" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-800">Rekap Ketepatan Waktu Buka Kelas — {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</h2>
                <p class="text-[11px] text-slate-500">Evaluasi kedisiplinan guru membuka sesi KBM dan mengisi berita acara</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b text-slate-500 uppercase font-semibold">
                            <th class="p-3">Nama Guru</th>
                            <th class="p-3">NIP</th>
                            <th class="p-3 text-center">Total Jurnal Terisi</th>
                            <th class="p-3 text-center">⚡ Buka Tepat Waktu</th>
                            <th class="p-3 text-center">⚠️ Terlambat Buka Kelas</th>
                            <th class="p-3 text-right">Tingkat Kedisiplinan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($disciplineRecap as $rec)
                            @php
                                $total = $rec['total_journals'];
                                $rate = $total > 0 ? round(($rec['punctual_count'] / $total) * 100) : 100;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="p-3 font-bold text-slate-800">{{ $rec['name'] }}</td>
                                <td class="p-3 font-mono text-slate-500">{{ $rec['nip'] }}</td>
                                <td class="p-3 text-center font-semibold text-slate-700">{{ $rec['total_journals'] }} Jurnal</td>
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold">
                                        {{ $rec['punctual_count'] }} Kali
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-bold">
                                        {{ $rec['late_count'] }} Kali
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <span class="px-3 py-1 rounded-xl text-xs font-extrabold {{ $rate >= 85 ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">
                                        {{ $rate }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-400">Belum ada data kedisiplinan guru tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>