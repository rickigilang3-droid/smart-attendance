<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans text-gray-800">
        <div class="max-w-7xl mx-auto space-y-6">
            
            {{-- Tombol Navigasi Kembali & Informasi --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('teacher.attendance.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-4 py-2.5 rounded-xl border border-slate-200/80 shadow-sm transition">
                        ← Kembali ke Lembar Presensi
                    </a>
                    <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-4 py-2.5 rounded-xl border border-slate-200/80 shadow-sm transition">
                        🏠 Dashboard Guru
                    </a>
                </div>
                <div class="text-xs font-semibold text-slate-500 bg-white px-4 py-2 rounded-xl border border-slate-200/80 shadow-sm">
                    Kelas Terpilih: <span class="text-purple-700 font-bold">{{ $selectedClassModel->name ?? 'Semua Kelas' }}</span>
                </div>
            </div>

            {{-- Banner Header --}}
            <div class="bg-gradient-to-r from-purple-700 to-indigo-800 rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Rekapitulasi Presensi & Berita Acara Mengajar</h2>
                    <p class="text-xs text-purple-100 mt-1">Laporan akumulasi bulanan dan log harian KBM beserta foto dokumentasi kegiatan kelas.</p>
                </div>
                <div class="text-xs bg-white/20 backdrop-blur-md text-white font-semibold px-4 py-2 rounded-xl border border-white/20 text-center">
                    Total Siswa: <span class="font-bold text-yellow-300">{{ $students->count() }} Siswa</span>
                </div>
            </div>

            @php
                $activeTab = request('view', $viewType ?? 'monthly');
            @endphp

            {{-- TAB SWITCHER NAVIGASI (BULANAN VS HARIAN) --}}
            <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-2">
                <a href="{{ route('teacher.attendance.recap', ['class_id' => $selectedClass, 'view' => 'monthly', 'month' => $selectedMonth, 'year' => $selectedYear]) }}" 
                   class="flex-1 text-center py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 {{ $activeTab === 'monthly' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                   📊 Ringkasan Bulanan
                </a>
                <a href="{{ route('teacher.attendance.recap', ['class_id' => $selectedClass, 'view' => 'daily', 'date' => $selectedDate ?? date('Y-m-d')]) }}" 
                   class="flex-1 text-center py-2.5 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 {{ $activeTab === 'daily' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                   📅 Log Harian & Berita Acara
                </a>
            </div>

            @if($activeTab === 'monthly')
                {{-- FORM FILTER BULANAN --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <form method="GET" action="{{ route('teacher.attendance.recap') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                        <input type="hidden" name="view" value="monthly">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Kelas</label>
                            <select name="class_id" class="block w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                                @foreach($classes as $id => $name)
                                    <option value="{{ $id }}" @selected($selectedClass == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bulan</label>
                            <select name="month" class="block w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                                @for($m = 1; $m <= 12; $m++)
                                    @php $mVal = sprintf('%02d', $m); @endphp
                                    <option value="{{ $mVal }}" @selected($selectedMonth == $mVal)>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->isoFormat('MMMM') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun</label>
                            <select name="year" class="block w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                    <option value="{{ $y }}" @selected($selectedYear == $y)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow transition">
                                Tampilkan Rekap
                            </button>
                        </div>
                    </form>
                </div>

                {{-- TABEL REKAP BULANAN --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="font-bold text-sm text-gray-800">Ringkasan Kehadiran Bulanan Siswa</h3>
                        <span class="text-xs text-slate-500">Periode: <strong>{{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->isoFormat('MMMM Y') }}</strong></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-slate-100 uppercase font-bold text-gray-700 tracking-wider text-[11px] border-b border-slate-200">
                                <tr>
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4 w-28">NISN / NIS</th>
                                    <th class="py-3.5 px-6">Nama Lengkap Siswa</th>
                                    <th class="py-3.5 px-4 text-center text-emerald-700 bg-emerald-50">Hadir</th>
                                    <th class="py-3.5 px-4 text-center text-blue-700 bg-blue-50">Sakit</th>
                                    <th class="py-3.5 px-4 text-center text-amber-700 bg-amber-50">Izin</th>
                                    <th class="py-3.5 px-4 text-center text-rose-700 bg-rose-50">Alpha</th>
                                    <th class="py-3.5 px-4 text-center text-purple-700 bg-purple-50">TDK</th>
                                    <th class="py-3.5 px-6 text-center">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($students as $idx => $s)
                                    @php
                                        $sRecap = $recapData[$s->id] ?? ['hadir'=>0, 'sakit'=>0, 'izin'=>0, 'alpha'=>0, 'tdk'=>0, 'total'=>0];
                                        $percent = $sRecap['total'] > 0 ? round(($sRecap['hadir'] / $sRecap['total']) * 100) : 100;
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3.5 px-4 text-center font-bold text-gray-400">{{ $idx + 1 }}</td>
                                        <td class="py-3.5 px-4 font-mono font-semibold text-gray-700">{{ $s->nisn ?? $s->nis ?? '-' }}</td>
                                        <td class="py-3.5 px-6 font-bold text-gray-800">{{ $s->name }}</td>
                                        <td class="py-3.5 px-4 text-center font-bold text-emerald-700 bg-emerald-50/50">{{ $sRecap['hadir'] }}</td>
                                        <td class="py-3.5 px-4 text-center font-bold text-blue-700 bg-blue-50/50">{{ $sRecap['sakit'] }}</td>
                                        <td class="py-3.5 px-4 text-center font-bold text-amber-700 bg-amber-50/50">{{ $sRecap['izin'] }}</td>
                                        <td class="py-3.5 px-4 text-center font-bold text-rose-700 bg-rose-50/50">{{ $sRecap['alpha'] }}</td>
                                        <td class="py-3.5 px-4 text-center font-bold text-purple-700 bg-purple-50/50">{{ $sRecap['tdk'] }}</td>
                                        <td class="py-3.5 px-6 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $percent >= 80 ? 'bg-emerald-100 text-emerald-800' : ($percent >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                                {{ $percent }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-12 text-center text-slate-400 text-xs">
                                            Belum ada data siswa untuk kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                {{-- FORM FILTER HARIAN --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <form method="GET" action="{{ route('teacher.attendance.recap') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <input type="hidden" name="view" value="daily">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Kelas</label>
                            <select name="class_id" class="block w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                                @foreach($classes as $id => $name)
                                    <option value="{{ $id }}" @selected($selectedClass == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Tanggal</label>
                            <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" class="block w-full text-xs font-medium rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-2.5">
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow transition">
                                Lihat Log Harian
                            </button>
                        </div>
                    </form>
                </div>

                {{-- CARD BERITA ACARA MENGAJAR (JURNAL HARIAN) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4 border-l-4 border-l-indigo-600">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold text-sm">📑</span>
                            <div>
                                <h3 class="font-bold text-sm text-gray-800">Berita Acara & Jurnal Mengajar Harian</h3>
                                <p class="text-xs text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($selectedDate ?? date('Y-m-d'))->isoFormat('D MMMM Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @if(isset($teachingJournal) && $teachingJournal)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2 space-y-3">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase block">Mata Pelajaran:</span>
                                    <p class="text-xs font-bold text-slate-800">{{ $teachingJournal->subject ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase block">Materi / Pokok Pembahasan:</span>
                                    <p class="text-xs font-semibold text-slate-700">{{ $teachingJournal->topic ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase block">Ringkasan Kegiatan (Catatan):</span>
                                    <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200/80 leading-relaxed">{{ $teachingJournal->activities_summary ?? 'Tidak ada catatan khusus.' }}</p>
                                </div>
                            </div>

                            <div class="text-center border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6 flex flex-col items-center justify-center">
                                <span class="text-[11px] font-bold text-slate-400 uppercase block mb-2">Foto Bukti Kegiatan KBM</span>
                                @if($teachingJournal->photo_evidence)
                                    <a href="{{ Storage::url($teachingJournal->photo_evidence) }}" target="_blank" class="block group">
                                        <img src="{{ Storage::url($teachingJournal->photo_evidence) }}" class="w-full h-36 object-cover rounded-xl border border-slate-200 shadow-sm group-hover:scale-105 transition" alt="Foto KBM">
                                        <span class="text-[10px] text-indigo-600 font-bold block mt-1">🔍 Klik untuk memperbesar</span>
                                    </a>
                                @else
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-slate-400 text-xs w-full">
                                        📷 Tidak ada foto diunggah.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs flex items-center gap-2">
                            <span>⚠️ Berita acara mengajar belum diisi untuk tanggal ini.</span>
                        </div>
                    @endif
                </div>

                {{-- TABEL DETAIL LOG PRESENSI HARIAN SISWA --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="font-bold text-sm text-gray-800">Detail Status Kehadiran Siswa — {{ \Carbon\Carbon::parse($selectedDate ?? date('Y-m-d'))->isoFormat('D MMMM Y') }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-slate-100 uppercase font-bold text-gray-700 tracking-wider text-[11px] border-b border-slate-200">
                                <tr>
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-6">Nama Lengkap Siswa</th>
                                    <th class="py-3.5 px-4 text-center whitespace-nowrap">Status Kehadiran</th>
                                    <th class="py-3.5 px-6">Lampiran Surat / Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($students as $idx => $s)
                                    @php
                                        $att = $dailyAttendances[$s->id] ?? null;
                                        $status = $att ? $att->status : 'Belum Diabsen';
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3.5 px-4 text-center font-bold text-gray-400">{{ $idx + 1 }}</td>
                                        <td class="py-3.5 px-6 font-bold text-gray-800">{{ $s->name }}</td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($status == 'Hadir' || $status == 'H')
                                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-bold rounded-lg text-[11px]">✓ Hadir</span>
                                            @elseif($status == 'Sakit' || $status == 'S')
                                                <span class="px-2.5 py-1 bg-blue-100 text-blue-800 font-bold rounded-lg text-[11px]">+ Sakit</span>
                                            @elseif($status == 'Izin' || $status == 'I')
                                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold rounded-lg text-[11px]">📩 Izin</span>
                                            @elseif($status == 'Alpa' || $status == 'Alpha' || $status == 'A')
                                                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 font-bold rounded-lg text-[11px]">✕ Alpha</span>
                                            @elseif($status == 'TDK')
                                                <span class="px-2.5 py-1 bg-purple-100 text-purple-800 font-bold rounded-lg text-[11px]">⚠️ TDK</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-slate-100 text-slate-500 font-medium rounded-lg text-[11px]">Belum Diabsen</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-6">
                                            @if($att && $att->permit)
                                                <a href="{{ Storage::url($att->permit->attachment_path ?? $att->permit->letter_file) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-lg border border-indigo-200 transition">
                                                    📄 Lihat Surat {{ $att->permit->permission_type ?? 'Izin' }} (Ortu)
                                                </a>
                                            @elseif($att && $att->notes)
                                                <span class="text-[11px] text-purple-700 font-semibold bg-purple-50 px-2.5 py-1 rounded-lg border border-purple-200 inline-block">
                                                    📌 Catatan: {{ $att->notes }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-slate-400 text-xs">
                                            Belum ada data siswa untuk kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>