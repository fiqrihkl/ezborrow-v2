<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Guru;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index()
    {
        $mapels = Mapel::with('gurus')->latest()->paginate(10);
        return view('admin.mapel.index', compact('mapels'));
    }

    public function create()
    {
        $gurus = Guru::all();
        return view('admin.mapel.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel',
            'kode_mapel' => 'required|unique:mapels,kode_mapel',
            'guru_ids'   => 'required|array'
        ]);

        $mapel = Mapel::create($request->only(['nama_mapel', 'kode_mapel', 'deskripsi']));
        $mapel->gurus()->attach($request->guru_ids);

        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil dibuat.');
    }

    public function edit(Mapel $mapel)
    {
        $gurus = Guru::all();
        $selectedGurus = $mapel->gurus->pluck('id')->toArray();
        return view('admin.mapel.edit', compact('mapel', 'gurus', 'selectedGurus'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel,' . $mapel->id,
            'kode_mapel' => 'required|unique:mapels,kode_mapel,' . $mapel->id,
            'guru_ids'   => 'required|array'
        ]);

        // Mengupdate semua kolom yang ada di form
        $mapel->update($request->only(['nama_mapel', 'kode_mapel', 'deskripsi']));
        
        // Sinkronisasi Guru Pengampu
        $mapel->gurus()->sync($request->guru_ids);

        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil diperbarui.');
    }

    public function destroy(Mapel $mapel)
    {
        try {
            // 1. Hapus relasi dengan Guru di tabel pivot (guru_mapel)
            $mapel->gurus()->detach();

            // 2. Hapus semua data peminjaman yang terhubung dengan mapel ini
            // Ini akan mencegah error 'Integrity constraint violation'
            if ($mapel->peminjamans()->exists()) {
                $mapel->peminjamans()->delete();
            }

            // 3. Terakhir, hapus data Mapel itu sendiri
            $mapel->delete();

            return back()->with('success', 'Mapel dan semua riwayat terkait berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}