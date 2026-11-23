<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- HEADER & TITLE -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <span>📩</span> Izin & Sakit Siswa
                    </h1>
                    <p class="text-sm font-medium text-slate-500 mt-1">Daftar permohonan surat izin dan sakit siswa yang diajukan oleh Orang Tua / Wali Murid</p>
                </div>
                <div>
                    <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition shadow-sm">
                        <span>←</span> Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- FLASH NOTIFICATION -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-2xl flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold rounded-2xl flex items-center gap-2">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- TABEL SURAT IZIN & SAKIT -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-medium text-slate-600">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-extrabold text-[10px] tracking-wider border-y border-slate-100">
                            <tr>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">Kelas</th>
                                <th class="py-3 px-4">Jenis Permohonan</th>
                                <th class="py-3 px-4">Tanggal Izin</th>
                                <th class="py-3 px-4">Alasan / Catatan</th>
                                <th class="py-3 px-4 text-center">Lampiran Surat</th>
                                <th class="py-3 px-4 text-center">Aksi / Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($permissions as $permit)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-4 px-4 font-bold text-slate-800">
                                        {{ $permit->student->name ?? 'Siswa' }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg font-bold">
                                            {{ $permit->student->schoolClass->name ?? $permit->student->school_class->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if(strtolower($permit->type ?? $permit->permission_type ?? 'izin') == 'sakit')
                                            <span class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-lg font-extrabold inline-flex items-center gap-1">
                                                <span>🤒</span> Sakit
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-lg font-extrabold inline-flex items-center gap-1">
                                                <span>✉️</span> Izin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-bold text-slate-700">
                                        {{ \Carbon\Carbon::parse($permit->start_date ?? $permit->permission_date ?? $permit->created_at)->format('d M Y') }}
                                        @if(isset($permit->end_date) && $permit->end_date != $permit->start_date)
                                            s/d {{ \Carbon\Carbon::parse($permit->end_date)->format('d M Y') }}
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-slate-600 max-w-xs truncate" title="{{ $permit->reason ?? $permit->notes ?? '-' }}">
                                        {{ $permit->reason ?? $permit->notes ?? '-' }}
                                    </td>
                                    
                                    <!-- LAMPIRAN SURAT FLEKSIBEL -->
                                    <td class="py-4 px-4 text-center">
                                        @php
                                            $filePath = $permit->attachment_photo 
                                                     ?? $permit->attachment 
                                                     ?? $permit->letter_file 
                                                     ?? $permit->file 
                                                     ?? $permit->image_path 
                                                     ?? null;
                                        @endphp

                                        @if($filePath)
                                            <a href="{{ storage_media_url($filePath) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-bold underline">
                                                <span>📄</span> Lihat Lampiran
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic">Tanpa Lampiran</span>
                                        @endif
                                    </td>

                                    <!-- AKSI / STATUS DENGAN KONDISI PERSETUJUAN GURU -->
                                    <td class="py-4 px-4 text-center">
                                        @php
                                            $isApproved = ($permit->teacher_status ?? $permit->status ?? 'Pending') === 'Approved';
                                        @endphp

                                        @if($isApproved)
                                            <span class="inline-flex items-center gap-1 px-3.5 py-2 bg-emerald-100 text-emerald-800 border border-emerald-300 font-extrabold rounded-xl text-xs shadow-sm">
                                                <span>✓</span> Disetujui
                                            </span>
                                        @else
                                            <form action="{{ route('teacher.permissions.verify', $permit->id) }}" method="POST" class="inline-flex gap-1">
                                                @csrf
                                                <input type="hidden" name="status" value="Approved">
                                                <button type="submit" class="py-2 px-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs transition shadow-sm flex items-center gap-1 cursor-pointer">
                                                    <span>✓</span> Setujui
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <span class="text-3xl mb-2">📭</span>
                                            <p class="font-bold text-slate-600">Belum Ada Permohonan Izin / Sakit</p>
                                            <p class="text-xs mt-0.5">Surat pengajuan izin dari Orang Tua murid akan tampil di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($permissions, 'links'))
                    <div class="mt-4 border-t border-slate-100 pt-3">
                        {{ $permissions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>