<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 font-sans max-w-7xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Kelola Pengumuman Sekolah</h1>
                <p class="text-xs text-slate-500">Tambah dan kelola informasi resmi yang tampil di Dashboard Guru & Orang Tua</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold rounded-xl transition">
                ← Kembali ke Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-semibold">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Tambah Pengumuman --}}
            <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-slate-800">Buat Pengumuman Baru</h2>
                <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Judul Pengumuman</label>
                        <input type="text" name="title" required placeholder="Contoh: Rapat Evaluasi Pembelajaran" class="w-full rounded-xl border-slate-300 text-xs py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kategori</label>
                        <select name="category" class="w-full rounded-xl border-slate-300 text-xs py-2">
                            <option value="AKADEMIK">AKADEMIK</option>
                            <option value="KEGIATAN">KEGIATAN</option>
                            <option value="UMUM">UMUM</option>
                        </select>
                    </div>

                    {{-- Target Sasaran Pengumuman --}}
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Target Sasaran Pengumuman</label>
                        <select name="target" class="w-full rounded-xl border-slate-300 text-xs py-2" required>
                            <option value="all">🌐 Semua (Guru & Orang Tua)</option>
                            <option value="teacher">👨‍🏫 Khusus Guru</option>
                            <option value="parent">👨‍👩‍👧 Khusus Orang Tua / Wali</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 text-xs py-2">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Isi Pengumuman</label>
                        <textarea name="content" rows="4" required placeholder="Tuliskan isi informasi di sini..." class="w-full rounded-xl border-slate-300 text-xs p-2.5"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow transition">
                        📢 Terbitkan Pengumuman
                    </button>
                </form>
            </div>

            {{-- Tabel Daftar Pengumuman --}}
            <div class="lg:col-span-2 p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-slate-800">Daftar Pengumuman Aktif</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b text-slate-500 uppercase font-semibold">
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Target & Kategori</th>
                                <th class="p-3">Judul & Isi</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($announcements as $ann)
                                <tr>
                                    <td class="p-3 font-mono text-slate-600 whitespace-nowrap">{{ $ann->date }}</td>
                                    <td class="p-3 space-y-1">
                                        {{-- Badge Target Sasaran --}}
                                        <div>
                                            @if(($ann->target ?? 'all') == 'teacher')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    👨‍🏫 Khusus Guru
                                                </span>
                                            @elseif(($ann->target ?? 'all') == 'parent')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    👨‍👩‍👧 Khusus Ortu
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    🌐 Semua
                                                </span>
                                            @endif
                                        </div>
                                        {{-- Badge Kategori --}}
                                        <div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700">
                                                {{ $ann->category }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-800">{{ $ann->title }}</div>
                                        <div class="text-slate-500 text-[11px] line-clamp-1">{{ $ann->content }}</div>
                                    </td>
                                    <td class="p-3 text-right">
                                        <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-rose-600 hover:underline font-bold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-slate-400">Belum ada pengumuman yang diterbitkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>