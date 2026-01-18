<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::latest()->paginate(10);
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip'       => 'nullable|unique:gurus,nip', 
        ], [
            'nama_guru.required' => 'Nama lengkap guru wajib diisi.',
            'nip.unique'         => 'NIP sudah terdaftar di sistem!',
        ]);

        Guru::create($validated);
        return redirect()->route('guru.index')->with('success', 'Data Guru berhasil ditambahkan.');
    }

    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip'       => 'nullable|unique:gurus,nip,' . $guru->id,
        ], [
            'nama_guru.required' => 'Nama lengkap guru wajib diisi.',
            'nip.unique'         => 'NIP sudah digunakan oleh guru lain!',
        ]);

        $guru->update($validated);
        return redirect()->route('guru.index')->with('success', 'Data Guru diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        try {
            DB::transaction(function () use ($guru) {
                // 1. Lepas jabatan sebagai Wali Kelas (Tabel Kelas)
                DB::table('kelas')->where('guru_id', $guru->id)->update(['guru_id' => null]);

                // 2. Hapus riwayat peminjaman terkait guru ini
                DB::table('peminjamans')->where('guru_id', $guru->id)->delete();

                // 3. Hapus data utama guru
                $guru->delete();
            });

            return redirect()->route('guru.index')->with('success', 'Data Guru dan seluruh riwayat terkait berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->route('guru.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}