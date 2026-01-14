<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    // Menentukan nama tabel secara manual
    protected $table = 'peminjamans'; 

    protected $fillable = [
        'siswa_id', 
        'chromebook_id', 
        'guru_id', 
        'mapel_id', 
        'waktu_pinjam', 
        'waktu_kembali', 
        'kode_voucher_diberikan'
    ];

    /**
     * Casting agar Laravel mengenali kolom ini sebagai Carbon/DateTime.
     * Sangat memudahkan untuk manipulasi waktu, misal: diffForHumans()
     */
    protected $casts = [
        'waktu_pinjam' => 'datetime',
        'waktu_kembali' => 'datetime',
    ];

    // --- ACCESSOR (Logika Tambahan) ---

    /**
     * Mengecek status pinjaman secara instan.
     * Gunakan di blade: $peminjaman->is_kembali
     */
    public function getIsKembaliAttribute(): bool
    {
        return $this->waktu_kembali !== null;
    }

    /**
     * Menghitung durasi peminjaman jika sudah kembali.
     */
    public function getDurasiAttribute()
    {
        if ($this->waktu_kembali) {
            return $this->waktu_pinjam->diffInMinutes($this->waktu_kembali) . ' Menit';
        }
        return 'Masih digunakan';
    }

    // --- RELASI ---

    /**
     * Relasi ke Siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Chromebook.
     */
    public function chromebook(): BelongsTo
    {
        return $this->belongsTo(Chromebook::class, 'chromebook_id');
    }

    /**
     * Relasi ke Guru (Penting untuk laporan filter per kelas/guru).
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Relasi ke Mapel (Penting untuk analisis penggunaan perangkat per mata pelajaran).
     */
    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}