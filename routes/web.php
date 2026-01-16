<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\ChromebookController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| AREA PUBLIK (Akses Tanpa Login)
|--------------------------------------------------------------------------
*/

// 1. Landing Page Utama
Route::get('/', function () {
    return view('index');
})->name('index');

// 2. Alur Peminjaman Unified
Route::get('/pinjam', [PeminjamanController::class, 'indexPinjam'])->name('pinjam.index');
Route::get('/scan-kamera', [PeminjamanController::class, 'indexScanKamera'])->name('scan.kamera');
Route::get('/scan-manual', [PeminjamanController::class, 'indexScanManual'])->name('scan.manual');

// 3. API PENDUKUNG (Fetch/AJAX)
Route::get('/get-siswa-by-qr/{qr_code}', [PeminjamanController::class, 'getSiswaByQr']);
Route::get('/get-mapel-by-guru/{guru_id}', [PeminjamanController::class, 'getMapel'])->name('get.mapel');

// 4. Eksekusi Transaksi & Pengembalian
Route::post('/peminjaman/final', [PeminjamanController::class, 'storeFinal'])->name('peminjaman.final');
Route::get('/peminjaman/konfirmasi-kembali/{siswa_id}', [PeminjamanController::class, 'showKonfirmasiKembali'])->name('peminjaman.konfirmasi.kembali');
Route::put('/peminjaman/kembali/{id}', [PeminjamanController::class, 'updateKembali'])->name('peminjaman.kembali');

// 5. Autentikasi Admin
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| AREA ADMIN (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // CRUD Master Data (Siswa, Chromebook, Guru, Mapel, Kelas)
    Route::resource('siswa', SiswaController::class);
    Route::resource('chromebook', ChromebookController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('mapel', MapelController::class);
    Route::resource('kelas', KelasController::class);

    // Rute Khusus Siswa (Nonaktifkan & Import/Export)
    Route::post('/siswa/{id}/keluar', [SiswaController::class, 'keluar'])->name('siswa.keluar');
    Route::get('/siswa-export', [SiswaController::class, 'export'])->name('siswa.export');
    Route::post('/siswa-import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('/siswa-template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');

    // Fitur Kenaikan Kelas (Promotion)
    Route::controller(PromotionController::class)->group(function () {
        Route::get('/promotion', 'index')->name('promotion.index');
        Route::get('/promotion/get-siswa/{kelas_id}', 'getSiswaByKelas');
        Route::post('/promotion/process', 'process')->name('promotion.process');
    });

    // Manajemen Voucher Internet
    Route::resource('voucher', VoucherController::class);
    Route::post('/voucher-import', [VoucherController::class, 'import'])->name('voucher.import');
    Route::delete('/voucher-clear', [VoucherController::class, 'clearAll'])->name('voucher.clearAll');

    // Laporan & Riwayat Peminjaman (INI YANG DIPANGGIL DI DASHBOARD)
    Route::get('/riwayat', [PeminjamanController::class, 'riwayat'])->name('riwayat.index');

    // Pengaturan Sistem
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});