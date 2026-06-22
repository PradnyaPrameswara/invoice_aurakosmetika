<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'invoice_id',
        'barang_id',
        'nama_item',
        'deskripsi_item',
        'kuantitas',
        'harga_satuan_kustom',
        'total_per_item',
    ];

    /**
     * Dapatkan invoice yang memiliki item ini.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Dapatkan barang master yang terkait dengan item ini (jika ada).
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }
}
