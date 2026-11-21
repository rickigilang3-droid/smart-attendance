<x-app-layout>
    <div x-data="{ openModal: false, selectedJournal: null }" class="p-6 space-y-6">
        <!-- Header & Navigasi Tab -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Monitoring KBM Real-time 🏫</h1>
                <p class="text-xs text-slate-500">Daftar guru yang telah membuka kelas, mengisi berita acara, dan ketepatan waktu KBM</p>
            </div>

            <!-- Tab Filter View -->
            <div class="flex items-center bg-slate-100 p-1 rounded-2xl border border-slate-200">
                <a href="{{ route('principal.kbm.index', ['view' => 'daily', 'date' => $selectedDate]) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $viewType === 'daily' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                   📅 Real-Time Hari Ini
                </a>
                <a href="{{ route('principal.kbm.index', ['view' => 'monthly', 'month' => $selectedMonth, 'year' => $selectedYear]) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $viewType === 'monthly' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                   📊 Rekap Bulanan
                </a>
            </div>
        </div>

        @if($viewType === 'daily')
            <!-- FILTER TANGGAL HARIAN -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <form method="GET" action="{{ route('principal.kbm.index') }}" class="flex items-center gap-3">
                    <input type="hidden" name="view" value="daily">
                    <label class="text-xs font-bold text-slate-600">Pilih Tanggal:</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500">
                </form>
                <div class="flex items-center gap-4 text-xs font-bold text-slate-500">
                    <span>Total Kelas Dibuka: <span class="text-emerald-600">{{ $openedJournals->count() }} Sesi</span></span>
                    <span>•</span>
                    <span>Hari: <span class="text-indigo-600">{{ $dayName }}, {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</span></span>
                </div>
            </div>

            <!-- TABEL REAL-TIME HARIAN (HANYA GURU YANG SUDAH BUKA KELAS) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b text-slate-500 uppercase font-semibold">
                                <th class="p-3">Jam / Mapel</th>
                                <th class="p-3">Kelas</th>
                                <th class="p-3">Guru Pengajar</th>
                                <th class="p-3">Status Buka Kelas</th>
                                <th class="p-3">Waktu Buka / Keterlambatan</th>
                                <th class="p-3">Materi / Ringkasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($openedJournals as $journal)
                                @php
                                    $teacherName = $journal->teacher->name ?? '-';
                                    $className   = $journal->schoolClass->name ?? '-';
                                    $subjectName = $journal->subject_name ?? $journal->subject ?? '-';

                                    // Data JSON Jurnal untuk Modal Detail
                                    $journalData = json_encode([
                                        'teacher'      => $teacherName,
                                        'subject'      => $subjectName,
                                        'class'        => $className,
                                        'topic'        => $journal->topic ?? '-',
                                        'summary'      => $journal->activities_summary ?? $journal->notes ?? '-',
                                        'late_reason'  => $journal->late_reason ?? null,
                                        'photo'        => $journal->photo_evidence ? asset('storage/' . $journal->photo_evidence) : null,
                                        'opened_at'    => $journal->open_time ?? '-',
                                        'is_late'      => (bool) $journal->is_late,
                                        'late_minutes' => $journal->late_minutes ?? 0
                                    ]);
                                @endphp

                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- JAM / MAPEL -->
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800">{{ $subjectName }}</div>
                                        <div class="text-[11px] font-mono text-slate-400">
                                            {{ $journal->time_range ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- KELAS -->
                                    <td class="p-3 font-semibold text-slate-700">
                                        {{ $className }}
                                    </td>

                                    <!-- GURU PENGAJAR -->
                                    <td class="p-3">
                                        <button type="button"
                                                @click="selectedJournal = {{ $journalData }}; openModal = true" 
                                                class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1.5 text-left focus:outline-none">
                                            <span>{{ $teacherName }}</span>
                                            <span class="text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded-md border border-indigo-100">🔍 Detail</span>
                                        </button>
                                    </td>

                                    <!-- STATUS BUKA KELAS -->
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700">
                                            ✅ Jurnal Terisi
                                        </span>
                                    </td>

                                    <!-- WAKTU BUKA / KETERLAMBATAN -->
                                    <td class="p-3">
                                        @if($journal->is_late)
                                            <span class="px-2.5 py-1 rounded-md bg-rose-100 text-rose-700 font-bold text-[11px]">
                                                ⚠️ Terlambat (Jam {{ $journal->open_time }} • Telat {{ $journal->late_minutes }}m)
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 font-medium text-[11px]">
                                                ⚡ Tepat Waktu ({{ $journal->open_time }})
                                            </span>
                                        @endif
                                    </td>

                                    <!-- MATERI / RINGKASAN -->
                                    <td class="p-3 text-slate-600">
                                        <button type="button"
                                                @click="selectedJournal = {{ $journalData }}; openModal = true" 
                                                class="text-indigo-600 hover:underline font-medium text-left">
                                            {{ \Illuminate\Support\Str::limit($journal->topic ?? $journal->activities_summary ?? 'Lihat Ringkasan KBM', 35) }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center">
                                        <p class="text-xs font-bold text-slate-500">Belum ada guru yang membuka kelas pada tanggal ini.</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Data akan otomatis muncul begitu guru menekan Buka Kelas di Portal Guru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @else
            <!-- FILTER BULANAN -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <form method="GET" action="{{ route('principal.kbm.index') }}" class="flex items-center gap-3">
                    <input type="hidden" name="view" value="monthly">
                    <select name="month" class="text-xs px-3 py-2 rounded-xl border-slate-200">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="text-xs px-3 py-2 rounded-xl border-slate-200">
                        @foreach([2025, 2026, 2027] as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-700 transition">Filter</button>
                </form>
            </div>

            <!-- TABEL REKAP BULANAN -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b text-slate-500 uppercase font-semibold">
                                <th class="p-3">Nama Guru</th>
                                <th class="p-3 text-center">Tepat Waktu</th>
                                <th class="p-3 text-center">Terlambat</th>
                                <th class="p-3 text-center">Total Jurnal Terisi Bulan Ini</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($monthlyRecap as $t)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="p-3 font-bold text-slate-800">{{ $t->name }}</td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-lg">
                                            {{ $t->punctual_count ?? 0 }} Sesi
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 bg-rose-50 text-rose-700 font-bold rounded-lg">
                                            {{ $t->late_count ?? 0 }} Sesi
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-lg">
                                            {{ $t->teaching_journals_count }} Kali Mengisi
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-slate-400">Belum ada data rekap jurnal guru untuk bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- MODAL POP-UP DETAIL KBM & BUKTI FOTO -->
        <div x-show="openModal" 
             x-transition 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" 
             style="display: none;">
            <div @click.away="openModal = false" class="bg-white rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <!-- Modal Header -->
                <div class="flex justify-between items-center border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-800" x-text="'Detail KBM: ' + (selectedJournal?.subject || '')"></h3>
                        <p class="text-xs text-slate-500" x-text="(selectedJournal?.teacher || '') + ' • Kelas ' + (selectedJournal?.class || '')"></p>
                    </div>
                    <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="space-y-4 overflow-y-auto text-xs pr-1">
                    <!-- Status Jam Buka -->
                    <div class="p-3 rounded-2xl" :class="selectedJournal?.is_late ? 'bg-rose-50 border border-rose-200' : 'bg-emerald-50 border border-emerald-200'">
                        <div class="font-bold text-xs" :class="selectedJournal?.is_late ? 'text-rose-700' : 'text-emerald-700'">
                            <span x-text="selectedJournal?.is_late ? '⚠️ Guru Terlambat Membuka Kelas' : '⚡ Guru Buka Kelas Tepat Waktu'"></span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5" x-text="'Waktu Buka Kelas: ' + (selectedJournal?.opened_at || '-') + (selectedJournal?.is_late ? ' (Terlambat ' + selectedJournal?.late_minutes + ' menit)' : '')"></p>
                        
                        <!-- Alasan Keterlambatan jika ada -->
                        <template x-if="selectedJournal?.late_reason">
                            <div class="mt-2 pt-2 border-t border-rose-200/80 text-rose-800">
                                <span class="font-bold">Klarifikasi Alasan Telat:</span>
                                <p class="italic text-[11px] mt-0.5" x-text="selectedJournal?.late_reason"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Materi Pembelajaran -->
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700">📌 Materi Pembelajaran / Topik:</label>
                        <p class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-slate-800 font-semibold" x-text="selectedJournal?.topic || '-'"></p>
                    </div>

                    <!-- Ringkasan Aktivitas / Berita Acara -->
                    <div class="space-y-1">
                        <label class="font-bold text-slate-700">📝 Ringkasan Kegiatan KBM / Berita Acara:</label>
                        <p class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-slate-700 whitespace-pre-line" x-text="selectedJournal?.summary || '-'"></p>
                    </div>

                    <!-- Foto Bukti KBM -->
                    <div class="space-y-2">
                        <label class="font-bold text-slate-700">📸 Foto Dokumentasi Bukti KBM:</label>
                        <template x-if="selectedJournal?.photo">
                            <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm max-h-64 flex justify-center bg-slate-100">
                                <img :src="selectedJournal?.photo" alt="Bukti KBM" class="object-cover w-full h-full hover:scale-105 transition-all duration-300">
                            </div>
                        </template>
                        <template x-if="!selectedJournal?.photo">
                            <div class="p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-center text-slate-400">
                                📷 Guru belum mengunggah foto bukti kegiatan KBM.
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t flex justify-end">
                    <button type="button" @click="openModal = false" class="px-5 py-2.5 bg-slate-800 text-white font-bold text-xs rounded-xl hover:bg-slate-900 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>