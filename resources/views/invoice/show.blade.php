@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detail Invoice: #{{ $invoice->no_invoice }}</h1>
            <a href="{{ route('invoice.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                Kembali ke Daftar
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Informasi Invoice</h2>
                <p><span class="font-bold">Nomor Invoice:</span> {{ $invoice->no_invoice }}</p>
                <p><span class="font-bold">Tanggal Terbit:</span> {{ $invoice->tanggal_terbit->format('d M Y') }}</p>
                <p><span class="font-bold">Jatuh Tempo:</span> {{ $invoice->tanggal_jatuh_tempo ? $invoice->tanggal_jatuh_tempo->format('d M Y') : '-' }}</p>
                <p><span class="font-bold">Status:</span>
                    <span class="px-2 py-1 font-semibold leading-tight rounded-full
                        @if ($invoice->status == 'draft') text-gray-700 bg-gray-200
                        @elseif ($invoice->status == 'terkirim') text-blue-700 bg-blue-200
                        @elseif ($invoice->status == 'lunas') text-green-700 bg-green-200
                        @elseif ($invoice->status == 'batal') text-red-700 bg-red-200
                        @endif">
                        {{ $invoice->status_label }}
                    </span>
                </p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Informasi Pelanggan</h2>
                <p><span class="font-bold">Nama Pelanggan:</span> {{ $invoice->pelanggan->nama_pelanggan }}</p>
                <p><span class="font-bold">Email:</span> {{ $invoice->pelanggan->email ?? '-' }}</p>
                <p><span class="font-bold">No. Telepon:</span> {{ $invoice->pelanggan->no_telepon ?? '-' }}</p>
                <p><span class="font-bold">Alamat:</span> {{ $invoice->pelanggan->alamat_lengkap ?? '-' }}</p>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-gray-700 mb-4">Item Invoice</h2>
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Nama Item</th>
                        <th class="py-3 px-6 text-left">Deskripsi</th>
                        <th class="py-3 px-6 text-center">Kuantitas</th>
                        <th class="py-3 px-6 text-right">Harga Satuan</th>
                        <th class="py-3 px-6 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($invoice->items as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $item->nama_item }}</td>
                        <td class="py-3 px-6 text-left">{{ $item->deskripsi_item ?? '-' }}</td>
                        <td class="py-3 px-6 text-center">{{ $item->kuantitas }}</td>
                        <td class="py-3 px-6 text-right">Rp{{ number_format($item->harga_satuan_kustom, 2, ',', '.') }}</td>
                        <td class="py-3 px-6 text-right">Rp{{ number_format($item->total_per_item, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-3 px-6 text-center text-gray-500">Tidak ada item dalam invoice ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-700 font-bold">Subtotal:</span>
                <span class="text-gray-900 text-lg font-semibold">Rp{{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-700 font-bold">Diskon:</span>
                <span class="text-gray-900 text-lg font-semibold">Rp{{ number_format($invoice->diskon, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-900 text-xl font-bold">Total Tagihan:</span>
                <span class="text-blue-600 text-2xl font-bold">Rp{{ number_format($invoice->total_tagihan, 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end space-x-2 mt-8">
            @if ($invoice->status == 'draft')
            <a href="{{ route('invoice.edit', $invoice->invoice_id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                Edit Invoice
            </a>
            @endif

            {{-- Form untuk mengubah status --}}
            <form action="{{ route('invoice.updateStatus', $invoice->invoice_id) }}" method="POST" class="inline-block">
                @csrf
                @method('PUT')
                <select name="status" onchange="this.form.submit()" class="shadow border rounded-lg py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="lunas" {{ $invoice->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="batal" {{ $invoice->status == 'batal' ? 'selected' : '' }}>Batal</option>
                </select>
            </form>

            <a href="{{ route('invoice.downloadPdf', $invoice->invoice_id) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                Download PDF
            </a>

            <form action="{{ route('invoice.destroy', $invoice->invoice_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini?');" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                    Hapus Invoice
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
