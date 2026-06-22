<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Increase precision to handle large totals (e.g. 1,200,000,000.00)
        DB::statement('ALTER TABLE `invoice` MODIFY `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00');
        DB::statement('ALTER TABLE `invoice` MODIFY `total_tagihan` DECIMAL(15,2) NOT NULL DEFAULT 0.00');

        // Keep column present if still exists in DB (older schema); harmless if removed later.
        // If you already dropped `pajak`, you can remove this statement.
        DB::statement('ALTER TABLE `invoice` MODIFY `pajak` DECIMAL(15,2) NOT NULL DEFAULT 0.00');

        DB::statement('ALTER TABLE `invoice_items` MODIFY `harga_satuan_kustom` DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE `invoice_items` MODIFY `total_per_item` DECIMAL(15,2) NOT NULL');

        DB::statement('ALTER TABLE `barang` MODIFY `harga_default` DECIMAL(15,2) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `invoice` MODIFY `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00');
        DB::statement('ALTER TABLE `invoice` MODIFY `total_tagihan` DECIMAL(10,2) NOT NULL DEFAULT 0.00');
        DB::statement('ALTER TABLE `invoice` MODIFY `pajak` DECIMAL(10,2) NOT NULL DEFAULT 0.00');

        DB::statement('ALTER TABLE `invoice_items` MODIFY `harga_satuan_kustom` DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE `invoice_items` MODIFY `total_per_item` DECIMAL(10,2) NOT NULL');

        DB::statement('ALTER TABLE `barang` MODIFY `harga_default` DECIMAL(10,2) NOT NULL');
    }
};
