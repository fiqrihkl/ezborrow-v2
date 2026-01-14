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
        Schema::create('chromebooks', function (Blueprint $table) {
    $table->id();
    $table->string('no_unit')->unique();
    $table->string('merek');
    $table->string('loker');
    $table->string('qr_code_unit')->unique();
    $table->enum('status', ['tersedia', 'dipinjam', 'rusak'])->default('tersedia');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chromebooks');
    }
};
