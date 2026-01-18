<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Siswa extends Model
{
    // Mass assignment protection
    protected $fillable = [
        'nis', 
        'nama_siswa', 
        'kelas_id', 
        'status', 
        'unique_id'
    ];

    /**
     * Boot function dari Laravel Model.
     * Berfungsi untuk menjalankan logika otomatis saat data dibuat.
     */
    protected static function boot()
    {
        parent::boot();

        // Otomatis membuat unique_id saat siswa baru ditambahkan
        static::creating(function ($siswa) {
            if (empty($siswa->unique_id)) {
                // Menghasilkan string unik random untuk QR Code, misal: SIS-ABC12345
                $siswa->unique_id = 'SIS-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Relasi ke Tabel Kelas.
     * Menghubungkan setiap siswa ke satu kelas tertentu.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Tabel Peminjaman.
     * Digunakan untuk melihat histori peminjaman chromebook oleh siswa ini.
     */
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'siswa_id');
    }
}