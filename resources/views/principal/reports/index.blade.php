<x-app-layout>
    <div class="p-6 space-y-6">
        <!-- Header Halaman -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Laporan & Rekapitulasi Presensi 📊</h1>
                <p class="text-xs text-slate-500">Laporan rekapitulasi kehadiran siswa per kelas berdasarkan periode bulan dan tahun</p>
            </div>

            <!-- Filter Kelas, Bulan, dan Tahun -->
            <form method="GET" action="{{ route('principal.reports') }}" class="flex flex-wrap items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm">
                <!-- Filter Kelas -->
                <select name="class_id" class="text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-700 font-semibold">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ ($selectedClass->id ?? null) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Filter Bulan -->
                <select name="month" class="text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-700 font-semibold">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>

                <!-- Filter Tahun -->
                <select name="year" class="text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500 text-slate-700 font-semibold">
                    @foreach([2025, 2026, 2027] as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm">
                    🔍 Tampilkan
                </button>
            </form>
        </div>

        <!-- Tabel Rekapitulasi Presensi Siswa -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 pb-4 gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">
                        Rekapitulasi Kelas: <span class="text-indigo-600 font-extrabold">{{ $selectedClass->name ?? '-' }}</span>
                    </h2>
                    <p class="text-xs text-slate-400">
                        Periode: {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase font-semibold">
                            <th class="p-3">Nama Siswa</th>
                            <th class="p-3 text-center text-emerald-600">✅ Hadir</th>
                            <th class="p-3 text-center text-amber-600">😷 Sakit</th>
                            <th class="p-3 text-center text-blue-600">📩 Izin</th>
                            <th class="p-3 text-center text-rose-600">❌ Alpa</th>
                            <th class="p-3 text-center">Total Jam Recorded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $st)
                            @php
                                $r = $recap[$st->id] ?? ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0, 'total' => 0];
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="p-3 font-bold text-slate-800 whitespace-nowrap">{{ $st->name }}</td>
                                <td class="p-3 text-center font-bold text-emerald-600 bg-emerald-50/30 rounded-lg">{{ $r['hadir'] }}</td>
                                <td class="p-3 text-center font-bold text-amber-600 bg-amber-50/30 rounded-lg">{{ $r['sakit'] }}</td>
                                <td class="p-3 text-center font-bold text-blue-600 bg-blue-50/30 rounded-lg">{{ $r['izin'] }}</td>
                                <td class="p-3 text-center font-bold text-rose-600 bg-rose-50/30 rounded-lg">{{ $r['alpha'] }}</td>
                                <td class="p-3 text-center font-bold text-slate-700">{{ $r['total'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-400">Tidak ada data siswa atau presensi untuk kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>