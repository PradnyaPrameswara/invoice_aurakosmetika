<div class="invoice-item-row bg-gray-50 p-4 rounded-lg shadow-sm mb-4 border border-gray-200">
    <div class="flex justify-end mb-2">
        <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 font-bold">
            <i class="fas fa-times-circle"></i> Hapus Item
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="items_{{ $index }}_barang_id" class="block text-gray-700 text-sm font-bold mb-2">Pilih Barang/Jasa (Opsional):</label>
            <select name="items[{{ $index }}][barang_id]" id="items_{{ $index }}_barang_id" class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('items.' . $index . '.barang_id') border-red-500 @enderror">
                <option value="">-- Pilih dari Master Barang --</option>
                @foreach ($barangs as $barang)
                    <option value="{{ $barang->barang_id }}"
                            data-harga-default="{{ $barang->harga_default }}"
                            data-nama-barang="{{ $barang->nama }}"
                            data-deskripsi-barang="{{ $barang->deskripsi }}"
                            {{ (isset($item) && $item->barang_id == $barang->barang_id) ? 'selected' : '' }}>
                        {{ $barang->nama }} (Rp{{ number_format($barang->harga_default, 2, ',', '.') }})
                    </option>
                @endforeach
            </select>
            @error('items.' . $index . '.barang_id')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="items_{{ $index }}_nama_item" class="block text-gray-700 text-sm font-bold mb-2">Nama Item:</label>
            <input type="text" name="items[{{ $index }}][nama_item]" id="items_{{ $index }}_nama_item" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('items.' . $index . '.nama_item') border-red-500 @enderror" value="{{ old('items.' . $index . '.nama_item', $item->nama_item ?? '') }}" required>
            @error('items.' . $index . '.nama_item')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="mb-4">
        <label for="items_{{ $index }}_deskripsi_item" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi Item (Opsional):</label>
        <textarea name="items[{{ $index }}][deskripsi_item]" id="items_{{ $index }}_deskripsi_item" rows="2" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('items.' . $index . '.deskripsi_item') border-red-500 @enderror">{{ old('items.' . $index . '.deskripsi_item', $item->deskripsi_item ?? '') }}</textarea>
        @error('items.' . $index . '.deskripsi_item')
            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
        @enderror
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label for="items_{{ $index }}_kuantitas" class="block text-gray-700 text-sm font-bold mb-2">Kuantitas:</label>
            <input type="number" name="items[{{ $index }}][kuantitas]" id="items_{{ $index }}_kuantitas" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('items.' . $index . '.kuantitas') border-red-500 @enderror" value="{{ old('items.' . $index . '.kuantitas', $item->kuantitas ?? 1) }}" min="1" required>
            @error('items.' . $index . '.kuantitas')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="items_{{ $index }}_harga_satuan_kustom" class="block text-gray-700 text-sm font-bold mb-2">Harga Satuan Kustom:</label>
            <input type="number" name="items[{{ $index }}][harga_satuan_kustom]" id="items_{{ $index }}_harga_satuan_kustom" step="0.01" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('items.' . $index . '.harga_satuan_kustom') border-red-500 @enderror" value="{{ old('items.' . $index . '.harga_satuan_kustom', $item->harga_satuan_kustom ?? '') }}" required>
            @error('items.' . $index . '.harga_satuan_kustom')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2">Total:</label>
            <p class="total-per-item-display text-gray-900 text-lg font-semibold">Rp0.00</p>
        </div>
    </div>
    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->item_id ?? '' }}">
</div>
