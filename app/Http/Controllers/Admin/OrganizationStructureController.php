<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationStructureController extends Controller
{
    // Tampilan Bagan & Form Input Data Personalia
    public function index()
    {
        $members = OrganizationStructure::orderBy('order_priority', 'asc')->get();

        // Pengelompokan data berdasarkan level hirarki
        $komite        = $members->where('level', 'komite')->first();
        $kepalaSekolah = $members->where('level', 'kepala_sekolah')->first();
        $subPimpinan   = $members->where('level', 'sub_pimpinan');
        $wakas         = $members->where('level', 'waka');
        $bk            = $members->where('level', 'bk');
        $walas         = $members->where('level', 'wali_kelas');
        $guruMapel     = $members->where('level', 'guru_mapel');

        return view('admin.organization-structure.index', compact(
            'members',
            'komite',
            'kepalaSekolah',
            'subPimpinan',
            'wakas',
            'bk',
            'walas',
            'guruMapel'
        ));
    }

    // Simpan Personalia Baru (Termasuk Upload Foto)
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'position_title' => 'required|string|max:255',
            'level'          => 'required|string',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'order_priority' => 'nullable|integer',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('organization', 'public');
        }

        OrganizationStructure::create([
            'name'           => $request->name,
            'position_title' => $request->position_title,
            'level'          => $request->level,
            'photo'          => $photoPath,
            'order_priority' => $request->order_priority ?? 0,
        ]);

        return redirect()->back()->with('success', 'Personalia struktur berhasil ditambahkan!');
    }

    // Hapus Personalia & Foto File
    public function destroy($id)
    {
        $member = OrganizationStructure::findOrFail($id);

        if ($member->photo && Storage::disk('public')->exists($member->photo)) {
            Storage::disk('public')->delete($member->photo);
        }

        $member->delete();

        return redirect()->back()->with('success', 'Personalia berhasil dihapus!');
    }
}