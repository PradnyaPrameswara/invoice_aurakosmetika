<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->changePrecision(15);
    }

    public function down(): void
    {
        $this->changePrecision(10);
    }

    private function changePrecision(int $precision): void
    {
        Schema::table('invoice', function (Blueprint $table) use ($precision) {
            $table->decimal('subtotal', $precision, 2)->default(0)->change();
            $table->decimal('total_tagihan', $precision, 2)->default(0)->change();
            $table->decimal('pajak', $precision, 2)->default(0)->change();
        });

        Schema::table('invoice_items', function (Blueprint $table) use ($precision) {
            $table->decimal('harga_satuan_kustom', $precision, 2)->change();
            $table->decimal('total_per_item', $precision, 2)->change();
        });

        Schema::table('barang', function (Blueprint $table) use ($precision) {
            $table->decimal('harga_default', $precision, 2)->change();
        });
    }
};
