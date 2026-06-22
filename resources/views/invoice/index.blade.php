@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-bold uppercase tracking-[0.2em] text-blue-700">Manajemen Penjualan</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Daftar Invoice</h1>
            <p class="mt-2 max-w-xl text-sm text-slate-500">Pantau tagihan, pembayaran, dan riwayat transaksi pelanggan dalam satu tempat.</p>
        </div>
        <div class="grid grid-cols-2 gap-2 sm:flex">
            <a href="{{ route('invoice.exportExcel', request()->query()) }}" class="btn-secondary">
                <i class="fas fa-file-excel"></i> Export
            </a>
            <a href="{{ route('invoice.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Invoice Baru
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
    <div class="surface-card mb-6 p-4 sm:p-6">
        <div class="mb-5 flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-100 text-blue-800"><i class="fas fa-sliders-h"></i></span>
            <div><h2 class="font-bold text-slate-800">Filter Invoice</h2><p class="text-xs text-slate-500">Persempit data sesuai kebutuhan</p></div>
        </div>
        <form action="{{ route('invoice.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <div>
                <label for="nama_pelanggan" class="block text-gray-700 text-sm font-bold mb-2">Nama Pelanggan:</label>
                <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" placeholder="Cari nama pelanggan" value="{{ request('nama_pelanggan') }}">
            </div>
            <div>
                <label for="tanggal_mulai" class="block text-gray-700 text-sm font-bold mb-2">Tgl Terbit (Mulai):</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
            </div>
            <div>
                <label for="tanggal_akhir" class="block text-gray-700 text-sm font-bold mb-2">Tgl Terbit (Akhir):</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
            </div>

            <div class="col-span-full flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search"></i> Terapkan Filter
                </button>
                <a href="{{ route('invoice.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    {{-- Tombol Filter Cepat --}}
    <div class="mb-4 flex items-center gap-2 overflow-x-auto pb-2">
        <span class="shrink-0 text-sm font-semibold text-slate-500">Filter cepat:</span>
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
    <div class="surface-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-[980px] w-full leading-normal">
            <thead>
                <tr class="border-b border-blue-950 bg-blue-900 text-xs uppercase tracking-wider text-blue-50">
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
                <tr class="border-b border-slate-100 transition hover:bg-blue-50/50">
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
        </table></div>
    </div>

    {{-- Total Pendapatan --}}
    <div class="surface-card mt-6 flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div><h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">
            Total Pendapatan (Lunas)
        </h2><p class="mt-1 text-xs text-slate-400">Berdasarkan filter yang diterapkan</p></div>
        <p class="text-2xl font-bold tracking-tight text-green-600 sm:text-3xl">
            Rp{{ number_format($totalPendapatan, 2, ',', '.') }}
        </p>
    </div>

</div>
@endsection
