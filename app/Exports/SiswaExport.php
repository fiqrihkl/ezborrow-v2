<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        // Mulai dengan query dasar (siswa sekolah aktif/nonaktif)
        $query = Siswa::query()->whereIn('status', ['aktif', 'nonaktif']);

        // Jika ada filter pencarian
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%");
            });
        }

        // Jika ada filter kelas
        if (!empty($this->filters['kelas_id'])) {
            $query->where('kelas_id', $this->filters['kelas_id']);
        }

        return $query->with('kelas');
    }

    // Menentukan judul kolom di Excel
    public function headings(): array
    {
        return [
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Status',
            'Unique ID',
        ];
    }

    // Memetakan data yang masuk ke kolom Excel
    public function map($siswa): array
    {
        return [
            $siswa->nis,
            $siswa->nama_siswa,
            $siswa->kelas ? $siswa->kelas->nama_kelas : '-',
            $siswa->status,
            $siswa->unique_id,
        ];
    }
}