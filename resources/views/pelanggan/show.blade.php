@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-lg rounded-lg p-6 max-w-md mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Detail Pelanggan</h1>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">ID Pelanggan:</label>
            <p class="text-gray-900 text-lg">{{ $pelanggan->pelanggan_id }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Pelanggan:</label>
            <p class="text-gray-900 text-lg">{{ $pelanggan->nama_pelanggan }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
            <p class="text-gray-900 text-lg">{{ $pelanggan->email ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">No. Telepon:</label>
            <p class="text-gray-900 text-lg">{{ $pelanggan->no_telepon ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap:</label>
            <p class="text-gray-900 text-lg">{{ $pelanggan->alamat_lengkap ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Dibuat Pada:</label>
            <p class="text-gray-900 text-lg">{{ $pelanggan->created_at->format('d M Y H:i') }}</p>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Diperbarui Pada:</label>
            <p class="text-gray-900 text-lg">{{ $pelanggan->updated_at->format('d M Y H:i') }}</p>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('pelanggan.edit', $pelanggan->pelanggan_id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                Edit Pelanggan
            </a>
            <a href="{{ route('pelanggan.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
