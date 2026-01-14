<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Menghitung jumlah siswa di setiap kelas dan memuat data wali
        $kelas = Kelas::with('wali')->withCount('siswas')->get();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        // Memuat semua guru beserta relasi kelasnya untuk pengecekan di dropdown
        $gurus = Guru::with('kelas')->get();
        return view('admin.kelas.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            // Memastikan satu guru tidak bisa jadi wali di dua kelas
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
        // Tetap memuat semua guru agar bisa menampilkan status mereka di dropdown
        $gurus = Guru::with('kelas')->get();
        return view('admin.kelas.edit', ['kelas' => $kela, 'gurus' => $gurus]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $kela->id,
            // Kecualikan ID kelas ini sendiri agar saat simpan ulang tidak dianggap duplikat
            'guru_id'    => 'nullable|unique:kelas,guru_id,' . $kela->id . '|exists:gurus,id'
        ], [
            'guru_id.unique' => 'Guru tersebut sudah menjadi wali kelas di kelas lain!'
        ]);

        $kela->update($request->all());
        return redirect()->route('kelas.index')->with('success', 'Data kelas diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        // Proteksi agar kelas yang ada siswanya tidak bisa dihapus sembarangan
        if($kela->siswas()->count() > 0) {
            return back()->with('error', 'Kelas tidak bisa dihapus karena masih ada siswanya!');
        }
        
        $kela->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}