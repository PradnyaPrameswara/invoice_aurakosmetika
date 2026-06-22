<?php

namespace App\Http\Controllers;

use App\Exports\InvoiceSalesExporter;
use App\Models\Invoice;
use App\Models\Pelanggan;
use App\Models\Barang;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use Barryvdh\DomPDF\Facade\Pdf; // Import facade Dompdf
use Carbon\Carbon; // Import Carbon untuk manipulasi tanggal
use Illuminate\Support\Facades\URL; // Untuk membuat Signed URL
use SimpleSoftwareIO\QrCode\Facades\QrCode; // QR Code facade

class InvoiceController extends Controller
{
    /**
     * Tampilkan daftar invoice.
     * Sesuai dengan UC-04, Alur Kerja Utama (Main Flow) langkah 2.
     * Memuat relasi pelanggan untuk tampilan tabel.
     */
    public function index(Request $request)
    {
        $query = Invoice::with('pelanggan');

        // --- AWAL PERUBAHAN ---
        // Query terpisah untuk kalkulasi pendapatan, hanya dari invoice lunas
        $pendapatanQuery = Invoice::where('status', 'lunas');
        // --- AKHIR PERUBAHAN ---

        // Filter/pencarian sesuai UC-04, langkah 3
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('nama_pelanggan') && $request->nama_pelanggan != '') {
            $query->whereHas('pelanggan', function ($q) use ($request) {
                $q->where('nama_pelanggan', 'like', '%' . $request->nama_pelanggan . '%');
            });
        }
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->where('tanggal_terbit', '>=', $request->tanggal_mulai);
            // Terapkan juga ke query pendapatan
            $pendapatanQuery->where('tanggal_terbit', '>=', $request->tanggal_mulai);
        }
        if ($request->has('tanggal_akhir') && $request->tanggal_akhir != '') {
            $query->where('tanggal_terbit', '<=', $request->tanggal_akhir);
            // Terapkan juga ke query pendapatan
            $pendapatanQuery->where('tanggal_terbit', '<=', $request->tanggal_akhir);
        }

        // --- AWAL PERUBAHAN BARU: Filter Pendapatan per Bulan ---
        if ($request->has('bulan') && $request->bulan != '') {
            $query->whereMonth('tanggal_terbit', $request->bulan);
            $pendapatanQuery->whereMonth('tanggal_terbit', $request->bulan);
        }
        if ($request->has('tahun') && $request->tahun != '') {
            $query->whereYear('tanggal_terbit', $request->tahun);
            $pendapatanQuery->whereYear('tanggal_terbit', $request->tahun);
        }
        // --- AKHIR PERUBAHAN BARU ---

        $invoices = $query->orderBy('tanggal_terbit', 'desc')->get();

        // --- AWAL PERUBAHAN: Hitung Total Pendapatan ---
        // Menghitung total pendapatan dari query yang sudah difilter
        $totalPendapatan = $pendapatanQuery->sum('total_tagihan');
        
        // Mengambil daftar tahun unik dari invoice untuk dropdown filter
        $years = Invoice::select(DB::raw('YEAR(tanggal_terbit) as year'))
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');
        // --- AKHIR PERUBAHAN ---

        return view('invoice.index', compact('invoices', 'totalPendapatan', 'years'));
    }

    /**
     * Export rekapan penjualan (invoice + item) ke Excel.
     */
    public function exportExcel(Request $request, InvoiceSalesExporter $exporter)
    {
        $query = Invoice::with(['pelanggan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('nama_pelanggan')) {
            $query->whereHas('pelanggan', function ($q) use ($request) {
                $q->where('nama_pelanggan', 'like', '%' . $request->nama_pelanggan . '%');
            });
        }
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_terbit', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_terbit', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_terbit', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_terbit', $request->tahun);
        }

        $invoices = $query->orderBy('tanggal_terbit', 'desc')->get();

        $filename = 'rekap-penjualan-' . now()->format('Ymd-His') . '.xlsx';
        return $exporter->download($invoices, $filename);
    }

    /**
     * Tampilkan form untuk membuat invoice baru.
     * Sesuai dengan UC-03, Alur Kerja Utama (Main Flow) langkah 1-2.
     * Mengirimkan data pelanggan dan barang untuk dropdown.
     */
    public function create()
    {
        $pelanggans = Pelanggan::all();
        $barangs = Barang::all(); // Untuk dropdown pemilihan barang
        $nextInvoiceNumber = $this->generateNextInvoiceNumber(); // Fungsi helper untuk nomor invoice otomatis
        return view('invoice.create', compact('pelanggans', 'barangs', 'nextInvoiceNumber'));
    }

    /**
     * Simpan invoice baru ke database.
     * Sesuai dengan UC-03, Alur Kerja Utama (Main Flow) langkah 13.
     * Menggunakan transaksi database untuk memastikan konsistensi data.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,pelanggan_id',
            'tanggal_terbit' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date|after_or_equal:tanggal_terbit',
            'status' => 'nullable|in:draft,lunas',
            'items' => 'required|array|min:1', // Pastikan ada minimal satu item
            'items.*.barang_id' => 'nullable|exists:barang,barang_id',
            'items.*.nama_item' => 'required|string|max:255',
            'items.*.deskripsi_item' => 'nullable|string',
            'items.*.kuantitas' => 'required|integer|min:1',
            'items.*.harga_satuan_kustom' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0|max:100', // Validasi diskon dalam persen (0-100)
        ]);

        DB::beginTransaction();
        try {
            // Hitung subtotal dari item yang dikirim
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += ($item['kuantitas'] * $item['harga_satuan_kustom']);
            }
            $total_sebelum_diskon = $subtotal;

            $diskon_persen = $request->diskon ?? 0; // Ambil nilai diskon dalam persen
            $diskon_amount = ($total_sebelum_diskon * $diskon_persen) / 100; // Hitung jumlah diskon

            $total_tagihan = $total_sebelum_diskon - $diskon_amount;
            if ($total_tagihan < 0) { // Pastikan total tidak negatif
                $total_tagihan = 0;
            }

            $invoice = Invoice::create([
                'no_invoice' => $this->generateNextInvoiceNumber(), // Generate nomor invoice
                'pelanggan_id' => $request->pelanggan_id,
                'tanggal_terbit' => $request->tanggal_terbit,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'subtotal' => $subtotal,
                'diskon' => $diskon_persen, // Simpan nilai diskon dalam persen
                'total_tagihan' => $total_tagihan,
                'status' => $request->input('status', 'draft'),
            ]);

            foreach ($request->items as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->invoice_id,
                    'barang_id' => $itemData['barang_id'] ?? null,
                    'nama_item' => $itemData['nama_item'],
                    'deskripsi_item' => $itemData['deskripsi_item'] ?? null,
                    'kuantitas' => $itemData['kuantitas'],
                    'harga_satuan_kustom' => $itemData['harga_satuan_kustom'],
                    'total_per_item' => $itemData['kuantitas'] * $itemData['harga_satuan_kustom'],
                ]);
            }

            DB::commit();
            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail invoice tertentu.
     * Sesuai dengan UC-04, Alur Kerja Utama (Main Flow) langkah 4.
     * Memuat relasi pelanggan dan item-item invoice beserta barangnya.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('pelanggan', 'items.barang'); // Load relasi pelanggan dan item beserta barangnya
        return view('invoice.show', compact('invoice'));
    }

    /**
     * Tampilkan form untuk mengedit invoice.
     * Sesuai dengan UC-04, Alur Kerja Utama (Main Flow) langkah 5c (jika status draft).
     * Hanya bisa diedit jika statusnya masih 'draft'.
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('invoice.show', $invoice->invoice_id)->with('error', 'Invoice hanya bisa diedit jika statusnya masih belum lunas.');
        }

        $pelanggans = Pelanggan::all();
        $barangs = Barang::all();
        $invoice->load('items'); // Load item-item yang sudah ada di invoice

        return view('invoice.edit', compact('invoice', 'pelanggans', 'barangs'));
    }

    /**
     * Perbarui invoice di database.
     * Ini akan menjadi kompleks karena melibatkan update item juga.
     * Menggunakan transaksi database untuk memastikan konsistensi data.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('invoice.show', $invoice->invoice_id)->with('error', 'Invoice hanya bisa diperbarui jika statusnya masih belum lunas.');
        }

        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,pelanggan_id',
            'tanggal_terbit' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date|after_or_equal:tanggal_terbit',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:invoice_items,item_id', // Untuk item yang sudah ada
            'items.*.barang_id' => 'nullable|exists:barang,barang_id',
            'items.*.nama_item' => 'required|string|max:255',
            'items.*.deskripsi_item' => 'nullable|string',
            'items.*.kuantitas' => 'required|integer|min:1',
            'items.*.harga_satuan_kustom' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0|max:100', // Validasi diskon dalam persen (0-100)
        ]);

        DB::beginTransaction();
        try {
            // Hitung ulang total
            $subtotal = 0;
            $updatedItemIds = [];

            foreach ($request->items as $itemData) {
                $itemTotal = $itemData['kuantitas'] * $itemData['harga_satuan_kustom'];
                $subtotal += $itemTotal;

                if (isset($itemData['item_id']) && $itemData['item_id']) {
                    // Update item yang sudah ada
                    $item = InvoiceItem::find($itemData['item_id']);
                    if ($item && $item->invoice_id === $invoice->invoice_id) {
                        $item->update([
                            'barang_id' => $itemData['barang_id'] ?? null,
                            'nama_item' => $itemData['nama_item'],
                            'deskripsi_item' => $itemData['deskripsi_item'] ?? null,
                            'kuantitas' => $itemData['kuantitas'],
                            'harga_satuan_kustom' => $itemData['harga_satuan_kustom'],
                            'total_per_item' => $itemTotal,
                        ]);
                        $updatedItemIds[] = $item->item_id;
                    }
                } else {
                    // Tambah item baru
                    $newItem = InvoiceItem::create([
                        'invoice_id' => $invoice->invoice_id,
                        'barang_id' => $itemData['barang_id'] ?? null,
                        'nama_item' => $itemData['nama_item'],
                        'deskripsi_item' => $itemData['deskripsi_item'] ?? null,
                        'kuantitas' => $itemData['kuantitas'],
                        'harga_satuan_kustom' => $itemData['harga_satuan_kustom'],
                        'total_per_item' => $itemTotal,
                    ]);
                    $updatedItemIds[] = $newItem->item_id;
                }
            }

            // Hapus item yang tidak lagi ada di request
            $invoice->items()->whereNotIn('item_id', $updatedItemIds)->delete();

            $total_sebelum_diskon = $subtotal;

            $diskon_persen = $request->diskon ?? 0; // Ambil nilai diskon dalam persen
            $diskon_amount = ($total_sebelum_diskon * $diskon_persen) / 100; // Hitung jumlah diskon

            $total_tagihan = $total_sebelum_diskon - $diskon_amount;
            if ($total_tagihan < 0) { // Pastikan total tidak negatif
                $total_tagihan = 0;
            }

            $invoice->update([
                'pelanggan_id' => $request->pelanggan_id,
                'tanggal_terbit' => $request->tanggal_terbit,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'subtotal' => $subtotal,
                'diskon' => $diskon_persen, // Simpan nilai diskon dalam persen
                'total_tagihan' => $total_tagihan,
                // Status tidak diubah di sini, hanya melalui updateStatus
            ]);

            DB::commit();
            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui invoice: ' . $e->getMessage());
        }
    }

    /**
     * Mengubah status invoice.
     * Sesuai dengan UC-04, Alur Kerja Utama (Main Flow) langkah 5a.
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:draft,terkirim,lunas,batal',
        ]);

        $invoice->status = $request->status;
        $invoice->save();

        return redirect()->route('invoice.show', $invoice->invoice_id)->with('success', 'Status invoice berhasil diubah menjadi ' . $invoice->status_label . '.');
    }

    /**
     * Mengunduh PDF invoice.
     * Sesuai dengan UC-04, Alur Kerja Utama (Main Flow) langkah 5b.
     * Menggunakan library Dompdf untuk menghasilkan file PDF.
     */
    public function downloadPdf(Invoice $invoice)
    {
        // Muat relasi yang diperlukan untuk tampilan PDF
        $invoice->load('pelanggan', 'items.barang');

        // Buat Signed URL untuk verifikasi keaslian invoice
        $verificationUrl = URL::signedRoute('invoice.verify', ['invoice' => $invoice->invoice_id]);

        // Generate QR Code SVG (tanpa Imagick). Tambahkan properti agar tajam saat dirender ke PDF.
        $qrSvgRaw = QrCode::format('svg')
            ->size(320) // lebih besar agar tetap tajam di PDF
            ->margin(0)
            ->errorCorrection('H')
            ->generate($verificationUrl);
        // Sisipkan style untuk menajamkan rendering di beberapa engine PDF
        $qrSvg = preg_replace('/<svg(.*?)>/', '<svg$1 shape-rendering="crispEdges">', $qrSvgRaw, 1);
        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        // Memuat view khusus untuk PDF (Anda perlu membuat resources/views/invoice/pdf_template.blade.php)
        $pdf = Pdf::loadView('invoice.pdf_template', [
            'invoice' => $invoice,
            'qrDataUri' => $qrDataUri,
            'verificationUrl' => $verificationUrl,
        ]);

        // Mengunduh PDF dengan nama file yang sesuai
        return $pdf->download('invoice-' . $invoice->no_invoice . '.pdf');
    }

    /**
     * Verifikasi keaslian invoice melalui Signed URL yang dipindai dari QR Code.
     */
    public function verify(Request $request, Invoice $invoice)
    {
        // Pastikan URL memiliki signature yang valid (mencegah manipulasi URL)
        if (! $request->hasValidSignature()) {
            abort(403, 'Tautan verifikasi tidak valid atau telah kedaluwarsa.');
        }

        $invoice->load('pelanggan');

        // Tampilkan halaman verifikasi sederhana
        return view('invoice.verify', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Hapus invoice dari database.
     * Ini akan menghapus item terkait juga karena onDelete('cascade') di migration.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dihapus!');
    }

    /**
     * Helper: Generate nomor invoice berikutnya.
     * Membuat nomor invoice unik berdasarkan tanggal dan urutan.
     */
    private function generateNextInvoiceNumber()
    {
        // Contoh sederhana: INV-YYYYMMDD-XXX
        $today = now()->format('Ymd');
        $lastInvoice = Invoice::where('no_invoice', 'like', "INV-{$today}-%")
                                 ->orderBy('no_invoice', 'desc')
                                 ->first();

        $nextNumber = 1;
        if ($lastInvoice) {
            // Ambil 3 digit terakhir dari nomor invoice terakhir
            $lastNum = (int) substr($lastInvoice->no_invoice, -3); 
            $nextNumber = $lastNum + 1;
        }

        // Format nomor berikutnya dengan padding nol di depan
        return 'INV-' . $today . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
