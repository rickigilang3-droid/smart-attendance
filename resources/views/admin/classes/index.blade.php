@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $classes */
@endphp

<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800">Data Master Kelas 🏫</h1>
                    <p class="text-xs text-slate-500 mt-1">Kelola rombel dan tingkat kelas sekolah.</p>
                </div>
                <a href="{{ route('admin.classes.create') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-2xl shadow-md transition">
                    + Tambah Kelas Baru
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                            <th class="py-3.5 px-4 w-1/6">Tingkat</th>
                            <th class="py-3.5 px-4 w-1/4">Nama Kelas</th>
                            <th class="py-3.5 px-4 w-1/4">Wali Kelas</th>
                            <th class="py-3.5 px-4 w-1/6">Jumlah Siswa</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($classes as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 font-bold text-slate-700">
                                    Tingkat {{ $item->grade_level }}
                                </td>
                                <td class="py-4 px-4 font-semibold text-slate-800">
                                    <a href="{{ route('admin.classes.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline font-bold">
                                        {{ $item->name }}
                                    </a>
                                </td>
                                <td class="py-4 px-4 text-slate-600 text-xs font-medium">
                                    @if($item->homeroomTeacher)
                                        <span class="font-semibold text-slate-800">👨‍🏫 {{ $item->homeroomTeacher->name }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum diatur</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl text-xs font-bold">
                                        👥 {{ $item->students_count ?? 0 }} Siswa
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.classes.show', $item->id) }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-semibold hover:bg-indigo-100 transition">
                                            Lihat Siswa
                                        </a>
                                        <a href="{{ route('admin.classes.edit', $item->id) }}" class="px-3 py-1.5 bg-amber-50 text-amber-600 rounded-xl text-xs font-semibold hover:bg-amber-100 transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.classes.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kelas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-600 rounded-xl text-xs font-semibold hover:bg-rose-100 transition">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Belum ada kelas yang terdaftar. Klik tombol Tambah Kelas Baru di atas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $classes->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>