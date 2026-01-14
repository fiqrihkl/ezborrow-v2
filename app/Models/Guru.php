<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     * Wajib didaftarkan agar proses Store/Update di Controller tidak error.
     */
    protected $fillable = [
        'nama_guru',
        'nip',
    ];

    /**
     * Relasi ke Model Mapel (Many-to-Many).
     * Menghubungkan Guru dengan Mata Pelajaran melalui tabel pivot guru_mapel.
     */
    public function mapels()
    {
        return $this->belongsToMany(Mapel::class, 'guru_mapel', 'guru_id', 'mapel_id')
                    ->withTimestamps(); // Rekomendasi: agar created_at di tabel pivot terisi otomatis
    }

    public function kelas()
    {
        // Relasi hasOne karena 1 guru hanya boleh jadi wali di 1 kelas
        return $this->hasOne(Kelas::class, 'guru_id');
    }

    /**
     * Relasi ke Model Peminjaman (One-to-Many).
     * Digunakan untuk melihat riwayat peminjaman yang diawasi oleh guru ini.
     */
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}