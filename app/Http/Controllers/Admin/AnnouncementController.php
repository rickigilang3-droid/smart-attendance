<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAnnouncement($request);

        $announcement = new Announcement();
        $announcement->title     = $validated['title'];
        $announcement->category  = mb_strtoupper(trim($validated['category']));
        $announcement->target    = $validated['target']; // <--- Menyimpan sasaran pengumuman
        $announcement->content   = $validated['content'];
        $announcement->date      = $validated['date'];
        $announcement->is_active = true;
        $announcement->save();

        return redirect()->back()->with('success', 'Pengumuman sekolah berhasil diterbitkan!');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $this->validateAnnouncement($request);

        $announcement->title    = $validated['title'];
        $announcement->category = mb_strtoupper(trim($validated['category']));
        $announcement->target   = $validated['target']; // <--- Memperbarui sasaran pengumuman
        $announcement->content  = $validated['content'];
        $announcement->date     = $validated['date'];
        $announcement->save();

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus!');
    }

    /**
     * Validasi input dan kembalikan data yang sudah tervalidasi.
     *
     * @return array{title: string, category: string, target: string, content: string, date: string}
     */
    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'target'   => 'required|in:all,teacher,parent', // <--- Validasi target sasaran
            'content'  => 'required|string',
            'date'     => 'required|date',
        ]);
    }
}