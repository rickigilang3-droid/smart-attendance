<x-app-layout>
    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-950/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-indigo-200 text-xs font-semibold backdrop-blur-md mb-3 border border-white/10">
                        <span>📢</span> PORTAL ORANG TUA / WALI SISWA
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">
                        Pengumuman Informasi Sekolah
                    </h1>
                    <p class="text-indigo-200 text-xs md:text-sm mt-1 max-w-2xl">
                        Dapatkan kabar, edaran resmi, dan informasi kegiatan sekolah terkini yang diterbitkan oleh pihak manajemen sekolah.
                    </p>
                </div>
            </div>

            <!-- Daftar Pengumuman Sekolah -->
            <div class="space-y-4">
                @if(isset($announcements) && count($announcements) > 0)
                    @foreach($announcements as $item)
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 hover:border-indigo-200 transition space-y-3">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <span>📌</span> Pengumuman Resmi
                                </span>
                                <span class="text-xs font-semibold text-slate-400">
                                    Diterbitkan: {{ !empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMMM YYYY • HH:mm') : '-' }} WIB
                                </span>
                            </div>

                            <h2 class="text-lg font-extrabold text-slate-800">
                                {{ $item->title ?? $item->judul ?? 'Pengumuman Sekolah' }}
                            </h2>

                            <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                                {{ $item->content ?? $item->isi ?? $item->description ?? '-' }}
                            </p>

                            @if(!empty($item->file_attachment) || !empty($item->attachment))
                                @php
                                    $filePath = $item->file_attachment ?? $item->attachment;
                                @endphp
                                <div class="pt-2">
                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-xs font-bold text-slate-700 transition border border-slate-200/80">
                                        <span>📎</span> Unduh Lampiran Berkas
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if(method_exists($announcements, 'hasPages') && $announcements->hasPages())
                        <div class="p-4 bg-white rounded-2xl border border-slate-200/80">
                            {{ $announcements->links() }}
                        </div>
                    @endif
                @else
                    <div class="bg-white p-12 rounded-3xl shadow-sm border border-slate-200/80 text-center text-slate-400">
                        <div class="text-4xl mb-3">📢</div>
                        <p class="text-sm font-bold text-slate-600">Belum Ada Pengumuman</p>
                        <p class="text-xs text-slate-400 mt-1">Pengumuman resmi dari sekolah akan ditampilkan di halaman ini.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>