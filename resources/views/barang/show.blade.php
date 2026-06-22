@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-lg rounded-lg p-6 max-w-md mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Detail Barang/Jasa</h1>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">ID Barang:</label>
            <p class="text-gray-900 text-lg">{{ $barang->barang_id }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nama:</label>
            <p class="text-gray-900 text-lg">{{ $barang->nama }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Kode/SKU:</label>
            <p class="text-gray-900 text-lg">{{ $barang->kode_sku ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
            <p class="text-gray-900 text-lg">{{ $barang->deskripsi ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Harga Default:</label>
            <p class="text-gray-900 text-lg">Rp{{ number_format($barang->harga_default, 2, ',', '.') }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Dibuat Pada:</label>
            <p class="text-gray-900 text-lg">{{ $barang->created_at->format('d M Y H:i') }}</p>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Diperbarui Pada:</label>
            <p class="text-gray-900 text-lg">{{ $barang->updated_at->format('d M Y H:i') }}</p>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('barang.edit', $barang->barang_id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                Edit Barang
            </a>
            <a href="{{ route('barang.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
