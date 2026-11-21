<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-5xl mx-auto space-y-6">

            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800">Tambah Data Siswa (Standar Dapodik) 📝</h1>
                    <p class="text-xs text-slate-500 mt-1">Lengkapi seluruh informasi data pokok siswa terdaftar.</p>
                </div>
                <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition">← Kembali</a>
            </div>

            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- 1. DATA PRIBADI -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-indigo-600 border-b border-slate-100 pb-3">1. Data Pribadi Siswa</h2>
                    
                    <div class="flex items-center gap-6 p-4 bg-slate-50 rounded-2xl border border-slate-200/60 mb-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-200 flex items-center justify-center text-slate-400 font-bold overflow-hidden border border-slate-300">📸</div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Foto Formal Siswa</label>
                            <input type="file" name="photo" accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-600 uppercase mb-1">Nama Lengkap (Sesuai Akta/Ijazah) *</label>
                            <input type="text" name="name" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Nama Panggilan</label>
                            <input type="text" name="nickname" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Jenis Kelamin *</label>
                            <select name="gender" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">NISN</label>
                            <input type="text" name="nisn" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">NIS Lokal Sekolah</label>
                            <input type="text" name="nis" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">NIK / KITAS</label>
                            <input type="text" name="nik" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">No. Kartu Keluarga (KK)</label>
                            <input type="text" name="no_kk" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Kelas *</label>
                            <select name="class_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Tempat Lahir</label>
                            <input type="text" name="pob" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Tanggal Lahir</label>
                            <input type="date" name="dob" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Agama</label>
                            <input type="text" name="religion" placeholder="Islam / Kristen / dll" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block font-bold text-slate-600 uppercase mb-1">Alamat Tempat Tinggal Lengkap</label>
                            <textarea name="address" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">RT / RW</label>
                            <input type="text" name="rt_rw" placeholder="001/002" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Kelurahan</label>
                            <input type="text" name="subdistrict" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Kecamatan</label>
                            <input type="text" name="district" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Kabupaten / Kota</label>
                            <input type="text" name="city" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Koordinat Lintang/Bujur</label>
                            <input type="text" name="coordinates" placeholder="-6.200000, 106.816666" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Status Tempat Tinggal</label>
                            <input type="text" name="residence_type" placeholder="Bersama Orang Tua / Kos" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Moda Transportasi</label>
                            <input type="text" name="transportation" placeholder="Jalan kaki / Motor" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Anak Ke- / Jml Saudara</label>
                            <div class="flex gap-2">
                                <input type="number" name="child_order" placeholder="Anak Ke" class="w-1/2 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                                <input type="number" name="siblings_count" placeholder="Jml Sdr" class="w-1/2 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. DATA PERIODIK & PENDIDIKAN ASAL -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-indigo-600 border-b border-slate-100 pb-3">2. Data Periodik & Pendidikan Asal</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Tinggi Badan (cm)</label>
                            <input type="number" name="height" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Berat Badan (kg)</label>
                            <input type="number" name="weight" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Jarak ke Sekolah (km)</label>
                            <input type="number" step="0.1" name="distance_to_school" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Waktu Tempuh (Menit)</label>
                            <input type="number" name="travel_time_minutes" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-600 uppercase mb-1">Nama Sekolah Asal</label>
                            <input type="text" name="previous_school" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-600 uppercase mb-1">No. Ijazah / SKHU</label>
                            <input type="text" name="ijazah_number" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                    </div>
                </div>

                <!-- 3. DATA ORANG TUA & KONTAK -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-indigo-600 border-b border-slate-100 pb-3">3. Data Orang Tua & Kontak Utama</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Nama Ayah Kandung</label>
                            <input type="text" name="father_name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Pekerjaan Ayah</label>
                            <input type="text" name="father_job" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Penghasilan Ayah</label>
                            <input type="text" name="father_income" placeholder="Contoh: Rp 3.000.000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Nama Ibu Kandung</label>
                            <input type="text" name="mother_name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Pekerjaan Ibu</label>
                            <input type="text" name="mother_job" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Penghasilan Ibu</label>
                            <input type="text" name="mother_income" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">No. HP Orang Tua / WA *</label>
                            <input type="text" name="parent_phone" placeholder="0812xxxx" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">No. HP Siswa</label>
                            <input type="text" name="student_phone" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Email Aktif</label>
                            <input type="email" name="email" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                    </div>
                </div>

                <!-- 4. KESEJAHTERAAN & BEASISWA -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-indigo-600 border-b border-slate-100 pb-3">4. Kesejahteraan, Beasiswa & Prestasi (Opsional)</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">No. KIP / PIP</label>
                            <input type="text" name="kip_number" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">No. PKH / KPS</label>
                            <input type="text" name="pkh_kps_number" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Riwayat Prestasi</label>
                            <textarea name="achievements" rows="2" placeholder="Contoh: Juara 1 FLS2N Kabupaten" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 uppercase mb-1">Riwayat Beasiswa</label>
                            <textarea name="scholarships" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.students.index') }}" class="px-6 py-3 bg-slate-200 text-slate-700 rounded-2xl text-xs font-bold">Batal</a>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold shadow-lg">Simpan Data Siswa Lengkap</button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>