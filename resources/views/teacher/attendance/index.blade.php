<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans text-gray-800" x-data="tdkModalHandler()">
        <div class="max-w-7xl mx-auto space-y-6">
            
            {{-- Tombol Navigasi Kembali & Informasi Tanggal --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-4 py-2.5 rounded-xl border border-slate-200/80 shadow-sm transition">
                        ← Kembali ke Dashboard
                    </a>
                </div>
                <div class="text-xs font-semibold text-slate-500 bg-white px-4 py-2 rounded-xl border border-slate-200/80 shadow-sm">
                    Tanggal Hari Ini: <span class="text-emerald-700 font-bold">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
                </div>
            </div>

            {{-- Alert Notifikasi Sukses --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
                </div>
            @endif

            {{-- Alert Notifikasi Error --}}
            @if(session('error'))
                <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 rounded-xl text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🚨</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">✕</button>
                </div>
            @endif

            {{-- Evaluasi Real-Time Keterlambatan Pembukaan Kelas --}}
            @php
                $currentTime = \Carbon\Carbon::now('Asia/Jakarta')->format('H:i');
                $startTimeThreshold = '07:15';
                $isSystemLate = $currentTime > $startTimeThreshold;
                $isUrlLate = request('class_status') == 'Terlambat Buka Kelas' || session('warning');
                $isLate = $isSystemLate || $isUrlLate;
                $openTime = request('opened_at', $currentTime);
            @endphp

            {{-- Banner Status Buka Kelas & Peringatan Terlambat --}}
            <div class="p-4 rounded-2xl text-xs font-bold flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs transition-all {{ $isLate ? 'bg-rose-100 border border-rose-300 text-rose-900' : 'bg-emerald-100 border border-emerald-300 text-emerald-900' }}">
                <div class="flex items-center gap-3">
                    <span class="text-xl">{{ $isLate ? '🚨' : '🎉' }}</span>
                    <div>
                        <span class="block font-extrabold text-sm">
                            Status Buka Kelas: {{ $isLate ? 'Terlambat Buka Kelas' : 'Tepat Waktu' }}
                        </span>
                        <span class="text-[11px] font-medium opacity-90">
                            Sesi kelas dibuka pada pukul <strong>{{ $openTime }} WIB</strong> {{ $isLate ? '(Melewati batas toleransi ' . $startTimeThreshold . ' WIB)' : '' }}.
                        </span>
                        
                        {{-- Preview Alasan Telat yang Sudah Diisi --}}
                        <template x-if="lateReason">
                            <div class="mt-1 text-[11px] text-rose-800 font-semibold bg-white/80 border border-rose-200 px-2.5 py-1 rounded-lg inline-block">
                                💬 Alasan Klarifikasi: <span x-text="lateReason"></span>
                            </div>
                        </template>
                    </div>
                </div>
                @if($isLate)
                    <button type="button" @click="showLateModal = true" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold border border-rose-700 shadow-sm transition flex items-center gap-1.5 cursor-pointer self-end sm:self-auto">
                        ✏️ <span x-text="lateReason ? 'Ubah Alasan Telat' : 'Isi Alasan Telat'"></span>
                    </button>
                @endif
            </div>

            {{-- Banner Informasi Utama --}}
            <div class="bg-emerald-50 border-l-4 border-emerald-600 rounded-r-2xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-emerald-900">Pencatatan Presensi & Berita Acara Mengajar</h2>
                    <p class="text-xs text-emerald-700 mt-0.5">Lakukan pencatatan absensi harian siswa serta lengkapi jurnal berita acara dan unggah foto kegiatan pembelajaran.</p>
                </div>
                <div class="text-xs bg-emerald-600 text-white font-semibold px-4 py-2 rounded-xl shadow-sm text-center">
                    {{ \Carbon\Carbon::parse($attendanceDate ?? date('Y-m-d'))->isoFormat('D MMMM Y') }}
                </div>
            </div>

            {{-- Form Filter Kelas & Tanggal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <form id="filterForm" method="GET" action="{{ route('teacher.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="class_code" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Kelas</label>
                        <select id="class_code" name="class_code" class="block w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5">
                            @if(isset($classes) && count($classes) > 0)
                                @foreach($classes as $code => $name)
                                    <option value="{{ $code }}" @selected(($selectedClass ?? '') == $code)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">Semua Kelas</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Absensi</label>
                        <input id="date" type="date" name="date" value="{{ $attendanceDate ?? date('Y-m-d') }}" class="block w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-2.5" />
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Tampilkan Data Siswa
                        </button>
                    </div>
                </form>
            </div>

            {{-- Form Lembar Presensi & Berita Acara Mengajar --}}
            <form method="POST" action="{{ route('teacher.attendance.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="class_code" value="{{ $selectedClass ?? '' }}">
                <input type="hidden" name="school_class_id" value="{{ $selectedClassModel->id ?? '' }}">
                <input type="hidden" name="date" value="{{ $attendanceDate ?? date('Y-m-d') }}">
                
                {{-- Input Hidden Klarifikasi Keterlambatan Buka Kelas --}}
                <input type="hidden" name="late_reason" x-model="lateReason">
                <input type="hidden" name="opened_at" value="{{ $openTime }}">
                <input type="hidden" name="class_status" value="{{ $isLate ? 'Terlambat Buka Kelas' : 'Tepat Waktu' }}">

                {{-- Section Berita Acara Mengajar & Bukti Foto KBM --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold text-sm">📝</span>
                            <div>
                                <h3 class="font-bold text-sm text-gray-800">Berita Acara Mengajar (Jurnal Harian Guru)</h3>
                                <p class="text-xs text-gray-500">Isi pokok materi pembelajaran dan unggah dokumentasi kegiatan mengajar hari ini.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Textarea Tambahan Alasan Telat di Form Utama jika Terlambat --}}
                    @if($isLate)
                        <div class="p-3.5 bg-rose-50/60 border border-rose-200 rounded-2xl space-y-1.5">
                            <label class="block text-xs font-bold text-rose-800 uppercase tracking-wider">
                                🚨 Alasan Keterlambatan Membuka Kelas <span class="text-rose-600 font-extrabold">*</span>
                            </label>
                            <textarea x-model="lateReason" rows="2" placeholder="Tuliskan penyebab keterlambatan pembukaan kelas..." class="w-full text-xs rounded-xl border-rose-300 focus:border-rose-500 focus:ring-rose-500 p-2.5 bg-white"></textarea>
                            <p class="text-[10px] text-rose-600 italic">Alasan ini akan langsung dilaporkan dan dipantau oleh Kepala Sekolah.</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                            <input type="text" name="subject" value="{{ old('subject', $journal->subject ?? '') }}" placeholder="Contoh: Matematika / Fisika" class="w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Materi / Pokok Pembahasan</label>
                            <input type="text" name="topic" value="{{ old('topic', $journal->topic ?? '') }}" placeholder="Contoh: Bab 3 - Trigonometri Dasar" class="w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Ringkasan Kegiatan Pembelajaran (Catatan)</label>
                            <textarea name="activities_summary" rows="3" placeholder="Tuliskan jalannya kegiatan kelas, diskusi kelompok, atau evaluasi pembelajaran..." class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">{{ old('activities_summary', $journal->activities_summary ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Foto Bukti Kegiatan Mengajar</label>
                            <div class="space-y-2">
                                <input type="file" name="photo_evidence" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-300 rounded-xl">
                                @if(isset($journal) && $journal->photo_evidence)
                                    <div class="flex items-center gap-2 mt-2 p-2 bg-slate-50 border border-slate-200 rounded-xl">
                                        <a href="{{ storage_media_url($journal->photo_evidence) }}" target="_blank" class="block flex-shrink-0">
                                            <img src="{{ storage_media_url($journal->photo_evidence) }}" class="w-12 h-12 object-cover rounded-lg border shadow-sm hover:scale-105 transition" title="Klik untuk memperbesar">
                                        </a>
                                        <div class="text-[11px] text-slate-600">
                                            <span class="font-bold text-emerald-600 block">✓ Foto Tersimpan</span>
                                            <a href="{{ storage_media_url($journal->photo_evidence) }}" target="_blank" class="text-indigo-600 hover:underline">Lihat Gambar</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Tabel Presensi Siswa --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-sm text-gray-800">Daftar Siswa Kelas</h3>
                            <p class="text-xs text-gray-500">Total Terdaftar: {{ isset($students) ? count($students) : 0 }} Siswa</p>
                        </div>
                        <div class="text-xs font-semibold flex flex-wrap items-center gap-2 sm:gap-3">
                            <span class="text-gray-500">Keterangan Status Kelas:</span>
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded">H: Hadir</span>
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded">S: Sakit</span>
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded">I: Izin</span>
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded">A: Alpha</span>
                            <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded">TDK: Tidak di Kelas</span>
                        </div>
                    </div>

                    {{-- Tabel Nama Siswa --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-slate-100 uppercase font-bold text-gray-700 tracking-wider text-[11px] border-b border-slate-200">
                                <tr>
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4 w-28">NIM / NIS</th>
                                    <th class="py-3.5 px-6">Nama Lengkap Siswa</th>
                                    <th class="py-3.5 px-4 text-center w-12">L/P</th>
                                    <th class="py-3.5 px-4 text-center whitespace-nowrap">Status Gerbang (Gate)</th>
                                    <th class="py-3.5 px-6 text-center whitespace-nowrap">Status Kehadiran Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($students ?? [] as $idx => $s)
                                    @php
                                        $studentId     = is_array($s) ? ($s['id'] ?? $idx) : ($s->id ?? $idx);
                                        $studentNis    = is_array($s) ? ($s['nis'] ?? $s['nim'] ?? '-') : ($s->nis ?? $s->nim ?? '-');
                                        $studentName   = is_array($s) ? ($s['name'] ?? '-') : ($s->name ?? '-');
                                        $studentGender = is_array($s) ? ($s['gender'] ?? 'L') : ($s->gender ?? 'L');
                                        
                                        // Deteksi Surat Izin/Sakit dari Ortu
                                        $permit        = $approvedPermits[$studentId] ?? (is_object($s) && isset($s->classAttendance->permit) ? $s->classAttendance->permit : null);
                                        
                                        // Status tersimpan
                                        $currentStatus = $savedAttendances[$studentId] ?? $existingAttendances[$studentId] ?? null;
                                        if (!$currentStatus && $permit) {
                                            $currentStatus = $permit->type ?? $permit->permission_type; 
                                        }
                                        $gateLog = isset($gateLogs) && isset($gateLogs[$studentId]) ? $gateLogs[$studentId] : null;
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3.5 px-4 text-center font-bold text-gray-400">{{ $idx + 1 }}</td>
                                        <td class="py-3.5 px-4 font-mono font-semibold text-gray-700">{{ $studentNis }}</td>
                                        <td class="py-3.5 px-6">
                                            <div class="font-bold text-gray-800">{{ $studentName }}</div>
                                            
                                            {{-- Indikator Surat Izin/Sakit Permanen --}}
                                            @if($permit)
                                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ ($permit->type ?? $permit->permission_type) == 'Sakit' ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                                        📄 {{ $permit->type ?? $permit->permission_type }} (Dari Ortu)
                                                    </span>
                                                    @if($permit->attachment_path || $permit->letter_file || $permit->attachment_photo || $permit->attachment)
                                                        <a href="{{ storage_media_url($permit->attachment_path ?? $permit->letter_file ?? $permit->attachment_photo ?? $permit->attachment) }}" 
                                                           target="_blank" 
                                                           class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold underline inline-flex items-center gap-1">
                                                            🔍 lihat surat
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                            {{-- Indikator Alasan TDK --}}
                                            <div x-show="notes['{{ $studentId }}']" class="text-[10px] text-purple-700 font-semibold bg-purple-50 border border-purple-200 px-2 py-0.5 rounded-md inline-block mt-1">
                                                📌 Alasan TDK: <span x-text="notes['{{ $studentId }}']"></span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ strtoupper($studentGender) == 'L' ? 'bg-sky-100 text-sky-700' : 'bg-pink-100 text-pink-700' }}">
                                                {{ strtoupper($studentGender) }}
                                            </span>
                                        </td>
                                        
                                        {{-- Indikator Status Gerbang (Gate) --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($gateLog)
                                                @if($gateLog->scan_out_at)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                                        Pulang ({{ \Carbon\Carbon::parse($gateLog->scan_out_at)->format('H:i') }} WIB)
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        Di Sekolah ({{ \Carbon\Carbon::parse($gateLog->created_at ?? $gateLog->scan_in_at)->format('H:i') }} WIB)
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                                    Belum Tap Gerbang
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-6 whitespace-nowrap">
                                            {{-- Input Hidden Alasan TDK --}}
                                            <input type="hidden" name="notes[{{ $studentId }}]" x-model="notes['{{ $studentId }}']">
                                            
                                            <div class="flex items-center justify-center gap-1.5">
                                                {{-- Option Hadir --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $studentId }}]" value="Hadir" @checked($currentStatus == 'Hadir' || $currentStatus == 'H') class="peer hidden" />
                                                    <span class="px-2.5 py-1.5 rounded-lg border border-slate-200 font-bold text-[11px] text-slate-500 bg-slate-50 hover:bg-slate-100 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 transition block shadow-sm">
                                                        ✓ Hadir
                                                    </span>
                                                </label>
                                                {{-- Option Sakit --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $studentId }}]" value="Sakit" @checked($currentStatus == 'Sakit' || $currentStatus == 'S') class="peer hidden" />
                                                    <span class="px-2.5 py-1.5 rounded-lg border border-slate-200 font-bold text-[11px] text-slate-500 bg-slate-50 hover:bg-slate-100 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition block shadow-sm">
                                                        + Sakit
                                                    </span>
                                                </label>
                                                {{-- Option Izin --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $studentId }}]" value="Izin" @checked($currentStatus == 'Izin' || $currentStatus == 'I') class="peer hidden" />
                                                    <span class="px-2.5 py-1.5 rounded-lg border border-slate-200 font-bold text-[11px] text-slate-500 bg-slate-50 hover:bg-slate-100 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 transition block shadow-sm">
                                                        📩 Izin
                                                    </span>
                                                </label>
                                                {{-- Option Alpha --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $studentId }}]" value="Alpa" @checked($currentStatus == 'Alpa' || $currentStatus == 'Alpha' || $currentStatus == 'A') class="peer hidden" />
                                                    <span class="px-2.5 py-1.5 rounded-lg border border-slate-200 font-bold text-[11px] text-slate-500 bg-slate-50 hover:bg-slate-100 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 transition block shadow-sm">
                                                        ✕ Alpha
                                                    </span>
                                                </label>
                                                {{-- Option TDK --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $studentId }}]" value="TDK" @checked($currentStatus == 'TDK') class="peer hidden" />
                                                    <span @click="openModal('{{ $studentId }}', '{{ addslashes($studentName) }}')" class="px-2.5 py-1.5 rounded-lg border border-slate-200 font-bold text-[11px] text-slate-500 bg-slate-50 hover:bg-slate-100 peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 transition block shadow-sm">
                                                        ⚠️ TDK
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                            Tidak ada data siswa ditemukan untuk kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Table Footer Action --}}
                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-md transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan Presensi & Berita Acara
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Pop-Up Modal Keterangan TDK --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Alasan Siswa Tidak Ada di Kelas</h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>
                
                <p class="text-xs text-slate-600">
                    Siswa: <span class="font-bold text-slate-800" x-text="activeStudentName"></span>
                </p>
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase">Pilih Alasan Cepat:</label>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" @click="tempNote = 'Sedang Berada di UKS'" class="text-[11px] bg-slate-100 hover:bg-purple-100 hover:text-purple-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium transition">🏥 UKS</button>
                        <button type="button" @click="tempNote = 'Izin Rapat / Kegiatan OSIS'" class="text-[11px] bg-slate-100 hover:bg-purple-100 hover:text-purple-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium transition">🏛️ Rapat OSIS</button>
                        <button type="button" @click="tempNote = 'Dispensasi Lomba Sekolah'" class="text-[11px] bg-slate-100 hover:bg-purple-100 hover:text-purple-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium transition">🏆 Dispen Lomba</button>
                        <button type="button" @click="tempNote = 'Panggilan Ruang BK / Konseling'" class="text-[11px] bg-slate-100 hover:bg-purple-100 hover:text-purple-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium transition">📋 Ruang BK</button>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Atau Tuliskan Keterangan Khusus:</label>
                    <textarea x-model="tempNote" rows="3" placeholder="Contoh: Mengikuti susulan ujian matematika di perpustakaan..." class="w-full text-xs rounded-xl border-slate-300 focus:border-purple-500 focus:ring-purple-500 p-2.5"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                    <button type="button" @click="saveNote()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow transition">Simpan Keterangan</button>
                </div>
            </div>
        </div>

        {{-- Pop-Up Modal Klarifikasi Keterlambatan Buka Kelas --}}
        <div x-show="showLateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-rose-200 max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🚨</span>
                        <h3 class="text-sm font-bold text-slate-800">Klarifikasi Keterlambatan Buka Kelas</h3>
                    </div>
                    <button type="button" @click="showLateModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>
                
                <p class="text-xs text-slate-600">
                    Sesi kelas dibuka pukul <strong class="text-rose-600">{{ $openTime }} WIB</strong> (Terlambat). Silakan berikan alasan klarifikasi untuk laporan Kepala Sekolah.
                </p>
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase">Pilih Alasan Cepat:</label>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" @click="tempLateReason = 'Piket / Pengawasan di Gerbang'" class="text-[11px] bg-slate-100 hover:bg-rose-100 hover:text-rose-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium transition">🏫 Piket Gerbang</button>
                        <button type="button" @click="tempLateReason = 'Rapat Mendadak dengan Kurikulum'" class="text-[11px] bg-slate-100 hover:bg-rose-100 hover:text-rose-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium transition">🏛️ Rapat Guru</button>
                        <button type="button" @click="tempLateReason = 'Kendala Lampu / Proyektor Kelas'" class="text-[11px] bg-slate-100 hover:bg-rose-100 hover:text-rose-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium transition">🔌 Kendala Fasilitas</button>
                        <button type="button" @click="tempLateReason = 'Penanganan Siswa Bermasalah / BK'" class="text-[11px] bg-slate-100 hover:bg-rose-100 hover:text-rose-700 px-2.5 py-1 rounded-lg border border-slate-200 font-medium transition">📋 Penanganan BK</button>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Atau Tuliskan Alasan Klarifikasi:</label>
                    <textarea x-model="tempLateReason" rows="3" placeholder="Tuliskan penyebab terlambat membuka kelas..." class="w-full text-xs rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500 p-2.5"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" @click="showLateModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">Batal</button>
                    <button type="button" @click="saveLateReason()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow transition">Simpan Alasan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Handler untuk Modal & Auto-Submit Filter --}}
    <script>
        function tdkModalHandler() {
            return {
                showModal: false,
                showLateModal: false,
                activeStudentId: null,
                activeStudentName: '',
                tempNote: '',
                lateReason: '{{ old("late_reason", $journal->late_reason ?? "") }}',
                tempLateReason: '{{ old("late_reason", $journal->late_reason ?? "") }}',
                notes: @json($savedNotes ?? []),
                init() {
                    this.showModal = false;
                    // Buka otomatis modal klarifikasi jika terdeteksi terlambat dan belum pernah diisi alasannya
                    const isLateSession = {{ $isLate ? 'true' : 'false' }};
                    if (isLateSession && !this.lateReason) {
                        this.showLateModal = true;
                    } else {
                        this.showLateModal = false;
                    }
                },
                openModal(studentId, studentName) {
                    this.activeStudentId = studentId;
                    this.activeStudentName = studentName;
                    this.tempNote = this.notes[studentId] || '';
                    this.showModal = true;
                },
                saveNote() {
                    if (this.activeStudentId) {
                        this.notes[this.activeStudentId] = this.tempNote;
                    }
                    this.showModal = false;
                },
                saveLateReason() {
                    this.lateReason = this.tempLateReason;
                    this.showLateModal = false;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const classSelect = document.getElementById('class_code');
            const dateInput = document.getElementById('date');
            const filterForm = document.getElementById('filterForm');
            if (classSelect && filterForm) {
                classSelect.addEventListener('change', function() {
                    filterForm.submit();
                });
            }
            if (dateInput && filterForm) {
                dateInput.addEventListener('change', function() {
                    filterForm.submit();
                });
            }
        });
    </script>
</x-app-layout>