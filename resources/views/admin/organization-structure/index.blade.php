<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans space-y-8">
        <div class="max-w-7xl mx-auto space-y-6">
            
            {{-- Header Modul --}}
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800">Struktur Organisasi & Personalia Sekolah 🏛️</h1>
                    <p class="text-xs text-slate-500 mt-1">Input data personalia & foto untuk membentuk bagan tata kelola sekolah secara otomatis.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    ← Kembali ke Dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Input Personalia Baru --}}
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">➕ Input Data Personalia Bagan</h2>
                <form action="{{ route('admin.organization-structure.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap & Gelar</label>
                        <input type="text" name="name" required placeholder="Contoh: Dra. Yati Utami P., M.Pd." class="w-full text-xs rounded-xl border-slate-200 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Nama Jabatan</label>
                        <input type="text" name="position_title" required placeholder="Contoh: Kepala Sekolah / Waka Kurikulum" class="w-full text-xs rounded-xl border-slate-200 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Tingkat / Position Level</label>
                        <select name="level" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-emerald-500">
                            <option value="komite">Komite Sekolah</option>
                            <option value="kepala_sekolah">Kepala Sekolah</option>
                            <option value="sub_pimpinan">Kepala TU / Penjaminan Mutu</option>
                            <option value="waka">Waka Urusan (Kesiswaan/Kurikulum/Sarpras/Humas/dll)</option>
                            <option value="bk">Bimbingan Konseling (BK)</option>
                            <option value="wali_kelas">Wali Kelas</option>
                            <option value="guru_mapel">Guru Mata Pelajaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Upload Foto Profil</label>
                        <input type="file" name="photo" accept="image/*" class="w-full text-xs rounded-xl border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Urutan Posisi / Prioritas (Angka)</label>
                        <input type="number" name="order_priority" value="0" class="w-full text-xs rounded-xl border-slate-200 focus:ring-emerald-500" placeholder="Semakin kecil semakin kiri">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                            Simpan Data Personalia
                        </button>
                    </div>
                </form>
            </div>

            {{-- VISUAL BAGAN STRUKTUR --}}
            <div class="p-8 bg-gradient-to-b from-emerald-800 via-emerald-700 to-teal-900 rounded-3xl shadow-xl text-white overflow-x-auto">
                <div class="min-w-[1000px] flex flex-col items-center space-y-8">

                    <div class="text-center space-y-1">
                        <h2 class="text-3xl font-black tracking-wider text-yellow-300 uppercase drop-shadow-md">
                            STRUKTUR ORGANISASI SEKOLAH DAN PERSONALIA
                        </h2>
                    </div>

                    {{-- LEVEL 1: Komite & Kepala Sekolah --}}
                    <div class="flex items-center justify-center gap-16 w-full max-w-3xl">
                        {{-- Komite --}}
                        <div class="bg-gradient-to-r from-amber-200 to-yellow-300 text-slate-900 px-6 py-3 rounded-2xl font-black text-sm uppercase shadow-lg border-2 border-yellow-100 flex flex-col items-center">
                            <span>{{ $komite->position_title ?? 'Komite Sekolah' }}</span>
                            @if($komite)
                                <span class="text-xs text-slate-700 font-bold mt-1">{{ $komite->name }}</span>
                                <form action="{{ route('admin.organization-structure.destroy', $komite->id) }}" method="POST" class="mt-1">
                                    @csrf @method('DELETE')
                                    <button class="text-[9px] bg-rose-600 text-white px-2 py-0.5 rounded">Hapus</button>
                                </form>
                            @endif
                        </div>

                        <div class="w-20 border-t-2 border-dashed border-yellow-300"></div>

                        {{-- Kepala Sekolah --}}
                        <div class="flex flex-col items-center bg-white/10 backdrop-blur-md p-4 rounded-3xl border border-white/20 shadow-2xl relative">
                            <div class="bg-gradient-to-r from-amber-200 to-yellow-300 text-slate-900 px-6 py-2 rounded-xl font-black text-xs uppercase shadow mb-3">
                                {{ $kepalaSekolah->position_title ?? 'Kepala Sekolah' }}
                            </div>
                            <div class="w-24 h-28 bg-slate-200 rounded-xl overflow-hidden border-2 border-white shadow-md mb-2">
                                <img src="{{ $kepalaSekolah && $kepalaSekolah->photo ? asset('storage/'.$kepalaSekolah->photo) : 'https://ui-avatars.com/api/?name='.urlencode($kepalaSekolah->name ?? 'Kepala Sekolah') }}" class="w-full h-full object-cover">
                            </div>
                            <span class="font-extrabold text-sm text-yellow-300 text-center">{{ $kepalaSekolah->name ?? 'Belum Diisi' }}</span>
                            @if($kepalaSekolah)
                                <form action="{{ route('admin.organization-structure.destroy', $kepalaSekolah->id) }}" method="POST" class="mt-2">
                                    @csrf @method('DELETE')
                                    <button class="text-[9px] bg-rose-600 hover:bg-rose-700 text-white px-2 py-0.5 rounded">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- LEVEL 2: Sub Pimpinan (TU / Penjaminan Mutu) --}}
                    @if($subPimpinan->count() > 0)
                        <div class="flex justify-center gap-8 w-full max-w-5xl pt-4 border-t border-white/20 flex-wrap">
                            @foreach($subPimpinan as $item)
                                <div class="flex flex-col items-center bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/20 shadow-lg w-52 relative">
                                    <div class="bg-yellow-200 text-slate-900 px-3 py-1 rounded-lg font-bold text-[11px] uppercase mb-2 text-center">
                                        {{ $item->position_title }}
                                    </div>
                                    <div class="w-20 h-24 bg-slate-200 rounded-lg overflow-hidden border-2 border-white shadow mb-1.5">
                                        <img src="{{ $item->photo ? asset('storage/'.$item->photo) : 'https://ui-avatars.com/api/?name='.urlencode($item->name) }}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="font-bold text-xs text-yellow-200 text-center">{{ $item->name }}</span>
                                    <form action="{{ route('admin.organization-structure.destroy', $item->id) }}" method="POST" class="mt-2">
                                        @csrf @method('DELETE')
                                        <button class="text-[9px] bg-rose-600 hover:bg-rose-700 text-white px-2 py-0.5 rounded">Hapus</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- LEVEL 3: Waka Urusan --}}
                    @if($wakas->count() > 0)
                        <div class="flex justify-center gap-4 w-full pt-4 border-t border-white/20 flex-wrap">
                            @foreach($wakas as $item)
                                <div class="flex flex-col items-center bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/20 w-48 relative">
                                    <div class="bg-amber-300 text-slate-900 px-2 py-1 rounded-lg font-extrabold text-[10px] uppercase text-center mb-2">
                                        {{ $item->position_title }}
                                    </div>
                                    <div class="w-16 h-20 bg-slate-200 rounded-lg overflow-hidden border border-white shadow mb-1">
                                        <img src="{{ $item->photo ? asset('storage/'.$item->photo) : 'https://ui-avatars.com/api/?name='.urlencode($item->name) }}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="font-bold text-[11px] text-yellow-200 text-center">{{ $item->name }}</span>
                                    <form action="{{ route('admin.organization-structure.destroy', $item->id) }}" method="POST" class="mt-2">
                                        @csrf @method('DELETE')
                                        <button class="text-[9px] bg-rose-600 hover:bg-rose-700 text-white px-2 py-0.5 rounded">Hapus</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- LEVEL BK (Bimbingan Konseling) --}}
                    @if($bk->count() > 0)
                        <div class="flex justify-center gap-4 w-full pt-4 border-t border-white/20 flex-wrap">
                            @foreach($bk as $item)
                                <div class="flex flex-col items-center bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/20 w-48 relative">
                                    <div class="bg-teal-300 text-slate-900 px-2 py-1 rounded-lg font-extrabold text-[10px] uppercase text-center mb-2">
                                        {{ $item->position_title }}
                                    </div>
                                    <div class="w-16 h-20 bg-slate-200 rounded-lg overflow-hidden border border-white shadow mb-1">
                                        <img src="{{ $item->photo ? asset('storage/'.$item->photo) : 'https://ui-avatars.com/api/?name='.urlencode($item->name) }}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="font-bold text-[11px] text-yellow-200 text-center">{{ $item->name }}</span>
                                    <form action="{{ route('admin.organization-structure.destroy', $item->id) }}" method="POST" class="mt-2">
                                        @csrf @method('DELETE')
                                        <button class="text-[9px] bg-rose-600 hover:bg-rose-700 text-white px-2 py-0.5 rounded">Hapus</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- LEVEL 4: Wali Kelas & Guru Mapel --}}
                    <div class="grid grid-cols-2 gap-6 w-full pt-4 border-t border-white/20">
                        {{-- Wali Kelas --}}
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/20">
                            <h3 class="text-xs font-bold text-yellow-300 uppercase text-center mb-3">Daftar Wali Kelas</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @forelse($walas as $item)
                                    <div class="p-2.5 bg-white/10 rounded-xl flex items-center justify-between text-xs">
                                        <div>
                                            <div class="font-bold text-amber-200">{{ $item->position_title }}</div>
                                            <div class="text-white text-[11px]">{{ $item->name }}</div>
                                        </div>
                                        <form action="{{ route('admin.organization-structure.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="text-[9px] bg-rose-600 text-white px-1.5 py-0.5 rounded">Hapus</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="col-span-2 text-center text-xs text-emerald-200">Belum ada data wali kelas.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Guru Mapel --}}
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/20">
                            <h3 class="text-xs font-bold text-yellow-300 uppercase text-center mb-3">Daftar Guru Mata Pelajaran</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @forelse($guruMapel as $item)
                                    <div class="p-2.5 bg-white/10 rounded-xl flex items-center justify-between text-xs">
                                        <div>
                                            <div class="font-bold text-amber-200">{{ $item->position_title }}</div>
                                            <div class="text-white text-[11px]">{{ $item->name }}</div>
                                        </div>
                                        <form action="{{ route('admin.organization-structure.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="text-[9px] bg-rose-600 text-white px-1.5 py-0.5 rounded">Hapus</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="col-span-2 text-center text-xs text-emerald-200">Belum ada data guru mapel.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>