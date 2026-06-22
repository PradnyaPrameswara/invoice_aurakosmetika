@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-lg rounded-lg p-6 max-w-md mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Detail Admin</h1>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">ID Admin:</label>
            <p class="text-gray-900 text-lg">{{ $admin->admin_id }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
            <p class="text-gray-900 text-lg">{{ $admin->username }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
            <p class="text-gray-900 text-lg">{{ $admin->email ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
            <p class="text-gray-900 text-lg">{{ $admin->nama_lengkap ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Dibuat Pada:</label>
            <p class="text-gray-900 text-lg">{{ $admin->created_at->format('d M Y H:i') }}</p>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Diperbarui Pada:</label>
            <p class="text-gray-900 text-lg">{{ $admin->updated_at->format('d M Y H:i') }}</p>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.edit', $admin->admin_id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                Edit Admin
            </a>
            <a href="{{ route('admin.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
