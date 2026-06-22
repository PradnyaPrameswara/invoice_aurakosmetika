<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Invoice {{ $invoice->no_invoice }}</title>
    <style>
        /* --- Pengaturan Dasar --- */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            max-width: 800px;
            margin: 40px auto;
            padding: 0 16px;
            color: #333;
            background-color: #f9fafb;
        }

        /* --- Header --- */
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header h1 {
            font-size: 28px;
            margin: 0 0 8px 0;
            color: #111827;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 600;
        }
        .badge-ok {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
        }

        /* --- Kartu Utama --- */
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            margin-top: 16px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        .card h2 {
            font-size: 18px;
            margin-top: 24px;
            margin-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        .muted {
            color: #6b7280;
            font-size: 14px;
            text-align: center;
            margin: 0 0 16px 0;
        }

        /* --- Detail & Grid --- */
        .invoice-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        .invoice-details dt {
            color: #6b7280;
            font-size: 14px;
        }
        .invoice-details dd {
            font-weight: 600;
            font-size: 16px;
            margin-left: 0;
        }
        
        /* --- Tabel Item --- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .items-table th, .items-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table thead th {
            background-color: #f9fafb;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .item-name {
            font-weight: 600;
        }

        /* --- Bagian Total --- */
        .totals-section {
            margin-top: 20px;
            padding-top: 10px;
            width: 60%;
            margin-left: auto;
        }
        .totals-section table {
            width: 100%;
        }
        .totals-section td {
            padding: 5px;
        }
        .totals-section .label {
            color: #6b7280;
        }
        .totals-section .value {
            text-align: right;
            font-weight: 600;
        }
        .totals-section .grand-total .value,
        .totals-section .grand-total .label {
            font-size: 18px;
            font-weight: bold;
            padding-top: 10px;
            border-top: 2px solid #333;
        }
        .totals-section .grand-total .value {
            color: #3b82f6;
        }


        /* --- Footer & Tombol --- */
        .footer {
            text-align: center;
            margin-top: 24px;
        }
        a.btn {
            display: inline-block;
            padding: 10px 16px;
            background: #3b82f6;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        a.btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Verifikasi Keaslian Invoice</h1>
        <span class="badge badge-ok">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            <span>TERVERIFIKASI</span>
        </span>
    </div>

    <div class="card">
        <p class="muted">Tautan ini dilindungi tanda tangan digital. Invoice ini dijamin asli dan belum dimodifikasi.</p>

        <h2>Detail Invoice</h2>
        <dl class="invoice-details">
            <div>
                <dt>Nomor Invoice</dt>
                <dd>{{ $invoice->no_invoice }}</dd>
            </div>
            <div>
                <dt>Status</dt>
                <dd>{{ strtoupper($invoice->status_label) }}</dd>
            </div>
            <div>
                <dt>Pelanggan</dt>
                <dd>{{ $invoice->pelanggan->nama_pelanggan ?? '-' }}</dd>
            </div>
            <div>
                <dt>Tanggal Terbit</dt>
                <dd>{{ optional($invoice->tanggal_terbit)->format('d M Y') }}</dd>
            </div>
        </dl>

        <h2>Rincian Pembelanjaan</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produk / Jasa</th>
                    <th class="text-right">Kuantitas</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->nama_item }}</div>
                        @if($item->deskripsi_item)
                            <div class="muted" style="font-size: 12px;">{{ $item->deskripsi_item }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->kuantitas }}</td>
                    <td class="text-right">Rp{{ number_format($item->harga_satuan_kustom, 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($item->total_per_item, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;" class="muted">Tidak ada item dalam invoice ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals-section">
            <table>
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">Rp{{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($invoice->diskon > 0)
                <tr>
                    <td class="label">Diskon</td>
                    <td class="value">- Rp{{ number_format($invoice->diskon, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td class="label">TOTAL</td>
                    <td class="value">Rp{{ number_format($invoice->total_tagihan, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <a class="btn" href="{{ route('invoice.show', $invoice->invoice_id) }}">Lihat di Aplikasi</a>
        </div>
    </div>
</body>
</html>