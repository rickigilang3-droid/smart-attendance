<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans" 
         x-data="{ 
            activeDay: 'Senin', 
            selectedClass: 'ALL' 
         }">
        
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header Utama -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                        <span>Jadwal Mengajar Saya</span> 📅
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Daftar plotting alokasi waktu mengajar harian Anda.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-2xl flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Navigation Bar: Tab Hari + Dropdown Filter Kelas -->
            <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Tab Hari (Senin - Jumat) -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                        <button 
                            @click="activeDay = '{{ $day }}'"
                            :class="activeDay === '{{ $day }}' 
                                ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20 font-bold' 
                                : 'bg-slate-50 text-slate-600 hover:bg-slate-100 font-semibold'"
                            class="px-4 py-2 rounded-xl text-xs transition-all duration-200 whitespace-nowrap">
                            {{ $day }}
                        </button>
                    @endforeach
                </div>

                <!-- Dropdown Filter Kelas -->
                <div class="flex items-center gap-2 min-w-[200px]">
                    <span class="text-xs font-bold text-slate-500 whitespace-nowrap">Filter Kelas:</span>
                    <select x-model="selectedClass" class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 text-slate-700 focus:ring-amber-500 focus:border-amber-500 py-2">
                        <option value="ALL">✨ Semua Kelas</option>
                        @foreach($schedules->pluck('schoolClass')->unique('id')->filter() as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Grid Card Jadwal Pelajaran Guru -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($schedules as $schedule)
                    @php
                        $isIstirahat = Str::contains(strtolower($schedule->subject), 'istirahat');
                    @endphp
                    <div x-show="activeDay === '{{ $schedule->day }}' && (selectedClass === 'ALL' || selectedClass == '{{ $schedule->class_id }}')"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="p-5 rounded-2xl border shadow-sm flex flex-col justify-between transition-all duration-200 relative overflow-hidden group
                                {{ $isIstirahat ? 'bg-amber-50/60 border-amber-200/80 hover:border-amber-400' : 'bg-white border-slate-200/80 hover:border-indigo-400 hover:shadow-md' }}">
                        
                        <!-- Accent Line Samping -->
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $isIstirahat ? 'bg-amber-400' : 'bg-indigo-500' }}"></div>
                        
                        <div class="space-y-3 pl-2">
                            <!-- Top Info: Nama Kelas & Jam Pelajaran -->
                            <div class="flex justify-between items-center">
                                <span class="px-2.5 py-1 rounded-lg font-bold text-[10px] uppercase tracking-wider
                                            {{ $isIstirahat ? 'bg-amber-200/60 text-amber-800' : 'bg-indigo-50 text-indigo-600' }}">
                                    {{ $schedule->schoolClass->name ?? 'Semua Kelas' }}
                                </span>
                                <div class="flex items-center gap-1 text-xs font-bold {{ $isIstirahat ? 'text-amber-700' : 'text-slate-600' }}">
                                    <span>⏱️</span>
                                    <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                </div>
                            </div>

                            <!-- Body Info: Nama Mata Pelajaran & Guru -->
                            <div class="pt-1">
                                <h3 class="text-base font-extrabold {{ $isIstirahat ? 'text-amber-900' : 'text-slate-800' }}">
                                    {{ $schedule->subject }}
                                </h3>
                                
                                <p class="text-xs font-medium mt-1 flex items-center gap-1.5 {{ $isIstirahat ? 'text-amber-700/80' : 'text-slate-500' }}">
                                    @if($isIstirahat)
                                        <span>☕ Jam Istirahat / Non-KBM</span>
                                    @else
                                        <span>👨‍🏫</span>
                                        <span>{{ $schedule->teacher->name ?? ($teacher->name ?? 'Pengajar') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Card Footer Info -->
                        <div class="mt-4 pt-3 border-t {{ $isIstirahat ? 'border-amber-200/60' : 'border-slate-100' }} flex justify-between items-center pl-2">
                            <span class="text-[10px] text-slate-400 font-medium">Hari: {{ $schedule->day }}</span>
                            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold">Aktif</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-12 text-center bg-white rounded-3xl border border-slate-200/80 text-slate-400 text-xs space-y-2">
                        <p class="text-2xl">📅</p>
                        <p class="font-bold text-slate-600">Belum Ada Jadwal Mengajar</p>
                        <p>Anda belum memiliki alokasi jam mengajar yang diplotting oleh Administrator.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>