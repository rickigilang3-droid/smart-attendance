<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-950/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-indigo-200 text-xs font-semibold backdrop-blur-md mb-3 border border-white/10">
                        <span>✉️</span> PORTAL ORANG TUA / WALI SISWA
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">
                        Pengajuan Surat Izin & Sakit Digital
                    </h1>
                    <p class="text-indigo-200 text-xs md:text-sm mt-1 max-w-2xl">
                        Unggah permohonan izin atau surat keterangan sakit untuk anak Anda. Status konfirmasi dapat dipantau secara langsung oleh wali kelas.
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

            <!-- Notifikasi Pesan Sukses -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3">
                    <span class="text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Form Pengajuan Surat Izin / Sakit -->
                <div class="lg:col-span-1 bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 h-fit space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-extrabold text-slate-800">Form Permohonan Baru</h2>
                        <p class="text-[11px] text-slate-500">Isi data dan lampirkan bukti surat digital</p>
                    </div>

                    @if($student)
                        <form action="{{ route('parent.permissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-medium">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Tipe Halangan</label>
                                <select name="type" required class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Sakit">Sakit</option>
                                    <option value="Izin">Izin (Keperluan Keluarga/Lainnya)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Dari Tanggal</label>
                                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs font-semibold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Alasan Detail</label>
                                <textarea name="reason" rows="3" required placeholder="Tuliskan alasan izin/sakit siswa secara singkat..." class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Unggah Surat / Dokumen Bukti</label>
                                <input type="file" name="letter_file" accept=".jpg,.jpeg,.png,.pdf" required class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                                <span class="text-[10px] text-slate-400 mt-1 block">Format: JPG, PNG, PDF (Maks. 2MB)</span>
                            </div>

                            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl shadow-md transition text-xs flex items-center justify-center gap-2">
                                <span>📤</span> Kirim Pengajuan Surat
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-rose-500 font-bold py-4 text-center">Data siswa belum terhubung dengan akun Anda.</p>
                    @endif
                </div>

                <!-- Tabel Riwayat Pengajuan Surat -->
                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h2 class="text-base font-extrabold text-slate-800">Riwayat Pengajuan Surat</h2>
                        <p class="text-[11px] text-slate-500">Daftar permohonan yang telah dikirimkan beserta status verifikasi guru</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200/80">
                                <tr>
                                    <th class="px-6 py-4">Tipe & Tanggal</th>
                                    <th class="px-6 py-4">Alasan</th>
                                    <th class="px-6 py-4">Berkas</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                @forelse($permissions as $perm)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-6 py-4">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1 {{ $perm->type == 'Sakit' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $perm->type }}
                                            </span>
                                            <span class="block font-extrabold text-slate-800">
                                                {{ \Carbon\Carbon::parse($perm->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($perm->end_date)->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 max-w-xs text-slate-600">
                                            {{ $perm->reason }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($perm->letter_file)
                                                <a href="{{ asset('storage/' . $perm->letter_file) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                                    <span>📎</span> Lihat File
                                                </a>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $st = strtolower($perm->status ?? 'pending');
                                            @endphp
                                            @if(in_array($st, ['disetujui', 'approved', 'verified']))
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700">
                                                    ✓ Disetujui
                                                </span>
                                            @elseif(in_array($st, ['ditolak', 'rejected']))
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700">
                                                    ✕ Ditolak
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-700">
                                                    ⏳ Menunggu
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-semibold">
                                            <div class="text-3xl mb-2">✉️</div>
                                            Belum ada riwayat surat izin/sakit yang diajukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($permissions, 'hasPages') && $permissions->hasPages())
                        <div class="p-4 border-t border-slate-100">
                            {{ $permissions->links() }}
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>