<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header Utama -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <h1 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                    <span>Jurnal & Rekap Kehadiran KBM</span> 📄
                </h1>
                <p class="text-xs text-slate-500 mt-1">Riwayat rekapan presensi siswa yang Anda ampu saat jam pelajaran.</p>
            </div>

            <!-- Tabel Rekap Presensi -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-50/80">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Tanggal</th>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 rounded-r-xl">Catatan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($reports as $report)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 text-xs font-bold text-slate-500">{{ \Carbon\Carbon::parse($report->date)->format('d M Y') }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $report->student->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold">
                                        {{ $report->schoolClass->name ?? $report->schoolClass->class_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg 
                                        {{ in_array($report->status, ['Hadir', 'H']) ? 'bg-emerald-50 text-emerald-600' : '' }}
                                        {{ in_array($report->status, ['Izin', 'I']) ? 'bg-amber-50 text-amber-600' : '' }}
                                        {{ in_array($report->status, ['Sakit', 'S']) ? 'bg-blue-50 text-blue-600' : '' }}
                                        {{ in_array($report->status, ['Alpa', 'A']) ? 'bg-rose-50 text-rose-600' : '' }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $report->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 text-xs">
                                    Belum ada rekap jurnal kehadiran KBM yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>