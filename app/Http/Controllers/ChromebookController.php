<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chromebook;

class ChromebookController extends Controller
{
    /**
     * Menampilkan daftar semua unit Chromebook.
     */
    public function index()
    {
        $units = Chromebook::latest()->paginate(10);
        return view('admin.chromebook.index', compact('units'));
    }

    /**
     * Menampilkan form untuk tambah unit baru.
     * Inilah yang tadi menyebabkan error karena fungsinya tidak ada.
     */
    public function create()
    {
        return view('admin.chromebook.create');
    }

    /**
     * Menyimpan data unit baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'qr_code_unit' => 'required|unique:chromebooks',
            'merek' => 'required',
            // Tambahkan validasi posisi_loker jika perlu
        ]);

        Chromebook::create([
            'qr_code_unit' => $request->qr_code_unit,
            'no_unit'      => $request->qr_code_unit, 
            'merek'        => $request->merek,
            'status'       => 'tersedia',
            'loker'        => $request->posisi_loker, // Pastikan input 'posisi_loker' disimpan ke kolom 'loker'
        ]);

        return redirect()->route('chromebook.index')->with('success', 'Unit berhasil didaftarkan.');
    }
    /**
     * Menampilkan form untuk edit data unit.
     */
    public function edit(Chromebook $chromebook)
    {
        return view('admin.chromebook.edit', compact('chromebook'));
    }

    /**
     * Memperbarui data unit di database.
     */
    public function update(Request $request, Chromebook $chromebook)
    {
        $validated = $request->validate([
            'no_unit'      => 'required|unique:chromebooks,no_unit,' . $chromebook->id,
            'merek'        => 'required',
            'qr_code_unit' => 'required|unique:chromebooks,qr_code_unit,' . $chromebook->id,
            'loker'        => 'required',
            'status'       => 'required|in:tersedia,dipinjam,rusak',
        ]);

        $chromebook->update($validated);
        return redirect()->route('chromebook.index')->with('success', 'Data unit diperbarui.');
    }

    /**
     * Menghapus unit dari database.
     */
    public function destroy(Chromebook $chromebook)
    {
        // Opsional: Beri proteksi agar unit yang sedang 'dipinjam' tidak bisa dihapus
        if ($chromebook->status == 'dipinjam') {
            return back()->with('error', 'Unit sedang dipinjam, tidak dapat dihapus!');
        }

        $chromebook->delete();
        return redirect()->route('chromebook.index')->with('success', 'Unit berhasil dihapus.');
    }

    public function show($id)
    {
        // Mengambil data unit chromebook beserta histori peminjamannya (Eager Loading)
        $unit = Chromebook::with(['peminjamans.siswa', 'peminjamans.guru', 'peminjamans.mapel'])
            ->findOrFail($id);

        // Ambil riwayat peminjaman terbaru di atas
        $histori = $unit->peminjamans()->latest()->paginate(10);

        return view('admin.chromebook.show', compact('unit', 'histori'));
    }
}