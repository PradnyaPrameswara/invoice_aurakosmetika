<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->no_invoice }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
            margin: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
        }
        .header-container {
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        .company-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            display: inline-block;
        }
        .company-details {
            text-align: right;
            font-size: 9pt;
            color: #555;
        }
        .details-container {
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            margin-bottom: 30px;
        }
        .details-container td {
            padding: 0; /* Reset padding */
            vertical-align: top;
        }
        .billed-to {
            padding: 10px;
        }
        .billed-to h4 {
            margin: 0 0 5px 0;
            font-size: 9pt;
            color: #555;
            text-transform: uppercase;
        }
        .billed-to p {
            margin: 0;
            font-size: 10pt;
        }
        .meta-table td {
            text-align: center;
            padding: 10px;
            border-left: 1px solid #ccc;
        }
        .meta-table h4 {
            margin: 0 0 5px 0;
            font-size: 9pt;
            color: #555;
            text-transform: uppercase;
        }
        .meta-table p {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
        }
        .highlight-box {
            background-color: #633c23;
            color: #ffffff;
        }
        .highlight-box h4, .highlight-box p {
            color: #ffffff;
        }
        .items-table th {
            background-color: #ffffff;
            color: #555;
            text-align: left;
            padding: 10px 8px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            border-bottom: 1px solid #333;
        }
        .items-table td {
            border-bottom: 1px solid #e5e7eb; /* Gray-200 */
            padding: 10px 8px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals-section {
            width: 45%;
            margin-left: auto;
            margin-top: 20px;
        }
        .totals-section td {
            padding: 6px 8px;
        }
        .totals-section .label {
            text-align: right;
            color: #555;
        }
        .totals-section .value {
            text-align: right;
            font-weight: bold;
        }
        .totals-section .grand-total .label,
        .totals-section .grand-total .value {
            font-size: 14pt;
            color: #452917;
            border-top: 2px solid #452917;
            padding-top: 10px;
        }
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #edd0ae;
        }
        .footer-section h4 {
            margin-top: 0;
            margin-bottom: 5px;
            color: #555;
        }
        .signature-section {
            text-align: center;
            margin-top: 60px;
        }
        .signature-line {
            border-bottom: 1px solid #555;
            width: 200px;
            margin: 50px auto 5px auto;
        }
        /* --- PERUBAHAN CSS DIMULAI DI SINI --- */
        .qr-img {
            width: 90px; /* Ukuran diperkecil */
            height: 90px; /* Ukuran diperkecil */
            object-fit: contain;
            image-rendering: pixelated;
            display: block;
            margin-bottom: 4px;
        }
        .footer-section td {
            width: 50%;
            vertical-align: top;
        }
        /* --- PERUBAHAN CSS SELESAI DI SINI --- */
    </style>
</head>
<body>

    @php
        $brandLogoPath = public_path('img/logo_invoice.png');
        if (!file_exists($brandLogoPath)) {
            $brandLogoPath = public_path('img/logo-aura-global.png');
        }
    @endphp

    <table class="header-container">
        <tr>
            <td>
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 80px; vertical-align: middle;">
                            @if (file_exists($brandLogoPath))
                                <img src="{{ $brandLogoPath }}" alt="Aura Global Kosmetika" class="company-logo">
                            @endif
                        </td>
                        <td style="vertical-align: middle;"></td>
                    </tr>
                </table>
            </td>
            <td class="company-details">
                <strong>PT. Solusi Digital Nusantara</strong><br>
                Jl. Jend. Sudirman No. 123, Kav. 45-46<br>
                Jakarta Pusat, DKI Jakarta 10220<br>
                Telp: (021) 1234-5678
            </td>
        </tr>
    </table>

    <table class="details-container">
        <tr>
            <td width="45%" class="billed-to">
                <h4>TAGIHAN KEPADA</h4>
                <p>
                    <strong>{{ $invoice->pelanggan->nama_pelanggan }}</strong><br>
                    {{ $invoice->pelanggan->alamat_lengkap ?? 'Alamat tidak tersedia' }}
                </p>
            </td>
            <td>
                <table class="meta-table">
                    <tr>
                        <td>
                            <h4>TANGGAL</h4>
                            <p>{{ $invoice->tanggal_terbit->format('d/m/Y') }}</p>
                        </td>
                        <td class="highlight-box">
                            <h4>MOHON BAYAR</h4>
                            <p>Rp {{ number_format($invoice->total_tagihan, 2, ',', '.') }}</p>
                        </td>
                        <td>
                            <h4>TGL. JATUH TEMPO</h4>
                            <p>{{ $invoice->tanggal_jatuh_tempo ? $invoice->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Produk / Jasa</th>
                <th class="text-right">Kuantitas</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoice->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->nama_item }}</strong>
                    @if($item->deskripsi_item)
                        <br><small>{{ $item->deskripsi_item }}</small>
                    @endif
                </td>
                <td class="text-right">{{ $item->kuantitas }}</td>
                <td class="text-right">Rp {{ number_format($item->harga_satuan_kustom, 2, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->kuantitas * $item->harga_satuan_kustom, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Tidak ada item dalam invoice ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-section">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">Rp {{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
        </tr>
        @if($invoice->diskon > 0)
        <tr>
            <td class="label">Diskon ({{ number_format($invoice->diskon, 2, ',', '.') }}%)</td>
            <td class="value">- Rp {{ number_format(($invoice->subtotal * $invoice->diskon) / 100, 2, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td class="label">Total Tagihan</td>
            <td class="value">Rp {{ number_format($invoice->total_tagihan, 2, ',', '.') }}</td>
        </tr>
    </table>

    <table class="footer-section">
        <tr>
            <td>
                <h4>Pesan:</h4>
                <p style="font-size: 9pt;">
                    Silahkan lakukan transfer ke rekening:<br>
                    <strong>BCA 123-456-7890 a/n PT. Solusi Digital Nusantara</strong>
                </p>
                
                @isset($qrDataUri)
                <div style="margin-top:20px;">
                    <img class="qr-img" src="{{ $qrDataUri }}" alt="QR Verifikasi Invoice">
                    <div style="color:#6b7280;font-size:8pt;">Scan untuk verifikasi</div>
                </div>
                @endisset
            </td>

            <td style="text-align: center;">
                <h4>Dengan Hormat,</h4>
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <strong>Finance Department</strong>
                </div>
            </td>
        </tr>
    </table>
    <div style="text-align: center; margin-top: 30px; font-size: 9pt; color: #777;">
        Terima kasih telah memilih layanan kami.
    </div>

</body>
</html>