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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('barang_id')->nullable(); // Bisa NULL jika item kustom
            $table->string('nama_item', 255);
            $table->text('deskripsi_item')->nullable();
            $table->integer('kuantitas');
            $table->decimal('harga_satuan_kustom', 10, 2);
            $table->decimal('total_per_item', 10, 2);
            $table->timestamps(); // Menambahkan created_at dan updated_at

            $table->foreign('invoice_id')->references('invoice_id')->on('invoice')->onDelete('cascade'); // Hapus item jika invoice dihapus
            $table->foreign('barang_id')->references('barang_id')->on('barang')->onDelete('set null'); // Set NULL jika barang master dihapus
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};