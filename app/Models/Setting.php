<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan penyimpanan data secara massal
    protected $fillable = ['key', 'value'];
}