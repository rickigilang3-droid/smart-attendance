<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-950/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-indigo-200 text-xs font-semibold backdrop-blur-md mb-3 border border-white/10">
                        <span>⏱️</span> PORTAL ORANG TUA / WALI SISWA
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">
                        Riwayat Tap Gerbang Sekolah (Smart Gate)
                    </h1>
                    <p class="text-indigo-200 text-xs md:text-sm mt-1 max-w-2xl">
                        Pantau seluruh riwayat jam masuk dan kepulangan anak Anda secara langsung saat melakukan scan kartu / sidik jari di gerbang utama.
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

            <!-- Tabel Riwayat Tap Gerbang -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Log Kehadiran Gerbang</h2>
                        <p class="text-xs text-slate-500">Daftar transaksi absensi gerbang urut dari yang terbaru</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="px-6 py-4">No</th>
                                <th class="px-6 py-4">Tanggal & Hari</th>
                                <th class="px-6 py-4">Jam Tap</th>
                                <th class="px-6 py-4">Tipe Tap</th>
                                <th class="px-6 py-4">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @forelse($gateLogs as $index => $log)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4 font-bold text-slate-400">
                                        {{ $gateLogs->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-800 block">
                                            {{ \Carbon\Carbon::parse($log->created_at)->locale('id')->isoFormat('D MMMM YYYY') }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ \Carbon\Carbon::parse($log->created_at)->locale('id')->isoFormat('dddd') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600 text-sm">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }} WIB
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(strtolower($log->type ?? $log->tap_type ?? 'masuk') == 'masuk')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                                <span>📥</span> Tap Masuk
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200/60">
                                                <span>📤</span> Tap Pulang
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $status = $log->status ?? 'Tepat Waktu';
                                        @endphp
                                        @if(in_array(strtolower($status), ['tepat waktu', 'hadir', 'on time']))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                                                ✓ {{ $status }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700">
                                                ⚠️ {{ $status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-semibold">
                                        <div class="text-3xl mb-2">⏱️</div>
                                        Belum ada riwayat pindaian tap gerbang untuk siswa ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($gateLogs->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $gateLogs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>