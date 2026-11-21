<x-app-layout>
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Input Nilai Siswa 📝</h1>
                <p class="text-xs text-slate-500">Kelola komponen nilai tugas, UH, PTS, PAS dan kirimkan ke Portal Admin untuk Rapor</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl text-xs font-semibold">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Filter Kelas & Mapel -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5">
            <form method="GET" action="{{ route('teacher.grades.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Pilih Kelas</label>
                    <select name="class_id" class="w-full text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Mata Pelajaran</label>
                    <input type="text" name="subject_name" value="{{ $subjectName }}" class="w-full text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500" placeholder="Matematika / IPA / dll">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Semester</label>
                    <select name="semester" class="w-full text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500">
                        <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil (PTS/PAS 1)</option>
                        <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Genap (PTS/PAS 2)</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition-all">
                        🔍 Tampilkan Siswa
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Tabel Input Nilai -->
        <form method="POST" action="{{ route('teacher.grades.store') }}">
            @csrf
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
            <input type="hidden" name="subject_name" value="{{ $subjectName }}">
            <input type="hidden" name="semester" value="{{ $semester }}">

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                                <th class="p-3">Nama Siswa</th>
                                <th class="p-3 text-center w-24">Tugas (20%)</th>
                                <th class="p-3 text-center w-24">UH/Formatif (20%)</th>
                                <th class="p-3 text-center w-24">PTS (30%)</th>
                                <th class="p-3 text-center w-24">PAS (30%)</th>
                                <th class="p-3 text-center w-24">Nilai Akhir</th>
                                <th class="p-3">Catatan Capaian Kompetensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $st)
                                @php
                                    $ass = $assessments[$st->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="p-3 font-bold text-slate-800 whitespace-nowrap">
                                        {{ $st->name }}
                                        @if($ass && $ass->status === 'submitted')
                                            <span class="block text-[10px] text-emerald-600 font-normal">✅ Terkirim ke Admin</span>
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01" min="0" max="100" name="grades[{{ $st->id }}][assignment_score]" value="{{ $ass->assignment_score ?? 0 }}" class="w-full text-xs text-center p-2 rounded-xl border-slate-200">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01" min="0" max="100" name="grades[{{ $st->id }}][quiz_score]" value="{{ $ass->quiz_score ?? 0 }}" class="w-full text-xs text-center p-2 rounded-xl border-slate-200">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01" min="0" max="100" name="grades[{{ $st->id }}][mid_score]" value="{{ $ass->mid_score ?? 0 }}" class="w-full text-xs text-center p-2 rounded-xl border-slate-200">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="0.01" min="0" max="100" name="grades[{{ $st->id }}][final_score]" value="{{ $ass->final_score ?? 0 }}" class="w-full text-xs text-center p-2 rounded-xl border-slate-200">
                                    </td>
                                    <td class="p-3 text-center font-extrabold text-indigo-600 text-sm">
                                        {{ number_format($ass->final_grade ?? 0, 1) }}
                                        <span class="text-xs text-slate-400">({{ $ass->letter_grade ?? '-' }})</span>
                                    </td>
                                    <td class="p-2">
                                        <input type="text" name="grades[{{ $st->id }}][teacher_notes]" value="{{ $ass->teacher_notes ?? '' }}" placeholder="Catatan materi..." class="w-full text-xs p-2 rounded-xl border-slate-200">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-slate-400">Pilih kelas untuk menampilkan siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->isNotEmpty())
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="submit" name="action" value="draft" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                            💾 Simpan Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition-all shadow-md">
                            🚀 Submit ke Admin untuk Rapor
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>