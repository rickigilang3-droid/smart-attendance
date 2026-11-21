<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            
            <!-- Header Form -->
            <div class="border-b border-slate-100 pb-4">
                <h1 class="text-xl font-extrabold text-slate-800">Tambah Jadwal Pelajaran Baru 📅</h1>
                <p class="text-xs text-slate-500 mt-1">Plotting mata pelajaran, waktu belajar, dan guru pengajar per kelas.</p>
            </div>

            <!-- Pesan Error Validasi jika ada -->
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-2xl space-y-1">
                    <p class="font-bold">Terjadi kesalahan input:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Pilih Hari -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pilih Hari <span class="text-rose-500">*</span></label>
                        <select name="day" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">-- Pilih Hari --</option>
                            @foreach($days as $d)
                                <option value="{{ $d }}" {{ old('day') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Kelas -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pilih Kelas <span class="text-rose-500">*</span></label>
                        <select name="class_id" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Nama Mata Pelajaran -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Matematika, Bahasa Indonesia" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Pilih Guru Pengajar -->
                <div>
    <label class="block font-bold text-slate-700 mb-1">Pilih Guru Pengajar <span class="text-slate-400 font-normal">(Opsional untuk Istirahat)</span></label>
    <select name="teacher_id" class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
        <option value="">-- Tanpa Guru (Istirahat / Kegiatan) --</option>
        @foreach($teachers as $teacher)
            <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->subject }})</option>
        @endforeach
    </select>
</div>

                <!-- Jam Mulai & Jam Selesai -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-sm transition">
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>