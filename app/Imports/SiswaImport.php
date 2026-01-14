<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['nis']) || empty($row['nama_kelas'])) {
            return null;
        }

        // Cari ID kelas berdasarkan nama yang diketik di Excel
        $kelas = Kelas::where('nama_kelas', trim($row['nama_kelas']))->first();

        // Jika nama kelas tidak ditemukan di database, lewati baris ini
        if (!$kelas) {
            return null; 
        }

        return new Siswa([
            'nis'        => $row['nis'],
            'nama_siswa' => $row['nama_siswa'],
            'kelas_id'   => $kelas->id, // Masukkan ID yang ditemukan
            'status'     => 'aktif',
            'unique_id'  => $row['unique_id'] ?? 'SIS-' . strtoupper(Str::random(8)),
        ]);
    }
}