<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Header -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800">Data Pengajar & Guru 👨‍🏫</h1>
                    <p class="text-xs text-slate-500 mt-1">Kelola data tenaga pengajar dan plotting mata pelajaran dasar.</p>
                </div>
                <a href="{{ route('admin.teachers.create') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-2xl shadow-md transition-all whitespace-nowrap">
                    + Tambah Pengajar
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Bar Pencarian Interaktif dengan Live Filter & Dropdown Suggestion -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <form action="{{ route('admin.teachers.index') }}" method="GET" class="relative">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                🔍
                            </div>
                            <input 
                                type="text" 
                                id="teacherSearchInput"
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Ketik nama guru, NIP, atau mata pelajaran..." 
                                autocomplete="off"
                                oninput="handleTeacherSearch(this.value)"
                                class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 rounded-2xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition font-medium text-slate-800"
                            >
                            <!-- Dropdown Live Search Suggestion -->
                            <div id="teacherSearchDropdown" class="hidden absolute z-30 left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 max-h-60 overflow-y-auto divide-y divide-slate-100">
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                                Cari Guru
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Data Pengajar -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse" id="teachersTable">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                            <th class="py-3.5 px-4">NIP</th>
                            <th class="py-3.5 px-4">Nama Lengkap</th>
                            <th class="py-3.5 px-4">Mata Pelajaran Utama</th>
                            <th class="py-3.5 px-4">No. Telepon / WA</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm" id="teachersTableBody">
                        @forelse($teachers as $teacher)
                            <tr class="hover:bg-slate-50 transition teacher-row" data-name="{{ strtolower($teacher->name) }}" data-nip="{{ $teacher->nip }}" data-subject="{{ strtolower($teacher->subject ?? '') }}">
                                <td class="py-4 px-4 font-mono font-bold text-slate-700">{{ $teacher->nip ?? '-' }}</td>
                                <td class="py-4 px-4 font-semibold text-slate-800">{{ $teacher->name }}</td>
                                <td class="py-4 px-4 font-semibold text-indigo-600">{{ $teacher->subject ?? '-' }}</td>
                                <td class="py-4 px-4 text-slate-600 font-mono text-xs">{{ $teacher->phone ?? '-' }}</td>
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="inline-block px-3 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl text-xs font-semibold transition">
                                        Edit
                                    </a>
                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus pengajar ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-semibold transition">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyTeacherRow">
                                <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Belum ada data pengajar yang terdaftar. Klik tombol Tambah Pengajar di atas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $teachers->links() }}
                </div>
            </div>

        </div>
    </div>

    <script>
        // Memuat SELURUH data guru untuk pencarian live auto-suggest lintas halaman
        const globalTeacherList = @json($allTeachers ?? []);

        function handleTeacherSearch(keyword) {
            const query = keyword.toLowerCase().trim();
            const dropdown = document.getElementById('teacherSearchDropdown');
            const rows = document.querySelectorAll('.teacher-row');

            // 1. Live Filter pada Tabel di halaman yang sedang aktif
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const nip = row.getAttribute('data-nip') || '';
                const subject = row.getAttribute('data-subject') || '';
                if (name.includes(query) || nip.includes(query) || subject.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            if (query.length === 0) {
                dropdown.innerHTML = '';
                dropdown.classList.add('hidden');
                return;
            }

            // 2. Pencarian LINTAS HALAMAN pada seluruh database guru
            const matches = globalTeacherList.filter(t => 
                (t.name && t.name.toLowerCase().includes(query)) || 
                (t.nip && t.nip.toLowerCase().includes(query)) ||
                (t.subject && t.subject.toLowerCase().includes(query))
            );

            if (matches.length > 0) {
                dropdown.innerHTML = '';
                matches.forEach(t => {
                    const item = document.createElement('div');
                    item.className = 'p-3 hover:bg-amber-50 cursor-pointer transition flex justify-between items-center text-xs';
                    const nipText = t.nip ? 'NIP: ' + t.nip : 'Tanpa NIP';
                    const subjectText = t.subject || 'Pengajar';
                    
                    item.innerHTML = '<div><span class="font-bold text-slate-800">' + t.name + '</span><span class="text-slate-400 text-[10px] ml-1 flex sm:inline mt-0.5 sm:mt-0">(' + nipText + ')</span></div><span class="text-[10px] font-bold px-2 py-0.5 bg-amber-100 text-amber-700 rounded-md">' + subjectText + '</span>';
                    
                    item.addEventListener('click', function() {
                        const searchInput = document.getElementById('teacherSearchInput');
                        searchInput.value = t.name;
                        dropdown.classList.add('hidden');
                        searchInput.form.submit();
                    });
                    dropdown.appendChild(item);
                });
                dropdown.classList.remove('hidden');
            } else {
                dropdown.innerHTML = '<div class="p-3 text-xs text-slate-400 text-center">Guru tidak ditemukan. Tekan Enter atau klik "Cari Guru".</div>';
                dropdown.classList.remove('hidden');
            }
        }

        // Sembunyikan dropdown jika klik di luar elemen
        document.addEventListener('click', function(e) {
            const searchInput = document.getElementById('teacherSearchInput');
            const dropdown = document.getElementById('teacherSearchDropdown');
            if (searchInput && dropdown && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>