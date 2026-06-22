<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'barang_id';

    protected $fillable = [
        'nama',
        'kode_sku',
        'deskripsi',
        'harga_default',
    ];

    /**
     * Dapatkan item invoice yang terkait dengan barang ini.
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'barang_id', 'barang_id');
    }
}
