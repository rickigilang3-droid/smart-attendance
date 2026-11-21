<x-app-layout>
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Monitoring & Cetak Rapor Siswa 📄</h1>
                <p class="text-xs text-slate-500">Pantau kelengkapan pengisian nilai dari guru mapel dan cetak rapor siswa</p>
            </div>

            <!-- Filter Kelas & Semester -->
            <form method="GET" action="{{ route('admin.report-cards.index') }}" class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm">
                <select name="class_id" class="text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ ($selectedClass->id ?? null) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
                <select name="semester" class="text-xs px-3 py-2 rounded-xl border-slate-200 focus:ring-indigo-500">
                    <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="Genap" {{ $semester == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-700 transition-all">
                    Filter
                </button>
            </form>
        </div>

        <!-- Section Status Pengisian Nilai Guru -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-3">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Status Setor Nilai Guru (Kelas: {{ $selectedClass->name ?? '-' }})</h2>
            <div class="flex flex-wrap gap-2">
                @forelse($submittedSubjects as $subject)
                    <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-bold rounded-xl flex items-center gap-1.5">
                        ✅ {{ $subject }}
                    </span>
                @empty
                    <span class="text-xs text-slate-400 italic">Belum ada guru mata pelajaran yang mengirimkan nilai (Submitted) untuk kelas ini.</span>
                @endforelse
            </div>
        </div>

        <!-- Tabel Daftar Siswa & Aksi Cetak Rapor -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="flex justify-between items-center border-b pb-4">
                <h2 class="text-sm font-bold text-slate-800">
                    Daftar Siswa Kelas: <span class="text-indigo-600">{{ $selectedClass->name ?? '-' }}</span>
                </h2>
                <span class="text-xs text-slate-500 font-medium">Total: {{ $students->count() }} Siswa</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b text-slate-500 uppercase font-semibold">
                            <th class="p-3">NIS / NISN</th>
                            <th class="p-3">Nama Siswa</th>
                            <th class="p-3 text-center">Jenis Kelamin</th>
                            <th class="p-3 text-right">Aksi Rapor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $st)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="p-3 font-mono text-slate-500">{{ $st->nisn ?? $st->nis ?? '-' }}</td>
                                <td class="p-3 font-bold text-slate-800">{{ $st->name }}</td>
                                <td class="p-3 text-center text-slate-600">{{ $st->gender ?? '-' }}</td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('admin.report-cards.show', ['id' => $st->id, 'semester' => $semester]) }}" 
                                       target="_blank"
                                       class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all inline-flex items-center gap-1.5">
                                        🖨️ Pratinjau & Cetak Rapor
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">Tidak ada siswa terdaftar di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>