<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-3xl mx-auto space-y-6">

            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">Edit Data Siswa ✏️</h1>
                        <p class="text-xs text-slate-500 mt-1">Perbarui informasi siswa {{ $student->name }}.</p>
                    </div>
                    <a href="{{ route('admin.students.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">← Kembali</a>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-2xl space-y-1">
                        <p class="font-bold">Terjadi kesalahan input:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">NISN *</label>
                            <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @error('nisn') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">NIS (Opsional)</label>
                            <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
                        <input type="text" name="name" value="{{ old('name', $student->name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kelas *</label>
                            <select name="class_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Jenis Kelamin *</label>
                            <select name="gender" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. HP Orang Tua / Wali *</label>
                        <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        @error('parent_phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Foto Siswa <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span></label>
                        <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                        @error('photo') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-6 flex justify-end gap-3 border-t border-slate-100">
                        <a href="{{ route('admin.students.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-semibold transition">Batal</a>
                        <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-xs font-semibold shadow-md shadow-amber-500/20 transition">Perbarui Data Siswa</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>