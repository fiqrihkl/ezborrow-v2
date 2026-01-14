<?php

namespace App\Imports;

use App\Models\Voucher;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;

class VoucherImport implements ToCollection, WithStartRow
{
    private $kelasIds;
    
    // Properti baru untuk laporan di Controller
    public $successCount = 0;
    public $duplicateCount = 0;

    /**
     * @param array $kelasIds - Menerima array ID kelas dari Controller
     */
    public function __construct(array $kelasIds)
    {
        $this->kelasIds = $kelasIds;
    }

    /**
     * Menentukan baris awal pembacaan (Baris 2, karena Baris 1 adalah Header)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Menggunakan Collection agar kita bisa melakukan operasi relasi Many-to-Many
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            $kodeVoucher = isset($row[0]) ? trim($row[0]) : null;

            // Abaikan jika baris tersebut kosong
            if (empty($kodeVoucher)) {
                continue;
            }

            // Cek apakah kode voucher sudah ada di database (Global Unique)
            $voucher = Voucher::where('kode_voucher', $kodeVoucher)->first();

            if (!$voucher) {
                // 1. Simpan voucher baru
                $newVoucher = Voucher::create([
                    'kode_voucher' => $kodeVoucher,
                ]);

                // 2. Hubungkan ke banyak kelas (Tabel Pivot)
                $newVoucher->kelas()->sync($this->kelasIds);
                
                // Tambah hitungan berhasil
                $this->successCount++;
            } else {
                // Tambah hitungan duplikat
                $this->duplicateCount++;
            }
        }
    }
}