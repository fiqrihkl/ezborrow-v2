<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');

        // Fitur Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            // Default: Tampilkan semua kecuali yang kemungkinan besar tidak perlu (opsional)
            // Jika ingin default hanya Aktif & Skorsing (nonaktif):
            $query->whereIn('status', ['aktif', 'nonaktif']);
        }

        // Fitur Pencarian (Nama, NIS, atau Unique ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('unique_id', 'like', "%$search%");
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
            'unique_id'  => 'nullable|unique:siswas,unique_id'
        ], [
            'nis.unique' => 'NIS ini sudah terdaftar!',
            'unique_id.unique' => 'QR Code ini sudah digunakan!',
            'kelas_id.required' => 'Wajib memilih kelas untuk siswa baru.'
        ]);

        Siswa::create([
            'nama_siswa' => $request->nama_siswa,
            'nis'        => $request->nis,
            'unique_id'  => $request->unique_id ?? 'S' . time() . rand(10,99),
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
            'unique_id'  => 'nullable|unique:siswas,unique_id,' . $siswa->id,
        ]);

        $siswa->update($request->all());
        return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $siswa = Siswa::findOrFail($id);
                // Hapus riwayat peminjaman (mencegah error foreign key)
                DB::table('peminjamans')->where('siswa_id', $id)->delete();
                $siswa->delete();
            });
            return back()->with('success', 'Data siswa dan riwayat peminjamannya berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data siswa.');
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

    /**
     * PERBAIKAN UTAMA: Fitur Import dengan Validasi Mendalam
     */
    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'kelas_id' => 'required|exists:kelas,id'
        ], [
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv',
            'kelas_id.required' => 'Pilih kelas tujuan import terlebih dahulu.'
        ]);

        try {
            Excel::import(new SiswaImport($request->kelas_id), $request->file('file'));
            
            return back()->with('success', 'Data Siswa Berhasil Diimport!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorDetails = [];
            
            foreach ($failures as $failure) {
                $errorDetails[] = "Baris " . $failure->row() . " (" . $failure->attribute() . "): " . $failure->errors()[0];
            }

            // Simpan error detail ke session untuk ditampilkan di view
            return back()->with('error_import', $errorDetails)
                         ->with('error', 'Gagal mengimpor file. Periksa detail kesalahan di bawah.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Jika ada Integrity constraint violation (Misal: NIS Duplikat di DB)
            if ($e->errorInfo[1] == 1062) {
                return back()->with('error', 'Gagal: Ada data NIS atau QRCode yang sudah terdaftar di sistem.');
            }
            return back()->with('error', 'Terjadi kesalahan database saat import.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $data = [
            ['nis', 'nama_lengkap', 'status', 'qrcode'], // Header harus sesuai dengan mapping di SiswaImport
            ['10223001', 'Puspawati', 'aktif', 'QR001'],
            ['10223002', 'Budi Santoso', 'aktif', 'QR002'],
        ];
        
        return Excel::download(new class($data) implements FromArray {
            protected $data;
            public function __construct(array $data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, 'template_import_siswa.xlsx');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (is_array($ids) && count($ids) > 0) {
            try {
                DB::transaction(function () use ($ids) {
                    DB::table('peminjamans')->whereIn('siswa_id', $ids)->delete();
                    Siswa::whereIn('id', $ids)->delete();
                });
                return back()->with('success', count($ids) . ' data siswa berhasil dihapus.');
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal menghapus data masal.');
            }
        }
        return back()->with('error', 'Pilih data yang ingin dihapus terlebih dahulu.');
    }
}