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
        $gurus = Guru::all(); // Untuk pilihan guru di form
        return view('admin.mapel.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel',
            'kode_mapel' => 'required|unique:mapels,kode_mapel', // Tambahkan ini
            'guru_ids'   => 'required|array'
        ]);

        // Simpan semua data termasuk deskripsi jika ada
        $mapel = Mapel::create($request->only(['nama_mapel', 'kode_mapel', 'deskripsi']));
        
        $mapel->gurus()->attach($request->guru_ids);

        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil dibuat.');
    }

    public function edit(Mapel $mapel)
    {
        $gurus = Guru::all();
        $selectedGurus = $mapel->gurus->pluck('id')->toArray(); // Ambil ID guru yang sudah terpilih
        return view('admin.mapel.edit', compact('mapel', 'gurus', 'selectedGurus'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mapels,nama_mapel,' . $mapel->id,
            'guru_ids'   => 'required|array'
        ]);

        $mapel->update(['nama_mapel' => $request->nama_mapel]);
        
        // Sync akan menghapus yang lama dan mengganti dengan yang baru di pivot
        $mapel->gurus()->sync($request->guru_ids);

        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil diupdate.');
    }

    public function destroy(Mapel $mapel)
    {
        $mapel->gurus()->detach(); // Hapus relasi di pivot dulu
        $mapel->delete();
        return back()->with('success', 'Mapel dihapus.');
    }
}