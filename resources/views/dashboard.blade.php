<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Guru') }}
        </h2>
    </x-slot>

    <div class="py-12 font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert Success -->
            @if(session('success'))
                <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-2xl flex items-center justify-between shadow-sm">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. JADWAL MENGAJAR HARI INI & MODE BUKA KELAS -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                            <span>📅 Jadwal Mengajar Hari Ini</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Hari ini: <strong class="text-indigo-600">{{ $todayName ?? date('d M Y') }}</strong></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($todaySchedules as $schedule)
                        <div class="p-5 bg-slate-50/50 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4 hover:border-indigo-300 transition">
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 font-bold rounded-lg text-[10px] uppercase tracking-wider">
                                        {{ $schedule->schoolClass->name ?? $schedule->schoolClass->class_name ?? 'Kelas' }}
                                    </span>

                                    @if($schedule->is_completed)
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-lg flex items-center gap-1">
                                            ✓ Selesai
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-base font-extrabold text-slate-800 pt-1">{{ $schedule->subject }}</h4>
                                
                                <p class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                                    <span>⏱️</span>
                                    <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB</span>
                                </p>
                            </div>

                            <!-- Form Tombol Buka Kelas -->
                            <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between gap-2">
                                <span class="text-[10px] text-slate-400 font-medium">Sesi Pembelajaran:</span>
                                
                                <form action="{{ route('teacher.open-class') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                    <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                                        <span>🚀</span> Buka Kelas
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 text-xs space-y-1">
                            <p class="text-xl">☕</p>
                            <p class="font-bold text-slate-600">Tidak ada jadwal mengajar untuk hari ini.</p>
                            <p>Silakan periksa jadwal mengajar Anda untuk hari lainnya.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. WIDGET PERMOHONAN SURAT IZIN / SAKIT MASUK DARI ORANG TUA -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <span>📩</span> Permohonan Surat Sakit & Izin Siswa
                        @if(isset($pendingPermissions) && count($pendingPermissions) > 0)
                            <span class="px-2 py-0.5 bg-rose-500 text-white rounded-full text-[10px] font-bold">
                                {{ count($pendingPermissions) }} Baru
                            </span>
                        @endif
                    </h3>
                </div>

                @if(isset($pendingPermissions) && count($pendingPermissions) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 uppercase font-semibold">
                                <tr>
                                    <th class="p-3 rounded-l-xl">Nama Siswa</th>
                                    <th class="p-3">Kategori</th>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Alasan</th>
                                    <th class="p-3">Bukti Surat</th>
                                    <th class="p-3 rounded-r-xl text-center">Aksi Guru</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($pendingPermissions as $item)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="p-3 font-bold text-slate-800">{{ $item->student->name ?? '-' }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-1 rounded-lg font-bold text-[10px] {{ ($item->type ?? $item->permission_type) == 'Sakit' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $item->type ?? $item->permission_type ?? 'Izin' }}
                                            </span>
                                        </td>
                                        <td class="p-3 font-mono">{{ $item->start_date ?? $item->permission_date }} s/d {{ $item->end_date ?? $item->start_date }}</td>
                                        <td class="p-3">{{ $item->reason ?? $item->notes ?? '-' }}</td>
                                        <td class="p-3">
                                            @if($item->letter_file || $item->attachment_path)
                                                <a href="{{ storage_media_url(($item->letter_file ?? $item->attachment_path)) }}" target="_blank" class="text-indigo-600 hover:underline font-bold flex items-center gap-1">
                                                    📎 Lihat File
                                                </a>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('teacher.permission.verify', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Approved">
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition text-[11px]">
                                                        Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('teacher.permission.verify', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Rejected">
                                                    <button type="submit" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-lg transition text-[11px]">
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 bg-slate-50 rounded-xl text-center text-xs text-slate-400">
                        Belum ada pengajuan surat sakit/izin baru dari orang tua.
                    </div>
                @endif
            </div>

            <!-- 3. INFORMASI SELAMAT DATANG -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80">
                <div class="p-6 text-gray-900 text-xs space-y-1">
                    <p class="font-bold text-slate-800 text-sm">👋 Selamat datang di Portal Guru!</p>
                    <p class="text-slate-500">Silakan kelola jadwal, lakukan pembukaan kelas saat jam mengajar, dan rekapitulasi presensi siswa melalui menu navigasi yang tersedia.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>