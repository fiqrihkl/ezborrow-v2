<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chromebook extends Model
{
    // Pastikan status ada di dalam daftar ini
    protected $fillable = [
        'qr_code_unit', 
        'no_unit', 
        'merek', 
        'status', 
        'loker'
    ];

    // Relasi ke Peminjaman (Opsional tapi disarankan)
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}