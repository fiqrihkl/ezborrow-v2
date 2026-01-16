<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Chromebook;
use App\Models\Peminjaman;
use App\Models\Voucher;
use App\Models\Setting; // Pastikan Model Setting diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan halaman utama pilihan scan
     */
    public function indexPinjam()
    {
        return view('peminjaman.index');
    }

    /**
     * Menampilkan Unified Scanner (Kamera)
     */
    public function indexScanKamera()
    {
        $gurus = Guru::all();
        return view('peminjaman.scan_kamera', compact('gurus'));
    }

    /**
     * Menampilkan Input Manual
     */
    public function indexScanManual()
    {
        $gurus = Guru::all(); 
        return view('peminjaman.scan_manual', compact('gurus'));
    }

    /**
     * API: Mendapatkan data siswa via AJAX
     */
    public function getSiswaByQr($qr_code)
    {
        $siswa = Siswa::with('kelas')->where('unique_id', $qr_code)->first();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Kartu Siswa tidak terdaftar.'], 404);
        }

        if ($siswa->status == 'nonaktif') {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak! Siswa sedang diskors.'], 403);
        }

        $pinjamanAktif = Peminjaman::where('siswa_id', $siswa->id)
                                    ->whereNull('waktu_kembali')
                                    ->with('chromebook')
                                    ->first();

        if ($pinjamanAktif) {
            return response()->json([
                'success' => true,
                'mode' => 'kembali',
                'pinjaman_id' => $pinjamanAktif->id,
                'siswa' => [
                    'nama_siswa' => $siswa->nama_siswa,
                    'no_unit' => $pinjamanAktif->chromebook->no_unit
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'mode' => 'pinjam',
            'siswa' => [
                'id' => $siswa->id,
                'nama_siswa' => $siswa->nama_siswa,
                'nama_kelas' => $siswa->kelas->nama_kelas ?? 'Tanpa Kelas',
                'kelas_id' => $siswa->kelas_id
            ]
        ]);
    }

    /**
     * API: Mendapatkan Mata Pelajaran berdasarkan Guru
     */
    public function getMapel($guru_id)
    {
        $guru = Guru::with('mapels')->find($guru_id);
        if (!$guru) {
            return response()->json([]);
        }
        return response()->json($guru->mapels);
    }

    /**
     * Logika Final Peminjaman
     */
    public function storeFinal(Request $request)
{
    return DB::transaction(function () use ($request) {
        $siswa = Siswa::findOrFail($request->siswa_id);
        $chromebook = Chromebook::where('qr_code_unit', trim($request->qr_chromebook))->first();

        // 1. Validasi Chromebook
        if (!$chromebook) {
            return redirect()->back()->with('error', 'Unit Chromebook tidak ditemukan!');
        }

        if ($chromebook->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Unit ' . $chromebook->no_unit . ' sedang ' . $chromebook->status);
        }

        // 2. LOGIKA BARU: Ambil Voucher berdasarkan relasi Many-to-Many
        // Kita mencari voucher yang terhubung dengan kelas_id siswa tersebut
        $voucher = Voucher::whereHas('kelas', function($q) use ($siswa) {
            $q->where('kelas.id', $siswa->kelas_id);
        })->lockForUpdate()->first();

        if (!$voucher) {
            return redirect()->back()->with('error', 'Stok voucher internet untuk kelas ' . ($siswa->kelas->nama_kelas ?? '') . ' habis! Segera infokan ke Bapak/Ibu Guru');
        }

        $kodeVoucher = $voucher->kode_voucher;

        // 3. Simpan Transaksi Peminjaman
        Peminjaman::create([
            'siswa_id' => $siswa->id,
            'chromebook_id' => $chromebook->id,
            'guru_id' => $request->guru_id,
            'mapel_id' => $request->mapel_id,
            'waktu_pinjam' => now(),
            'kode_voucher_diberikan' => $kodeVoucher,
        ]);

        // 4. Update Status & Hapus Voucher (karena sudah terpakai)
        $chromebook->update(['status' => 'dipinjam']);
        
        // Menghapus voucher akan otomatis menghapus relasinya di tabel pivot (karena cascade)
        $voucher->delete();

        return redirect()->back()->with([
            'voucher_baru' => $kodeVoucher,
            'success' => 'Peminjaman Berhasil!'
        ]);
    });
}

    /**
     * Logika Pengembalian
     */
    public function updateKembali(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $peminjaman = Peminjaman::findOrFail($id);
            $chromebook = Chromebook::find($peminjaman->chromebook_id);

            if (trim($request->qr_chromebook_verifikasi) !== $chromebook->qr_code_unit) {
                return back()->with('error', 'Gagal! Chromebook yang dikembalikan tidak cocok.');
            }

            $peminjaman->update(['waktu_kembali' => now()]);
            $chromebook->update(['status' => 'tersedia']);

            return redirect()->back()->with('success', 'Berhasil dikembalikan.');
        });
    }

    /**
     * Menampilkan Laporan Riwayat dengan Fitur Cetak Keseluruhan
     */
    public function riwayat(Request $request)
    {
        // Ambil data pengaturan sekolah untuk KOP Surat di hasil cetak
        $settings = Setting::pluck('value', 'key')->all();

        $query = Peminjaman::with(['siswa.kelas', 'chromebook', 'guru', 'mapel']);

        // 1. Filter Pencarian (Nama Siswa, No Unit, atau Nama Kelas)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('siswa', function($sq) use ($search) {
                    $sq->where('nama_siswa', 'like', "%$search%")
                      ->orWhereHas('kelas', function($kq) use ($search) {
                          $kq->where('nama_kelas', 'like', "%$search%");
                      });
                })->orWhereHas('chromebook', function($cq) use ($search) {
                    $cq->where('no_unit', 'like', "%$search%");
                });
            });
        }

        // 2. Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_pinjam', '<=', $request->end_date);
        }

        // 3. Logika Cetak atau Tampilan Biasa
        if ($request->has('print_all')) {
            // Ambil SEMUA data tanpa batasan halaman untuk dicetak
            $history = $query->latest()->get(); 
        } else {
            // Gunakan pagination 10 data untuk tampilan layar web
            $history = $query->latest()->paginate(10);
        }

        // Kirim $settings juga agar Kop Surat muncul datanya
        return view('admin.riwayat.index', compact('history', 'settings'));
    }
}