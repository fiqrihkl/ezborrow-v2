<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class SiswaImport implements ToModel, WithHeadingRow
{
    protected $kelas_id;

    // Konstruktor untuk menerima lemparan kelas_id dari Controller
    public function __construct($kelas_id)
    {
        $this->kelas_id = $kelas_id;
    }

    public function model(array $row)
    {
        // 1. Validasi: Jika NIS atau Nama Siswa kosong, lewati
        if (empty($row['nis']) || empty($row['nama_siswa'])) {
            return null;
        }

        // 2. Simpan ke database menggunakan ID kelas yang dipilih dari Modal
        return new Siswa([
            'nis'        => $row['nis'],
            'nama_siswa' => $row['nama_siswa'],
            'kelas_id'   => $this->kelas_id, // Menggunakan ID dari pilihan admin
            'status'     => 'aktif',
            'unique_id'  => $row['unique_id'] ?? 'SIS-' . strtoupper(Str::random(8)),
        ]);
    }
}