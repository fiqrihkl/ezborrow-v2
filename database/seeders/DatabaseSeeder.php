<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Chromebook;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Guru
        $guru1 = Guru::create(['nama_guru' => 'Fiqri Haikal', 'nip' => '19900101202401']);
        $guru2 = Guru::create(['nama_guru' => 'Budi Santoso', 'nip' => '19850512201003']);

        // 2. Data Mapel
        $mapel1 = Mapel::create(['nama_mapel' => 'Pemrograman Web', 'kode_mapel' => 'WEB01', 'deskripsi' => 'Belajar Laravel 12']);
        $mapel2 = Mapel::create(['nama_mapel' => 'Basis Data', 'kode_mapel' => 'DB01', 'deskripsi' => 'Belajar MySQL']);
        $mapel3 = Mapel::create(['nama_mapel' => 'Jaringan Dasar', 'kode_mapel' => 'NET01', 'deskripsi' => 'Belajar Mikrotik']);

        // 3. Data Hubungan Guru & Mapel (Tabel Pivot guru_mapel)
        // Pak Fiqri mengajar Web dan Basis Data
        DB::table('guru_mapel')->insert([
            ['guru_id' => $guru1->id, 'mapel_id' => $mapel1->id, 'created_at' => now(), 'updated_at' => now()],
            ['guru_id' => $guru1->id, 'mapel_id' => $mapel2->id, 'created_at' => now(), 'updated_at' => now()],
            ['guru_id' => $guru2->id, 'mapel_id' => $mapel3->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Data Kelas (Wali Kelasnya Pak Fiqri)
        $kelasX = Kelas::create(['nama_kelas' => 'X RPL 1', 'guru_id' => $guru1->id]);
        $kelasXI = Kelas::create(['nama_kelas' => 'XI RPL 1', 'guru_id' => $guru1->id]);

        // 5. Data Siswa
        Siswa::create([
            'nama_siswa' => 'Ahmad Rendy',
            'nis' => '10001',
            'unique_id' => 'QR-SIS-001',
            'kelas_id' => $kelasX->id,
            'status' => 'aktif'
        ]);
        Siswa::create([
            'nama_siswa' => 'Siti Aminah',
            'nis' => '10002',
            'unique_id' => 'QR-SIS-002',
            'kelas_id' => $kelasX->id,
            'status' => 'nonaktif' // Contoh siswa yang sedang diskors
        ]);

        // 6. Data Chromebook
        Chromebook::create([
            'no_unit' => 'CB-01',
            'merek' => 'Acer Spin',
            'loker' => 'Loker A-01',
            'qr_code_unit' => 'QR-CB-01',
            'status' => 'tersedia'
        ]);
        Chromebook::create([
            'no_unit' => 'CB-02',
            'merek' => 'Samsung Chromebook',
            'loker' => 'Loker A-02',
            'qr_code_unit' => 'QR-CB-02',
            'status' => 'tersedia'
        ]);

        // 7. Data Voucher Internet
        Voucher::insert([
            ['kode_voucher' => 'WIFI-SCHOOL-789', 'is_used' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_voucher' => 'WIFI-SCHOOL-456', 'is_used' => false, 'created_at' => now(), 'updated_at' => now()],
            ['kode_voucher' => 'WIFI-SCHOOL-123', 'is_used' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}