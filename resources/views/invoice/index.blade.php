@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Invoice</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('invoice.exportExcel', request()->query()) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-900 font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                Export Excel
            </a>
            <a href="{{ route('invoice.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                Buat Invoice Baru
            </a>
        </div>
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

    {{-- Filter/Pencarian --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Filter Invoice</h2>
        <form action="{{ route('invoice.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select name="status" id="status" class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <div>
                <label for="nama_pelanggan" class="block text-gray-700 text-sm font-bold mb-2">Nama Pelanggan:</label>
                <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ request('nama_pelanggan') }}">
            </div>
            <div>
                <label for="tanggal_mulai" class="block text-gray-700 text-sm font-bold mb-2">Tgl Terbit (Mulai):</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ request('tanggal_mulai') }}">
            </div>
            <div>
                <label for="tanggal_akhir" class="block text-gray-700 text-sm font-bold mb-2">Tgl Terbit (Akhir):</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ request('tanggal_akhir') }}">
            </div>

            <div class="col-span-full flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                    Filter
                </button>
                <a href="{{ route('invoice.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out">Reset</a>
            </div>
        </form>
    </div>

    {{-- Tombol Filter Cepat --}}
    <div class="flex items-center space-x-2 mb-4">
        <span class="font-semibold text-gray-600">Filter Cepat:</span>
        <a href="{{ route('invoice.index', array_merge(request()->except('status'), ['status' => ''])) }}" class="text-sm {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }} font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out">
            Semua
        </a>
        <a href="{{ route('invoice.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}" class="text-sm {{ request('status') == 'draft' ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }} font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out">
            Belum Lunas
        </a>
        <a href="{{ route('invoice.index', array_merge(request()->except('status'), ['status' => 'lunas'])) }}" class="text-sm {{ request('status') == 'lunas' ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }} font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out">
            Lunas
        </a>
    </div>

    {{-- Tabel Invoice --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">No. Invoice</th>
                    <th class="py-3 px-6 text-left">Pelanggan</th>
                    <th class="py-3 px-6 text-left">Tgl. Terbit</th>
                    <th class="py-3 px-6 text-left">Jatuh Tempo</th>
                    <th class="py-3 px-6 text-right">Total Tagihan</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse ($invoices as $invoice)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $invoice->no_invoice }}</td>
                    <td class="py-3 px-6 text-left">{{ $invoice->pelanggan->nama_pelanggan }}</td>
                    <td class="py-3 px-6 text-left">{{ $invoice->tanggal_terbit->format('d M Y') }}</td>
                    <td class="py-3 px-6 text-left">{{ $invoice->tanggal_jatuh_tempo ? $invoice->tanggal_jatuh_tempo->format('d M Y') : '-' }}</td>
                    <td class="py-3 px-6 text-right">Rp{{ number_format($invoice->total_tagihan, 2, ',', '.') }}</td>
                    <td class="py-3 px-6 text-left">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full
                            @if ($invoice->status == 'draft') text-gray-700 bg-gray-200
                            @elseif ($invoice->status == 'terkirim') text-blue-700 bg-blue-200
                            @elseif ($invoice->status == 'lunas') text-green-700 bg-green-200
                            @elseif ($invoice->status == 'batal') text-red-700 bg-red-200
                            @endif">
                            {{ $invoice->status_label }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex items-center justify-center space-x-1">
                            <a href="{{ route('invoice.show', $invoice->invoice_id) }}" class="p-2 rounded-lg bg-green-500 hover:bg-green-600 text-white shadow-md flex items-center justify-center text-sm font-medium transition duration-300 ease-in-out" title="Lihat Detail">
                                <i class="fas fa-eye mr-1"></i> Lihat
                            </a>
                            @if ($invoice->status == 'draft')
                            <a href="{{ route('invoice.edit', $invoice->invoice_id) }}" class="p-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white shadow-md flex items-center justify-center text-sm font-medium transition duration-300 ease-in-out" title="Edit Invoice">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            @endif
                            <form action="{{ route('invoice.destroy', $invoice->invoice_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-red-600 hover:bg-red-700 text-white shadow-md flex items-center justify-center text-sm font-medium transition duration-300 ease-in-out" title="Hapus Invoice">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-3 px-6 text-center text-gray-500">Tidak ada data invoice.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Total Pendapatan --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-2">
            Total Pendapatan (Lunas)
        </h2>
        <p class="text-3xl font-bold text-green-600">
            Rp{{ number_format($totalPendapatan, 2, ',', '.') }}
        </p>
         <p class="text-sm text-gray-500 mt-1">
            Berdasarkan filter yang diterapkan di atas.
        </p>
    </div>

</div>
@endsection
