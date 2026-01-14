<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $row) {
            // Menambahkan kolom kelas_id yang terhubung ke tabel kelas
            $row->foreignId('kelas_id')->nullable()->after('id')->constrained('kelas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $row) {
            $row->dropForeign(['kelas_id']);
            $row->dropColumn('kelas_id');
        });
    }
};