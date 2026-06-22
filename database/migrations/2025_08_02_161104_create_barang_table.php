<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('barang_id');
            $table->string('nama', 255);
            $table->string('kode_sku', 50)->unique()->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_default', 10, 2);
            $table->timestamps(); // Menambahkan created_at dan updated_at
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
