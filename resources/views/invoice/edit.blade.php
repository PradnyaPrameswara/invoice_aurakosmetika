@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Invoice: #{{ $invoice->no_invoice }}</h1>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('invoice.update', $invoice->invoice_id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Gunakan metode PUT untuk update --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="no_invoice" class="block text-gray-700 text-sm font-bold mb-2">Nomor Invoice:</label>
                    <input type="text" id="no_invoice" name="no_invoice" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 bg-gray-100 cursor-not-allowed" value="{{ $invoice->no_invoice }}" readonly>
                </div>
                <div>
                    <label for="pelanggan_id" class="block text-gray-700 text-sm font-bold mb-2">Pelanggan:</label>
                    <select name="pelanggan_id" id="pelanggan_id" class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('pelanggan_id') border-red-500 @enderror" required>
                        <option value="">Pilih Pelanggan</option>
                        @foreach ($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->pelanggan_id }}" {{ old('pelanggan_id', $invoice->pelanggan_id) == $pelanggan->pelanggan_id ? 'selected' : '' }}>
                                {{ $pelanggan->nama_pelanggan }} ({{ $pelanggan->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('pelanggan_id')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_terbit" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Terbit:</label>
                    <input type="date" name="tanggal_terbit" id="tanggal_terbit" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tanggal_terbit') border-red-500 @enderror" value="{{ old('tanggal_terbit', $invoice->tanggal_terbit->format('Y-m-d')) }}" required>
                    @error('tanggal_terbit')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_jatuh_tempo" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Jatuh Tempo (Opsional):</label>
                    <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tanggal_jatuh_tempo') border-red-500 @enderror" value="{{ old('tanggal_jatuh_tempo', $invoice->tanggal_jatuh_tempo ? $invoice->tanggal_jatuh_tempo->format('Y-m-d') : '') }}">
                    @error('tanggal_jatuh_tempo')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <h2 class="text-xl font-semibold text-gray-700 mb-4">Item Invoice</h2>
            <div id="invoice-items-container">
                {{-- Item invoice akan ditambahkan di sini oleh JavaScript --}}
                @forelse ($invoice->items as $index => $item)
                    @include('invoice._item_row', ['index' => $index, 'item' => $item, 'barangs' => $barangs])
                @empty
                    @include('invoice._item_row', ['index' => 0, 'item' => null, 'barangs' => $barangs])
                @endforelse
            </div>

            <button type="button" id="add-item-btn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out mt-4">
                Tambah Barang/Jasa
            </button>

            <div class="mt-8 border-t pt-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700 font-bold">Subtotal:</span>
                    <span id="subtotal-display" class="text-gray-900 text-lg font-semibold">Rp{{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
                    <input type="hidden" name="subtotal" id="subtotal-input" value="{{ $invoice->subtotal }}">
                </div>
                
                {{-- Input Diskon dalam persen --}}
                <div class="mb-4">
                    <label for="diskon" class="block text-gray-700 text-sm font-bold mb-2">Diskon (%):</label>
                    <input type="number" name="diskon" id="diskon" step="0.01" min="0" max="100" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('diskon') border-red-500 @enderror" value="{{ old('diskon', $invoice->diskon ?? 0.00) }}">
                    @error('diskon')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-900 text-xl font-bold">Total Tagihan:</span>
                    <span id="total-display" class="text-blue-600 text-2xl font-bold">Rp{{ number_format($invoice->total_tagihan, 2, ',', '.') }}</span>
                    <input type="hidden" name="total_tagihan" id="total-input" value="{{ $invoice->total_tagihan }}">
                </div>
            </div>

            <div class="flex items-center justify-between mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                    Perbarui Invoice
                </button>
                <a href="{{ route('invoice.show', $invoice->invoice_id) }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Template untuk baris item baru --}}
<template id="invoice-item-template">
    @include('invoice._item_row', ['index' => '__INDEX__', 'item' => null, 'barangs' => $barangs])
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const invoiceItemsContainer = document.getElementById('invoice-items-container');
        const addItemBtn = document.getElementById('add-item-btn');
        let itemIndex = invoiceItemsContainer.children.length; // Mulai dari jumlah item yang sudah ada

        const diskonInput = document.getElementById('diskon'); // Ambil input diskon

        function calculateTotals() {
            let subtotal = 0;
            invoiceItemsContainer.querySelectorAll('.invoice-item-row').forEach(row => {
                const quantityInput = row.querySelector('[name$="[kuantitas]"]');
                const priceInput = row.querySelector('[name$="[harga_satuan_kustom]"]');

                const quantity = parseFloat(quantityInput ? quantityInput.value : 0) || 0;
                const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
                const itemTotal = quantity * price;

                const totalPerItemDisplay = row.querySelector('.total-per-item-display');
                if (totalPerItemDisplay) {
                    totalPerItemDisplay.textContent = 'Rp' + itemTotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
                subtotal += itemTotal;
            });

            let totalBeforeDiscount = subtotal;

            const diskonPersen = parseFloat(diskonInput.value) || 0; // Ambil nilai diskon dalam persen
            const diskonAmount = (totalBeforeDiscount * diskonPersen) / 100; // Hitung jumlah diskon

            let totalAfterDiscount = totalBeforeDiscount - diskonAmount;

            // Pastikan total tidak negatif
            if (totalAfterDiscount < 0) {
                totalAfterDiscount = 0;
            }

            document.getElementById('subtotal-display').textContent = 'Rp' + subtotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('total-display').textContent = 'Rp' + totalAfterDiscount.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            document.getElementById('subtotal-input').value = subtotal.toFixed(2);
            document.getElementById('total-input').value = totalAfterDiscount.toFixed(2);
        }

        function setupItemRow(row) {
            const quantityInput = row.querySelector('[name$="[kuantitas]"]');
            const priceInput = row.querySelector('[name$="[harga_satuan_kustom]"]');
            const barangSelect = row.querySelector('[name$="[barang_id]"]');
            const namaItemInput = row.querySelector('[name$="[nama_item]"]');
            const deskripsiItemInput = row.querySelector('[name$="[deskripsi_item]"]');

            if (quantityInput && priceInput) {
                quantityInput.addEventListener('input', calculateTotals);
                priceInput.addEventListener('input', calculateTotals);
            }

            if (barangSelect) {
                barangSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const defaultPrice = selectedOption.dataset.hargaDefault;
                    const namaBarang = selectedOption.dataset.namaBarang;
                    const deskripsiBarang = selectedOption.dataset.deskripsiBarang;

                    if (defaultPrice) {
                        priceInput.value = parseFloat(defaultPrice).toFixed(2);
                    } else {
                        priceInput.value = '';
                    }

                    if (namaBarang) {
                        namaItemInput.value = namaBarang;
                    } else {
                        namaItemInput.value = '';
                    }

                    if (deskripsiBarang) {
                        deskripsiItemInput.value = deskripsiBarang;
                    } else {
                        deskripsiItemInput.value = '';
                    }

                    calculateTotals();
                });
            }

            const removeButton = row.querySelector('.remove-item-btn');
            if (removeButton) {
                removeButton.addEventListener('click', function() {
                    row.remove();
                    calculateTotals();
                });
            }
        }

        // Setup existing items on load
        invoiceItemsContainer.querySelectorAll('.invoice-item-row').forEach(setupItemRow);
        calculateTotals(); // Hitung total awal

        addItemBtn.addEventListener('click', function() {
            const template = document.getElementById('invoice-item-template');
            const clone = document.importNode(template.content, true);
            const newRow = clone.firstElementChild;

            // Ganti placeholder __INDEX__ dengan nilai itemIndex yang sebenarnya
            const htmlString = newRow.outerHTML.replace(/__INDEX__/g, itemIndex);
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlString, 'text/html');
            const finalRow = doc.body.firstChild;

            invoiceItemsContainer.appendChild(finalRow);
            setupItemRow(finalRow);
            itemIndex++;
            calculateTotals();
        });

        // Event listener untuk input diskon
        if (diskonInput) {
            diskonInput.addEventListener('input', calculateTotals);
        }
    });
</script>
@endsection
