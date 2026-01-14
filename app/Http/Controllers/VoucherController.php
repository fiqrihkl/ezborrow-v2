<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Kelas;
use App\Imports\VoucherImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil kelas dengan jumlah voucher yang terhubung (Many-to-Many)
        $kelas = Kelas::withCount('vouchers')->get();
        
        $query = Voucher::with('kelas'); 

        // Filter pencarian berdasarkan kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('kelas.id', $request->kelas_id);
            });
        }

        $vouchers = $query->latest()->paginate(20);
        $totalVoucher = Voucher::count();

        return view('admin.voucher.index', compact('vouchers', 'totalVoucher', 'kelas'));
    }

    /**
     * Menyimpan voucher (Bulk Text & Excel Import)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_ids' => 'required|array',
            'bulk_codes' => 'required_without:file_voucher',
            'file_voucher' => 'nullable|mimes:xlsx,xls,csv|max:2048'
        ], [
            'kelas_ids.required' => 'Pilih minimal satu kelas tujuan!',
            'bulk_codes.required_without' => 'Masukkan kode teks atau unggah file Excel/CSV.'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $insertedCount = 0;
                $duplicateCount = 0;

                // --- LOGIKA 1: IMPORT VIA EXCEL/CSV ---
                if ($request->hasFile('file_voucher')) {
                    $import = new VoucherImport($request->kelas_ids);
                    Excel::import($import, $request->file('file_voucher'));
                    
                    $insertedCount = $import->successCount;
                    $duplicateCount = $import->duplicateCount;
                } 
                // --- LOGIKA 2: INPUT TEKS MASSAL ---
                else if ($request->filled('bulk_codes')) {
                    $codes = preg_split("/[\s,]+/", $request->bulk_codes);
                    $codes = array_filter(array_unique($codes));

                    foreach ($codes as $code) {
                        $cleanCode = trim($code);
                        if (empty($cleanCode)) continue;
                        
                        $exists = Voucher::where('kode_voucher', $cleanCode)->exists();

                        if (!$exists) {
                            $newVoucher = Voucher::create(['kode_voucher' => $cleanCode]);
                            // Hubungkan ke banyak kelas via pivot table
                            $newVoucher->kelas()->sync($request->kelas_ids);
                            $insertedCount++;
                        } else {
                            $duplicateCount++;
                        }
                    }
                }

                // --- LOGIKA PESAN TOAST INFORMATIF ---
                if ($insertedCount > 0) {
                    $pesan = "Berhasil menambahkan <b>$insertedCount</b> kode voucher baru.";
                    if ($duplicateCount > 0) {
                        $pesan .= "<br><small>Informasi: <b>$duplicateCount</b> kode lainnya dilewati karena sudah ada di database (duplikat).</small>";
                    }
                    return redirect()->back()->with('success', $pesan);
                } elseif ($insertedCount == 0 && $duplicateCount > 0) {
                    return redirect()->back()->with('error', "Gagal menyimpan! Seluruh kode (<b>$duplicateCount</b>) yang Anda masukkan sudah terdaftar di sistem.");
                }

                return redirect()->back()->with('info', 'Tidak ada data yang diproses.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus satu voucher
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Voucher berhasil dihapus.');
    }

    /**
     * Mengosongkan seluruh stok voucher
     */
    public function clearAll()
    {
        try {
            DB::transaction(function () {
                // Hapus relasi di pivot table dulu
                DB::table('kelas_voucher')->delete();
                // Baru hapus master voucher
                Voucher::query()->delete();
            });

            return back()->with('success', 'Seluruh stok voucher telah dikosongkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengosongkan stok: ' . $e->getMessage());
        }
    }
}