<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans" x-data="{ search: '' }">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header Utama -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                        <span>Data Siswa</span> 👥
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Daftar siswa di kelas yang Anda ampu / wali kelasi.</p>
                </div>
            </div>

            <!-- Card Pencarian & Tabel -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <input type="text" x-model="search" placeholder="Cari nama siswa atau NISN..." 
                        class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 text-slate-700 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-4">
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-400 uppercase bg-slate-50/80 rounded-xl">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">NISN</th>
                                <th class="px-4 py-3">Nama Lengkap</th>
                                <th class="px-4 py-3">L/P</th>
                                <th class="px-4 py-3">Kelas</th>
                                <th class="px-4 py-3 rounded-r-xl">No HP Ortu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $student)
                                <tr x-show="search === '' || '{{ strtolower($student->name) }}'.includes(search.toLowerCase()) || '{{ $student->nisn }}'.includes(search)" class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-600">{{ $student->nisn ?? '-' }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $student->name }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-600">{{ $student->gender ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold">
                                            {{ $student->schoolClass->name ?? $student->schoolClass->class_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500 font-mono">{{ $student->parent_phone ?? $student->phone ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400 text-xs">
                                        Belum ada data siswa untuk kelas Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>