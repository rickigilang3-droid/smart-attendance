<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- Header Utama --}}
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <a href="{{ route('admin.classes.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">← Kembali ke Data Kelas</a>
                    <h1 class="text-2xl font-extrabold text-slate-800 mt-1">Daftar Siswa Kelas {{ $class->name }} 🏫</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Tingkat {{ $class->grade_level }} • Total: {{ $class->students->count() }} Siswa Terdaftar
                    </p>
                </div>
                <a href="{{ route('admin.students.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-md transition">
                    + Tambah Siswa Baru
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Card Informasi & Penetapan Wali Kelas --}}
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 border-2 border-indigo-100 overflow-hidden flex-shrink-0 shadow-sm">
                            @if($class->homeroomTeacher)
                                <img src="{{ $class->homeroomTeacher->photo_url }}" alt="Wali Kelas" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-2xl font-bold bg-slate-50">
                                    👨‍🏫
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 rounded-md border border-indigo-100">
                                    Wali Kelas
                                </span>
                                @if($class->homeroomTeacher)
                                    <span class="text-xs text-slate-500">NIP: {{ $class->homeroomTeacher->nip ?? '-' }}</span>
                                @endif
                            </div>
                            <h2 class="text-lg font-extrabold text-slate-800 mt-1">
                                {{ $class->homeroomTeacher->name ?? 'Belum Ditentukan' }}
                            </h2>
                            <p class="text-xs text-slate-500">
                                {{ $class->homeroomTeacher ? 'Pengampu: ' . $class->homeroomTeacher->subject : 'Pilih salah satu guru terdaftar di bawah ini untuk ditetapkan sebagai wali kelas.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Form Tetapkan / Ubah Wali Kelas --}}
                    <div class="w-full md:w-auto bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                        <form action="{{ route('admin.classes.assign-homeroom', $class->id) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                                    Pilih Wali Kelas
                                </label>
                                <select name="teacher_id" class="text-xs rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64">
                                    <option value="">-- Tanpa Wali Kelas --</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ $class->teacher_id == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }} ({{ $teacher->subject ?? 'Guru' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="sm:self-end px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                                Simpan Wali Kelas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tabel Daftar Siswa --}}
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <h3 class="text-base font-extrabold text-slate-800 mb-4">Daftar Siswa Terdaftar 👨‍🎓</h3>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">NISN</th>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Jenis Kelamin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($class->students as $index => $student)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-4 px-4 font-mono font-bold text-slate-700">{{ $student->nisn }}</td>
                                <td class="py-4 px-4 font-semibold text-slate-800">{{ $student->name }}</td>
                                <td class="py-4 px-4 text-slate-600">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $student->gender == 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                        {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada siswa yang terdaftar di kelas {{ $class->name }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>