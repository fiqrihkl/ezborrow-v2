<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function index()
    {
        // Hanya tampilkan siswa yang masih sekolah (aktif/nonaktif skorsing)
        // Gunakan Eager Loading 'kelas' agar query database efisien
        $siswas = Siswa::with('kelas')
            ->whereIn('status', ['aktif', 'nonaktif'])
            ->latest()
            ->paginate(15);

        return view('admin.siswa.index', compact('siswas'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis'        => 'required|unique:siswas,nis',
            'kelas_id'   => 'required|exists:kelas,id',
        ], [
            'nis.unique' => 'NIS ini sudah terdaftar!',
            'kelas_id.required' => 'Wajib memilih kelas untuk siswa baru.'
        ]);

        // unique_id tidak divalidasi di sini karena sudah dibuat otomatis di Model (boot method)
        Siswa::create([
            'nama_siswa' => $request->nama_siswa,
            'nis'        => $request->nis,
            'kelas_id'   => $request->kelas_id,
            'status'     => 'aktif', // Default siswa baru adalah aktif
        ]);

        return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis'        => 'required|unique:siswas,nis,' . $siswa->id,
            'kelas_id'   => 'nullable|exists:kelas,id',
            'status'     => 'required|in:aktif,nonaktif,alumni,keluar',
        ]);

        // Jika status diubah ke 'keluar' atau 'alumni', lepaskan kelasnya
        $data = $request->all();
        if (in_array($request->status, ['keluar', 'alumni'])) {
            $data['kelas_id'] = null;
        }

        $siswa->update($data);
        return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil diperbarui.');
    }

    /**
     * Fitur Keluarkan Siswa (Update Status jadi Nonaktif)
     */
    public function keluar($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->update(['status' => 'nonaktif']);

        return back()->with('success', 'Siswa ' . $siswa->nama_siswa . ' berhasil dinonaktifkan.');
    }

    /**
     * Fitur Hapus Permanen
     */
    public function destroy($id)
    {
        try {
            $siswa = Siswa::findOrFail($id);
            $siswa->delete();

            return back()->with('success', 'Data siswa berhasil dihapus permanen dari sistem.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data. Siswa mungkin masih memiliki riwayat peminjaman.');
        }
    }

    public function export() 
    {
        return Excel::download(new SiswaExport, 'data-siswa.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new SiswaImport, $request->file('file'));
        
        return back()->with('success', 'Data Siswa Berhasil Diimport!');
    }

    public function downloadTemplate()
    {
        // Ambil semua nama kelas yang ada untuk dijadikan panduan
        $daftarKelas = Kelas::pluck('nama_kelas')->toArray();
        $stringKelas = implode(', ', $daftarKelas);

        $data = [
            ['nis', 'nama_siswa', 'nama_kelas', 'unique_id'], // Baris 1: Header
            ['10223001', 'Contoh Nama Siswa', $daftarKelas[0] ?? 'Isi Sesuai Daftar', ''], // Baris 2: Contoh
            ['', '', '', ''],
            ['--- DAFTAR NAMA KELAS YANG TERDAFTAR (JANGAN DIHAPUS) ---'],
            [$stringKelas]
        ];
        
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            public function __construct(array $data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, 'template_siswa.xlsx');
    }
}