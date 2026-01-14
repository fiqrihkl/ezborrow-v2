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
        Schema::create('siswas', function (Blueprint $table) {
    $table->id();
    $table->string('nama_siswa');
    $table->string('nis')->unique();
    $table->string('unique_id')->unique(); // Untuk QR Code
    $table->foreignId('kelas_id')->constrained('kelas');
    $table->enum('status', ['aktif', 'nonaktif', 'alumni'])->default('aktif'); // Tetap ada sesuai permintaan
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
