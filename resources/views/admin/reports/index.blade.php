<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Header -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800">Rekap & Laporan Kehadiran 📄</h1>
                    <p class="text-xs text-slate-500 mt-1">Filter dan cetak laporan presensi siswa dalam format PDF atau Excel.</p>
                </div>
            </div>

            <!-- Form Filter Data -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Kelas</label>
                        <select name="class_id" class="w-full text-xs rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Kelas</option>
                            @if(isset($classes) && (is_array($classes) || is_object($classes)))
                                @foreach($classes as $c)
                                    <option value="{{ is_object($c) ? $c->id : $c['id'] }}" {{ $classId == (is_object($c) ? $c->id : $c['id']) ? 'selected' : '' }}>
                                        {{ is_object($c) ? $c->name : $c['name'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                            🔍 Filter
                        </button>
                    </div>
                </form>

                <!-- Tombol Export Excel & PDF -->
                <div class="flex gap-3 mt-4 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.reports.excel', ['start_date' => $startDate, 'end_date' => $endDate, 'class_id' => $classId]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-sm transition">
                        📊 Export Excel (.xlsx)
                    </a>
                    <a href="{{ route('admin.reports.pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'class_id' => $classId]) }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-sm transition" target="_blank">
                        📄 Export PDF Siap Cetak
                    </a>
                </div>
            </div>

            <!-- Tabel Data Absensi -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800">Hasil Rekapitulasi Presensi</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px]">
                            <tr>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">NISN</th>
                                <th class="p-4">Nama Siswa</th>
                                <th class="p-4">Kelas</th>
                                <th class="p-4">Jam Masuk</th>
                                <th class="p-4">Jam Pulang</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($attendances as $log)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="p-4 font-semibold text-slate-800">{{ $log->date }}</td>
                                    <td class="p-4">{{ $log->student->nisn ?? '-' }}</td>
                                    <td class="p-4 font-bold text-slate-800">{{ $log->student->name ?? '-' }}</td>
                                    <td class="p-4">{{ $log->student->schoolClass->name ?? '-' }}</td>
                                    <td class="p-4 text-emerald-600 font-semibold">{{ $log->scan_in ?? '-' }} WIB</td>
                                    <td class="p-4 text-amber-600 font-semibold">{{ $log->scan_out ?? '-' }} WIB</td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-md bg-emerald-100 text-emerald-700 font-bold text-[10px]">
                                            HADIR
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-400">
                                        Tidak ada data kehadiran pada periode tanggal & kelas yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>