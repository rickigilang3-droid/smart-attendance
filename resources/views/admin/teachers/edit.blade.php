<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-3xl mx-auto space-y-6">
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800">Edit Data Guru / Pengajar ✏️</h1>
                    <p class="text-xs text-slate-500 mt-1">Perbarui informasi data diri dan mata pelajaran guru.</p>
                </div>
                <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition">
                    ← Kembali
                </a>
            </div>

            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip', $teacher->nip) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('nip') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('name') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mata Pelajaran Utama</label>
                        <input type="text" name="subject" value="{{ old('subject', $teacher->subject) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('subject') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">No. Telepon / WA</label>
                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('phone') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <a href="{{ route('admin.teachers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-200 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>