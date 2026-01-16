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
    public function index(Request $request)
{
    // Mulai query dasar (Eager Loading kelas)
    $query = Siswa::with('kelas');

    // Fitur Filter Status (PENTING)
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    } else {
        // Default jika tidak ada filter status: Tampilkan Aktif & Nonaktif (Alumni disembunyikan)
        $query->whereIn('status', ['aktif', 'nonaktif']);
    }

    // Fitur Pencarian (Nama atau NIS)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama_siswa', 'like', "%$search%")
              ->orWhere('nis', 'like', "%$search%");
        });
    }

    // Fitur Filter Kelas
    if ($request->filled('kelas_id')) {
        $query->where('kelas_id', $request->kelas_id);
    }

    $siswas = $query->latest()->paginate(15)->withQueryString();
    $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();

    return view('admin.siswa.index', compact('siswas', 'kelases'));
}

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
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

        Siswa::create([
            'nama_siswa' => $request->nama_siswa,
            'nis'        => $request->nis,
            'kelas_id'   => $request->kelas_id,
            'status'     => 'aktif',
        ]);

        return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
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

        $data = $request->all();

        // CEK DATABASE: Jika database Ibu melarang NULL di kelas_id, 
        // hapus atau beri komentar pada blok IF di bawah ini.
        if (in_array($request->status, ['keluar', 'alumni'])) {
            // $data['kelas_id'] = null; // Aktifkan hanya jika database sudah NULLABLE
        }

        $siswa->update($data);
        return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil diperbarui.');
    }

    public function keluar($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->update(['status' => 'nonaktif']);
        return back()->with('success', 'Siswa ' . $siswa->nama_siswa . ' berhasil dinonaktifkan.');
    }

    public function destroy($id)
    {
        try {
            $siswa = Siswa::findOrFail($id);
            $siswa->delete();
            return back()->with('success', 'Data siswa berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus. Data ini mungkin terhubung dengan transaksi lain.');
        }
    }

    public function export(Request $request) 
    {
        $filters = [
            'search'   => $request->query('search'),
            'kelas_id' => $request->query('kelas_id')
        ];
        return Excel::download(new SiswaExport($filters), 'data-siswa.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        try {
            Excel::import(new SiswaImport($request->kelas_id), $request->file('file'));
            return back()->with('success', 'Data Siswa Berhasil Diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat import. Pastikan format kolom Excel sudah benar.');
        }
    }

    public function downloadTemplate()
    {
        $daftarKelas = Kelas::pluck('nama_kelas')->toArray();
        $stringKelas = implode(', ', $daftarKelas);

        $data = [
            ['nis', 'nama_siswa'], // Header disederhanakan karena kelas_id diambil dari modal
            ['10223001', 'Nama Siswa Contoh'],
            ['', ''],
            ['INFO: Kolom kelas tidak diperlukan karena Anda akan memilih kelas di sistem sebelum upload.'],
            ['DAFTAR KELAS TERSEDIA:'],
            [$stringKelas]
        ];
        
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            public function __construct(array $data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, 'template_siswa.xlsx');
    }
}