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
    // 1. Modifikasi tabel vouchers (Hapus kolom kelas_id lama)
    Schema::table('vouchers', function (Blueprint $table) {
        $table->dropForeign(['kelas_id']); // Jika ada foreign key
        $table->dropColumn('kelas_id');
    });

    // 2. Buat tabel pivot
    Schema::create('kelas_voucher', function (Blueprint $table) {
        $table->id();
        $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
        $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_voucher');
    }
};
