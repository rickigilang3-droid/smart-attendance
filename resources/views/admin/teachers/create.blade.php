<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            
            <div class="border-b border-slate-100 pb-4">
                <h1 class="text-xl font-extrabold text-slate-800">Tambah Data Guru / Pengajar 👨‍🏫</h1>
                <p class="text-xs text-slate-500 mt-1">Masukkan informasi biodata dan mata pelajaran utama pengajar.</p>
            </div>

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

            <form action="{{ route('admin.teachers.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIP (Nomor Induk Pegawai) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 198501152010011002" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Drs. Budi Santoso, M.Pd" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Mata Pelajaran Utama <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Matematika" required class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. Telepon / Whatsapp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" class="w-full text-xs rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-sm transition">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>