<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App Models: Invoice
 *
 * @property int $invoice_id
 * @property string $no_invoice
 * @property int $pelanggan_id
 * @property \Carbon\CarbonInterface|null $tanggal_terbit
 * @property \Carbon\CarbonInterface|null $tanggal_jatuh_tempo
 * @property float|int $subtotal
 * @property float|int $total_tagihan
 * @property float|int|null $diskon
 * @property string $status
 *
 * @property-read \App\Models\Pelanggan|null $pelanggan
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceItem[] $items
 */
class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoice';
    protected $primaryKey = 'invoice_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'no_invoice',
        'pelanggan_id',
        'tanggal_terbit',
        'tanggal_jatuh_tempo',
        'subtotal',
        'total_tagihan',
        'diskon', // Tambahkan ini
        'status',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'subtotal' => 'float',
        'total_tagihan' => 'float',
        'diskon' => 'float',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Belum Lunas',
            'lunas' => 'Lunas',
            'terkirim' => 'Terkirim',
            'batal' => 'Batal',
            default => (string) $this->status,
        };
    }

    /**
     * Dapatkan pelanggan yang memiliki invoice ini.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    /**
     * Dapatkan item-item invoice yang terkait dengan invoice ini.
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }
}
