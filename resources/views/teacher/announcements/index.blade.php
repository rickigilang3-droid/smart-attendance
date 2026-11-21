<x-app-layout>
    <div class="min-h-screen bg-slate-100/70 p-4 sm:p-6 lg:p-8 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header Utama -->
            <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm">
                <h1 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                    <span>Pengumuman Sekolah</span> 📢
                </h1>
                <p class="text-xs text-slate-500 mt-1">Informasi dan edaran resmi dari pihak manajemen sekolah.</p>
            </div>

            <!-- List Card Pengumuman -->
            <div class="space-y-4">
                @forelse($announcements as $announcement)
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                            <h2 class="text-base font-extrabold text-slate-800">{{ $announcement->title }}</h2>
                            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">
                                📅 {{ \Carbon\Carbon::parse($announcement->date)->format('d M Y') }}
                            </span>
                        </div>
                        <p class="text-xs leading-relaxed text-slate-600 pt-1">
                            {{ $announcement->content }}
                        </p>
                    </div>
                @empty
                    <div class="p-12 text-center bg-white rounded-3xl border border-slate-200/80 text-slate-400 text-xs space-y-2">
                        <p class="text-2xl">📢</p>
                        <p class="font-bold text-slate-600">Belum Ada Pengumuman</p>
                        <p>Saat ini belum ada pengumuman terbaru dari sekolah.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>