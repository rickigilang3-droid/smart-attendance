<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-950/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-indigo-200 text-xs font-semibold backdrop-blur-md mb-3 border border-white/10">
                        <span>📊</span> PORTAL ORANG TUA / WALI SISWA
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">
                        Rekap Kehadiran Anak
                    </h1>
                    <p class="text-indigo-200 text-xs md:text-sm mt-1 max-w-2xl">
                        Pantau seluruh rekapan absensi harian dan presensi jam pelajaran siswa secara real-time.
                    </p>
                </div>

                @if($student)
                    <div class="flex items-center gap-4 bg-white/10 p-3 rounded-2xl backdrop-blur-md border border-white/10">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-12 h-12 rounded-xl object-cover border-2 border-white/20">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/30 flex items-center justify-center text-lg font-bold border-2 border-white/20">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-semibold text-indigo-200">Siswa Terhubung</p>
                            <p class="text-sm font-extrabold text-white">{{ $student->name }}</p>
                            <p class="text-[11px] text-indigo-200/80">NISN: {{ $student->nisn }} • Kelas: {{ $student->schoolClass->name ?? '-' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tabel Riwayat Kehadiran Pelajaran/Harian -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Daftar Presensi Pembelajaran</h2>
                        <p class="text-xs text-slate-500">Rekapitulasi status kehadiran anak pada jam pelajaran</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="px-6 py-4">No</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Status Absensi</th>
                                <th class="px-6 py-4">Catatan Guru</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($attendances as $index => $att)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4 font-bold text-slate-400">
                                        {{ method_exists($attendances, 'firstItem') ? $attendances->firstItem() + $index : $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-800 block">
                                            {{ \Carbon\Carbon::parse($att->date ?? $att->created_at)->locale('id')->isoFormat('D MMMM YYYY') }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ \Carbon\Carbon::parse($att->date ?? $att->created_at)->locale('id')->isoFormat('dddd') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $st = strtolower($att->status ?? 'hadir');
                                        @endphp
                                        @if(in_array($st, ['hadir', 'h']))
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-700">
                                                ✓ Hadir
                                            </span>
                                        @elseif(in_array($st, ['sakit', 's']))
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-700">
                                                💊 Sakit
                                            </span>
                                        @elseif(in_array($st, ['izin', 'i']))
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-700">
                                                ✉️ Izin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-700">
                                                ✕ Alpha / Tanpa Keterangan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $att->notes ?? $att->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-semibold">
                                        <div class="text-3xl mb-2">📊</div>
                                        Belum ada rekapan absensi pelajaran untuk siswa ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($attendances, 'hasPages') && $attendances->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>