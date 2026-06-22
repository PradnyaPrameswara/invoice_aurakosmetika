@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Pelanggan</h1>
        <a href="{{ route('pelanggan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
            Tambah Pelanggan Baru
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

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">ID</th>
                    <th class="py-3 px-6 text-left">Nama Pelanggan</th>
                    <th class="py-3 px-6 text-left">Email</th>
                    <th class="py-3 px-6 text-left">No. Telepon</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse ($pelanggans as $pelanggan)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $pelanggan->pelanggan_id }}</td>
                    <td class="py-3 px-6 text-left">{{ $pelanggan->nama_pelanggan }}</td>
                    <td class="py-3 px-6 text-left">{{ $pelanggan->email ?? '-' }}</td>
                    <td class="py-3 px-6 text-left">{{ $pelanggan->no_telepon ?? '-' }}</td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center space-x-2">
                            <a href="{{ route('pelanggan.show', $pelanggan->pelanggan_id) }}" class="w-8 h-8 rounded-full bg-green-200 hover:bg-green-300 flex items-center justify-center text-green-700 transition duration-300 ease-in-out" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('pelanggan.edit', $pelanggan->pelanggan_id) }}" class="w-8 h-8 rounded-full bg-yellow-200 hover:bg-yellow-300 flex items-center justify-center text-yellow-700 transition duration-300 ease-in-out" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('pelanggan.destroy', $pelanggan->pelanggan_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-full bg-red-200 hover:bg-red-300 flex items-center justify-center text-red-700 transition duration-300 ease-in-out" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-3 px-6 text-center text-gray-500">Tidak ada data pelanggan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
