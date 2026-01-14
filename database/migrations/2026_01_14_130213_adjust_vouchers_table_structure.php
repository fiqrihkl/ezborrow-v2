<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // 1. Menghapus kolom kelas_id dari tabel vouchers karena sudah tidak satu-ke-satu lagi
    Schema::table('vouchers', function (Blueprint $table) {
        // Cek dulu apakah kolom ada, lalu hapus
        if (Schema::hasColumn('vouchers', 'kelas_id')) {
            $table->dropColumn('kelas_id');
        }
    });

    // 2. Membuat tabel pivot kelas_voucher
    Schema::create('kelas_voucher', function (Blueprint $table) {
        $table->id();
        // Menghubungkan ke tabel vouchers
        $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
        // Menghubungkan ke tabel kelas
        $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('kelas_voucher');
    Schema::table('vouchers', function (Blueprint $table) {
        $table->foreignId('kelas_id')->nullable();
    });
}
};
