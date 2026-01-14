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
    Schema::table('vouchers', function (Blueprint $table) {
        // Hapus unique lama pada kode_voucher saja
        $table->dropUnique(['kode_voucher']); 
        
        // Buat unique baru gabungan kode dan kelas
        $table->unique(['kode_voucher', 'kelas_id']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
