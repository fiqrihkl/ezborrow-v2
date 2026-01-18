<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SiswaImport implements ToModel, WithHeadingRow
{
    protected $kelas_id;

    /**
     * Konstruktor untuk menerima lemparan kelas_id dari Controller
     */
    public function __construct($kelas_id)
    {
        $this->kelas_id = $kelas_id;
    }

    /**
     * Pemetaan kolom Excel ke Model Siswa
     */
    public function model(array $row)
    {
        /**
         * Penjelasan Array Key (Heading Row):
         * Laravel Excel secara otomatis mengubah heading menjadi "slug" (huruf kecil & spasi jadi underscore)
         * - "NIS" menjadi $row['nis']
         * - "Nama Lengkap" menjadi $row['nama_lengkap']
         * - "Status" menjadi $row['status']
         * - "QRCode" menjadi $row['qrcode']
         */

        // 1. Validasi: Jika NIS atau Nama Lengkap kosong, lewati baris ini
        if (empty($row['nis']) || empty($row['nama_lengkap'])) {
            return null;
        }

        // 2. Normalisasi Status: Jika kosong diisi 'aktif', jika ada diubah ke huruf kecil
        $statusInput = !empty($row['status']) ? strtolower(trim($row['status'])) : 'aktif';

        // 3. Simpan ke Database
        return new Siswa([
            'nis'        => $row['nis'],
            'nama_siswa' => $row['nama_lengkap'],
            'kelas_id'   => $this->kelas_id, // Menggunakan kelas dari pilihan modal
            'status'     => $statusInput,
            'unique_id'  => $row['qrcode'] ?? 'SIS-' . strtoupper(Str::random(8)),
        ]);
    }
}