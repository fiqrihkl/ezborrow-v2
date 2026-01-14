<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('siswa_id')->constrained('siswas');
    $table->foreignId('chromebook_id')->constrained('chromebooks');
    $table->foreignId('guru_id')->constrained('gurus');
    $table->foreignId('mapel_id')->constrained('mapels');
    $table->string('kode_voucher_diberikan')->nullable(); // Voucher yang didapat siswa saat itu
    $table->dateTime('waktu_pinjam');
    $table->dateTime('waktu_kembali')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
