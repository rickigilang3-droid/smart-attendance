<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-2xl mx-auto space-y-6">

            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">Tambah Kelas Baru 🏫</h1>
                        <p class="text-xs text-slate-500 mt-1">Input tingkat dan nama kelas sekolah.</p>
                    </div>
                    <a href="{{ route('admin.classes.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">← Kembali</a>
                </div>

                <form action="{{ route('admin.classes.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tingkat / Grade *</label>
                        <select name="grade_level" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="10">Tingkat 10 / X</option>
                            <option value="11">Tingkat 11 / XI</option>
                            <option value="12">Tingkat 12 / XII</option>
                        </select>
                        @error('grade_level') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Kelas *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: X IPA 1" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-6 flex justify-end gap-3 border-t border-slate-100">
                        <a href="{{ route('admin.classes.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-semibold transition">Batal</a>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-semibold shadow-md transition">Simpan Kelas</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>