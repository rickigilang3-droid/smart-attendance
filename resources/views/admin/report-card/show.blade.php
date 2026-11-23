<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Siswa - {{ $student->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; padding: 0 !important; }
            .print-card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans p-6">

    <!-- Tombol Aksi Cetak Header (Tidak tampil saat diprint) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <a href="{{ route('admin.report-cards.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all">
            ⬅️ Kembali ke Dashboard Admin
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg transition-all flex items-center gap-2">
            🖨️ Cetak / Simpan PDF Rapor
        </button>
    </div>

    <!-- Lembar Dokumen Rapor -->
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-3xl shadow-xl border border-slate-200 print-card space-y-6">
        
        <!-- KOP SEKOLAH -->
        <div class="text-center border-b-2 border-slate-800 pb-4 space-y-2">
            <div class="flex justify-center mb-1">
                <img 
                    src="{{ storage_media_url('logo-sekolah/logo-sekolah.jpg') }}" 
                    onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/2991/2991148.png';" 
                    alt="Logo SMA XYZ JAKARTA" 
                    class="w-16 h-16 object-contain"
                >
            </div>
            <h1 class="text-xl font-black text-slate-900 tracking-wider uppercase">SMA XYZ JAKARTA</h1>
            <p class="text-xs text-slate-600">Jl. Pendidikan No. 123, Jakarta | Telp: (021) 555-0199 | Website: www.smpxyzjakarta.sch.id</p>
            <h2 class="text-sm font-bold text-slate-800 underline uppercase pt-2">LAPORAN HASIL BELAJAR SISWA (RAPOR)</h2>
        </div>

        <!-- INFORMASI SISWA -->
        <div class="grid grid-cols-2 text-xs gap-y-2 gap-x-6 border-b pb-4">
            <div>
                <span class="font-bold text-slate-600 inline-block w-28">Nama Siswa</span>
                <span class="font-bold text-slate-900">: {{ $student->name }}</span>
            </div>
            <div>
                <span class="font-bold text-slate-600 inline-block w-28">Kelas</span>
                <span class="font-bold text-slate-900">: {{ $student->schoolClass->name ?? '-' }}</span>
            </div>
            <div>
                <span class="font-bold text-slate-600 inline-block w-28">NIS / NISN</span>
                <span class="font-bold text-slate-900">: {{ $student->nisn ?? $student->nis ?? '-' }}</span>
            </div>
            <div>
                <span class="font-bold text-slate-600 inline-block w-28">Semester / Tahun</span>
                <span class="font-bold text-slate-900">: {{ $semester }} / 2025-2026</span>
            </div>
        </div>

        <!-- TABEL CAPAIAN NILAI MATA PELAJARAN -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold text-slate-800 uppercase">A. CAPAIAN AKADEMIK MATA PELAJARAN</h3>
            <table class="w-full text-left text-xs border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 uppercase font-bold text-[11px]">
                        <th class="border border-slate-300 p-2 text-center w-10">No</th>
                        <th class="border border-slate-300 p-2">Mata Pelajaran</th>
                        <th class="border border-slate-300 p-2 text-center w-16">Nilai Akhir</th>
                        <th class="border border-slate-300 p-2 text-center w-16">Predikat</th>
                        <th class="border border-slate-300 p-2">Capaian Kompetensi / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assessments as $index => $ass)
                        <tr>
                            <td class="border border-slate-300 p-2 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="border border-slate-300 p-2 font-bold text-slate-800">{{ $ass->subject_name }}</td>
                            <td class="border border-slate-300 p-2 text-center font-bold text-indigo-700">{{ number_format($ass->final_grade, 1) }}</td>
                            <td class="border border-slate-300 p-2 text-center font-bold">{{ $ass->letter_grade ?? '-' }}</td>
                            <td class="border border-slate-300 p-2 text-slate-700 text-[11px]">{{ $ass->teacher_notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-slate-300 p-4 text-center text-slate-400 italic">Nilai mata pelajaran belum tersedia / belum disetor guru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- REKAPITULASI KETIDAKHADIRAN -->
        <div class="space-y-2 pt-2">
            <h3 class="text-xs font-bold text-slate-800 uppercase">B. KETIDAKHADIRAN (PRESENSI)</h3>
            <div class="w-1/2">
                <table class="w-full text-left text-xs border-collapse border border-slate-300">
                    <tr class="border-b border-slate-200">
                        <td class="border border-slate-300 p-2 font-semibold">Sakit</td>
                        <td class="border border-slate-300 p-2 text-center font-bold">{{ $attendanceRecap['sakit'] }} hari</td>
                    </tr>
                    <tr class="border-b border-slate-200">
                        <td class="border border-slate-300 p-2 font-semibold">Izin</td>
                        <td class="border border-slate-300 p-2 text-center font-bold">{{ $attendanceRecap['izin'] }} hari</td>
                    </tr>
                    <tr>
                        <td class="border border-slate-300 p-2 font-semibold">Tanpa Keterangan (Alpha)</td>
                        <td class="border border-slate-300 p-2 text-center font-bold">{{ $attendanceRecap['alpha'] }} hari</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <div class="pt-12 grid grid-cols-2 text-center text-xs">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold mb-16">Wali Kelas</p>
                <p class="font-bold underline">( ________________________ )</p>
            </div>
            <div>
                <p>Jakarta, {{ date('d F Y') }}</p>
                <p class="font-bold mb-16">Kepala Sekolah</p>
                <p class="font-bold underline">Drs. Hasan Basri, M.Pd.I</p>
            </div>
        </div>
    </div>

</body>
</html>