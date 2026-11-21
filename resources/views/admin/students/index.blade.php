<x-app-layout>
    {{-- Eager load relasi kelas sekali saja --}}
    @php($students->loadMissing('schoolClass'))
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Header -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800">Data Master Siswa 👥</h1>
                    <p class="text-xs text-slate-500 mt-1">Sistem Informasi Master Siswa terintegrasi standar Dapodik.</p>
                </div>
                <a href="{{ route('admin.students.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-md transition-all whitespace-nowrap">
                    + Tambah Siswa Baru
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Bar Pencarian Interaktif -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <form id="studentSearchForm" action="{{ route('admin.students.index') }}" method="GET" class="relative">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                🔍
                            </div>
                            <input 
                                type="text" 
                                id="studentSearchInput"
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Ketik nama siswa atau NISN..." 
                                autocomplete="off"
                                oninput="handleSearchInput(this.value)"
                                class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 rounded-2xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium text-slate-800"
                            >
                            <!-- Dropdown Live Search Suggestion -->
                            <div id="searchDropdown" class="hidden absolute z-30 left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 max-h-60 overflow-y-auto divide-y divide-slate-100">
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                                Cari Siswa
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.students.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Data Siswa -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse" id="studentsTable">
                    <thead>
                        <tr class="text-xs text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                            <th class="py-3.5 px-4">NISN</th>
                            <th class="py-3.5 px-4">Nama Lengkap</th>
                            <th class="py-3.5 px-4">L/P</th>
                            <th class="py-3.5 px-4">Kelas</th>
                            <th class="py-3.5 px-4">No HP Ortu</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm" id="studentsTableBody">
                        @forelse ($students as $item)
                            <tr class="hover:bg-slate-50 transition student-row" data-name="{{ strtolower($item->name) }}" data-nisn="{{ $item->nisn }}">
                                <td class="py-4 px-4 font-mono font-bold text-slate-700">{{ $item->nisn ?? '-' }}</td>
                                <td class="py-4 px-4 font-semibold">
                                    <button
                                        type="button"
                                        data-student="{{ json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                        onclick="openStudentModal(this)"
                                        class="text-indigo-600 hover:text-indigo-800 font-bold hover:underline text-left cursor-pointer">
                                        {{ $item->name }}
                                    </button>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $item->gender == 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                        {{ $item->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-700 font-medium">{{ $item->schoolClass->name ?? '-' }}</td>
                                <td class="py-4 px-4 text-slate-600 font-mono text-xs">{{ $item->parent_phone ?? '-' }}</td>
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('admin.students.edit', $item->id) }}" class="inline-block px-3 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl text-xs font-semibold transition">
                                        Edit
                                    </a>
                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.students.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus siswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-semibold transition">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyTableRow">
                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">Belum ada data siswa terdaftar. Klik tombol Tambah Siswa Baru di atas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $students->links() }}
                </div>
            </div>

            <!-- POP-UP MODAL JUMBO DENGAN TAB -->
            <dialog id="studentModal" class="p-0 rounded-3xl backdrop:bg-slate-900/60 backdrop:backdrop-blur-md shadow-2xl border-0 w-full max-w-4xl my-auto mx-auto">
                <div class="bg-white p-6 space-y-6 relative">
                    <button type="button" onclick="document.getElementById('studentModal').close()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 font-bold text-xl cursor-pointer">✕</button>
                    <!-- Header Profile -->
                    <div class="flex items-center gap-5 pb-4 border-b border-slate-100">
                        <div id="mPhoto" class="w-20 h-20 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center font-bold text-indigo-600 overflow-hidden text-3xl flex-shrink-0"></div>
                        <div>
                            <h2 id="mName" class="text-2xl font-black text-slate-800"></h2>
                            <p class="text-xs text-slate-500 mt-1">
                                NISN: <span id="mNisn" class="font-mono font-bold text-slate-700"></span> •
                                NIS: <span id="mNis" class="font-bold text-slate-700"></span> •
                                Kelas: <span id="mClass" class="font-bold text-indigo-600"></span>
                            </p>
                        </div>
                    </div>
                    <!-- Navigasi Tab -->
                    <div class="flex border-b border-slate-200 text-xs font-bold gap-6">
                        <button type="button" onclick="switchTab('tab-pribadi')" id="btn-tab-pribadi" class="pb-2 border-b-2 border-indigo-600 text-indigo-600">Data Pribadi</button>
                        <button type="button" onclick="switchTab('tab-periodik')" id="btn-tab-periodik" class="pb-2 text-slate-400 border-b-2 border-transparent hover:text-slate-600">Periodik & Sekolah</button>
                        <button type="button" onclick="switchTab('tab-ortu')" id="btn-tab-ortu" class="pb-2 text-slate-400 border-b-2 border-transparent hover:text-slate-600">Orang Tua & Kontak</button>
                        <button type="button" onclick="switchTab('tab-kesejahteraan')" id="btn-tab-kesejahteraan" class="pb-2 text-slate-400 border-b-2 border-transparent hover:text-slate-600">Kesejahteraan & Beasiswa</button>
                    </div>
                    <!-- Isi Tab Content -->
                    <div id="tab-pribadi" class="tab-content grid grid-cols-3 gap-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">NIK / KK</span><span id="mNik" class="font-bold text-slate-700 break-all"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">TTL</span><span id="mTtl" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Agama</span><span id="mAgama" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl col-span-2"><span class="text-slate-400 block mb-1">Alamat</span><span id="mAlamat" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">RT/RW - Kel/Kec</span><span id="mWilayah" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Status Tempat Tinggal</span><span id="mStatusTinggal" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Transportasi</span><span id="mTransport" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Anak Ke / Sdr</span><span id="mSaudara" class="font-bold text-slate-700"></span></div>
                    </div>
                    <div id="tab-periodik" class="tab-content hidden grid grid-cols-3 gap-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Tinggi / Berat Badan</span><span id="mTbBb" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Jarak ke Sekolah</span><span id="mJarak" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Waktu Tempuh</span><span id="mWaktu" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl col-span-2"><span class="text-slate-400 block mb-1">Sekolah Asal</span><span id="mSekolahAsal" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">No Ijazah / SKHU</span><span id="mIjazah" class="font-bold text-slate-700"></span></div>
                    </div>
                    <div id="tab-ortu" class="tab-content hidden grid grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Ayah Kandung</span><span id="mAyah" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Pekerjaan & Gaji Ayah</span><span id="mKerjaAyah" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Ibu Kandung</span><span id="mIbu" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Pekerjaan & Gaji Ibu</span><span id="mKerjaIbu" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">No HP Ortu / WA</span><span id="mHpOrtu" class="font-bold text-indigo-600"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Email Siswa/Ortu</span><span id="mEmail" class="font-bold text-slate-700"></span></div>
                    </div>
                    <div id="tab-kesejahteraan" class="tab-content hidden grid grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">No. KIP / PIP</span><span id="mKip" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">No. PKH / KPS</span><span id="mPkh" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Riwayat Prestasi</span><span id="mPrestasi" class="font-bold text-slate-700"></span></div>
                        <div class="p-3 bg-slate-50 rounded-2xl"><span class="text-slate-400 block mb-1">Beasiswa</span><span id="mBeasiswa" class="font-bold text-slate-700"></span></div>
                    </div>
                    <div class="text-right pt-4 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('studentModal').close()" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-2xl text-xs hover:bg-slate-200 transition">Tutup Detail</button>
                    </div>
                </div>
            </dialog>
        </div>
    </div>

    <script>
        // Memuat SELURUH data siswa dari server
        const globalStudentList = @json($allStudents);

        function handleSearchInput(keyword) {
            const query = keyword.toLowerCase().trim();
            const dropdown = document.getElementById('searchDropdown');
            const rows = document.querySelectorAll('.student-row');

            // 1. Live Filter untuk tabel di halaman aktif
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const nisn = row.getAttribute('data-nisn') || '';
                if (name.includes(query) || nisn.includes(query)) {
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

            // 2. Cari di SELURUH DATABASE SISWA (Mencakup Semua Halaman)
            const matches = globalStudentList.filter(s => 
                (s.name && s.name.toLowerCase().includes(query)) || 
                (s.nisn && s.nisn.toLowerCase().includes(query))
            );

            if (matches.length > 0) {
                dropdown.innerHTML = '';
                matches.forEach(s => {
                    const item = document.createElement('div');
                    item.className = 'p-3 hover:bg-indigo-50 cursor-pointer transition flex justify-between items-center text-xs';
                    const className = s.school_class ? s.school_class.name : (s.schoolClass ? s.schoolClass.name : '-');
                    const nisnText = s.nisn || 'No NISN';
                    
                    item.innerHTML = '<div><span class="font-bold text-slate-800">' + s.name + '</span><span class="text-slate-400 text-[10px] ml-1">(' + nisnText + ')</span></div><span class="text-[10px] font-bold px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-md">' + className + '</span>';
                    
                    item.addEventListener('click', function() {
                        selectSuggestedStudent(s);
                    });
                    dropdown.appendChild(item);
                });
                dropdown.classList.remove('hidden');
            } else {
                dropdown.innerHTML = '<div class="p-3 text-xs text-slate-400 text-center">Siswa tidak ditemukan. Tekan Enter atau klik "Cari Siswa".</div>';
                dropdown.classList.remove('hidden');
            }
        }

        function selectSuggestedStudent(studentData) {
            document.getElementById('searchDropdown').classList.add('hidden');
            const searchInput = document.getElementById('studentSearchInput');
            if (searchInput) {
                searchInput.value = studentData.name;
            }
            // Submit form untuk filter tabel secara otomatis
            document.getElementById('studentSearchForm').submit();
        }

        document.addEventListener('click', function(e) {
            const searchInput = document.getElementById('studentSearchInput');
            const dropdown = document.getElementById('searchDropdown');
            if (searchInput && dropdown && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function val(v) {
            return (v === null || v === undefined || v === '' || v === 'null') ? '-' : v;
        }

        function fmtDate(v) {
            if (!v) return '-';
            return String(v).substring(0, 10);
        }

        function setText(id, text) {
            const el = document.getElementById(id);
            if (el) el.textContent = text;
        }

        function openStudentModal(btnOrObj) {
            let data = btnOrObj;
            if (btnOrObj && typeof btnOrObj.getAttribute === 'function') {
                data = JSON.parse(btnOrObj.getAttribute('data-student'));
            }
            
            setText('mName', val(data.name));
            setText('mNisn', val(data.nisn));
            setText('mNis', val(data.nis));
            
            const className = data.school_class ? data.school_class.name : (data.schoolClass ? data.schoolClass.name : '-');
            setText('mClass', val(className));
            
            setText('mNik', val(data.nik) + ' / ' + val(data.kk || data.no_kk));
            setText('mTtl', val(data.pob) + ', ' + fmtDate(data.dob));
            setText('mAgama', val(data.religion));
            setText('mAlamat', val(data.address));
            
            const wil = val(data.rt_rw) + ' - ' + val(data.village_subdistrict || data.subdistrict);
            setText('mWilayah', wil);
            setText('mStatusTinggal', val(data.residence_status || data.residence_type));
            setText('mTransport', val(data.transportation));
            setText('mSaudara', 'Anak ke-' + val(data.child_number || data.child_order) + ' dari ' + val(data.total_siblings || data.siblings_count) + ' bersaudara');
            setText('mTbBb', val(data.height) + ' cm / ' + val(data.weight) + ' kg');
            setText('mJarak', val(data.distance_to_school) + ' km');
            setText('mWaktu', val(data.travel_time || data.travel_time_minutes) + ' menit');
            setText('mSekolahAsal', val(data.previous_school));
            setText('mIjazah', val(data.diploma_skhu_number || data.ijazah_number));
            setText('mAyah', val(data.father_name));
            setText('mKerjaAyah', val(data.father_job) + ' (' + val(data.father_income) + ')');
            setText('mIbu', val(data.mother_name));
            setText('mKerjaIbu', val(data.mother_job) + ' (' + val(data.mother_income) + ')');
            setText('mHpOrtu', val(data.parent_phone));
            setText('mEmail', val(data.parent_email || data.email));
            setText('mKip', val(data.kip_pip_number || data.kip_number));
            setText('mPkh', val(data.pkh_kps_number));
            setText('mPrestasi', val(data.achievement_history || data.achievements));
            setText('mBeasiswa', val(data.scholarship || data.scholarships));

            const photoBox = document.getElementById('mPhoto');
            if (photoBox) {
                photoBox.innerHTML = '';
                if (data.photo) {
                    const img = document.createElement('img');
                    img.src = '/storage/' + data.photo;
                    img.className = 'w-full h-full object-cover';
                    photoBox.appendChild(img);
                } else {
                    photoBox.textContent = '🧑‍🎓';
                }
            }

            switchTab('tab-pribadi');
            document.getElementById('studentModal').showModal();
        }

        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            const target = document.getElementById(tabId);
            if (target) target.classList.remove('hidden');

            document.querySelectorAll('[id^="btn-tab-"]').forEach(btn => {
                btn.classList.remove('border-indigo-600', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-slate-400');
            });

            const activeBtn = document.getElementById('btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-slate-400');
                activeBtn.classList.add('border-indigo-600', 'text-indigo-600');
            }
        }
    </script>
</x-app-layout> 