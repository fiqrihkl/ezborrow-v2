<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['kode_voucher']; // Pastikan kelas_id sudah dihapus dari fillable

    public function kelas()
    {
        // Menjelaskan bahwa Voucher ini bisa muncul di banyak Kelas
        return $this->belongsToMany(Kelas::class, 'kelas_voucher', 'voucher_id', 'kelas_id');
    }
}