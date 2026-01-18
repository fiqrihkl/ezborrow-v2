<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow; // Untuk mulai dari baris tertentu
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SiswaImport implements ToModel, WithStartRow
{
    use Importable;

    protected $kelas_id;

    /**
     * Konstruktor untuk menerima kelas_id
     */
    public function __construct($kelas_id)
    {
        $this->kelas_id = $kelas_id;
    }

    /**
     * Tentukan mulai dari baris mana (Baris 2)
     * Baris 1 (Heading) akan diabaikan total
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Pemetaan berdasarkan INDEKS KOLOM
     * [0] = Kolom A (nis)
     * [1] = Kolom B (nama_siswa)
     * [2] = Kolom C (status)
     * [3] = Kolom D (unique_id)
     */
    public function model(array $row)
    {
        // Jika kolom NIS (A) dan Nama (B) kosong, abaikan baris tersebut
        if (empty($row[0]) || empty($row[1])) {
            return null;
        }

        // Normalisasi Status (Kolom C)
        $statusInput = !empty($row[2]) ? strtolower(trim($row[2])) : 'aktif';

        return new Siswa([
            'nis'        => trim((string)$row[0]), // Kolom A
            'nama_siswa' => $row[1],               // Kolom B
            'kelas_id'   => $this->kelas_id, 
            'status'     => $statusInput,
            'unique_id'  => $row[3] ?? 'SIS-' . strtoupper(Str::random(8)), // Kolom D
        ]);
    }
}