<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Pastikan nama kolom SAMA PERSIS dengan di phpMyAdmin
        // Kita ambil nis, nama_siswa, unique_id, kelas_id, dan status
        return Siswa::select('nis', 'nama_siswa', 'unique_id', 'kelas_id', 'status')->get();
    }

    public function headings(): array
    {
        // Sesuaikan judul kolom di Excel nanti
        return ["NIS", "Nama Siswa", "QR Code ID", "ID Kelas", "Status"];
    }
}