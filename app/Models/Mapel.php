<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mapel extends Model
{
    // Mass assignment agar data bisa disimpan sekaligus
    protected $fillable = [
        'nama_mapel', 
        'kode_mapel', 
        'deskripsi'
    ];

    /**
     * Relasi ke Guru (Many-to-Many)
     * Satu Mapel bisa diampu banyak Guru.
     */
    public function gurus(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_mapel', 'mapel_id', 'guru_id');
    }

    /**
     * Relasi ke Peminjaman (One-to-Many)
     * Satu Mapel bisa memiliki banyak riwayat Peminjaman.
     * Ini penting agar kita bisa menghapus riwayat peminjaman 
     * sebelum menghapus Mapel untuk menghindari eror database.
     */
    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'mapel_id');
    }
}