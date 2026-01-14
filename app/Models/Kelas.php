<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    // Tambahkan guru_id tetap ada sesuai struktur database Ibu
    protected $fillable = ['nama_kelas', 'guru_id'];

    /**
     * Relasi ke Guru (Wali Kelas)
     */
    public function wali()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi ke Siswa
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * PERBAIKAN: Tambahkan relasi ke Voucher
     * Ini yang menyebabkan eror "undefined method vouchers()" sebelumnya
     */
    public function vouchers()
    {
        // Menjelaskan bahwa satu Kelas bisa punya banyak Voucher
        return $this->belongsToMany(Voucher::class, 'kelas_voucher', 'kelas_id', 'voucher_id');
    }
}