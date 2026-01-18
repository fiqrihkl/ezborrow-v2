<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Menggunakan paginate(10) agar sesuai dengan tampilan index yang baru
        // withCount('siswas') digunakan untuk menghitung jumlah siswa secara otomatis
        $kelas = Kelas::with('wali')
                    ->withCount('siswas')
                    ->latest()
                    ->paginate(10);

        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        // Memuat semua guru untuk pilihan dropdown Wali Kelas
        $gurus = Guru::with('kelas')->get();
        return view('admin.kelas.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            // Guru hanya boleh menjadi wali di satu kelas (unique di tabel kelas)
            'guru_id'    => 'nullable|unique:kelas,guru_id|exists:gurus,id'
        ], [
            'nama_kelas.unique' => 'Nama kelas sudah terdaftar!',
            'guru_id.unique'    => 'Guru tersebut sudah menjadi wali kelas di kelas lain!'
        ]);

        Kelas::create($request->all());
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function edit(Kelas $kela)
    {
        // Mengambil data guru untuk pilihan wali kelas
        $gurus = Guru::with('kelas')->get();
        
        // Variabel dikirim dengan nama 'kelas' agar sesuai dengan file edit.blade.php
        return view('admin.kelas.edit', [
            'kelas' => $kela, 
            'gurus' => $gurus
        ]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $kela->id,
            // Abaikan pengecekan unique untuk guru_id milik kelas ini sendiri saat update
            'guru_id'    => 'nullable|unique:kelas,guru_id,' . $kela->id . '|exists:gurus,id'
        ], [
            'guru_id.unique' => 'Guru tersebut sudah menjadi wali kelas di kelas lain!'
        ]);

        $kela->update($request->all());
        return redirect()->route('kelas.index')->with('success', 'Data kelas diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        // Proteksi jika masih ada siswa di dalam kelas tersebut
        if($kela->siswas()->count() > 0) {
            return back()->with('error', 'Kelas tidak bisa dihapus karena masih ada siswanya!');
        }
        
        $kela->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}