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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->string('no_invoice', 50)->unique();
            $table->unsignedBigInteger('pelanggan_id'); // unsignedBigInteger untuk foreign key
            $table->date('tanggal_terbit');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('pajak', 10, 2)->default(0.00);
            $table->decimal('total_tagihan', 10, 2)->default(0.00);
            $table->enum('status', ['draft', 'terkirim', 'lunas', 'batal'])->default('draft');
            $table->timestamps(); // Menambahkan created_at dan updated_at

            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('restrict');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
