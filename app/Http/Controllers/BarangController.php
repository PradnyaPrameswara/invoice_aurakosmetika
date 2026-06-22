<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Tampilkan daftar barang.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 2.
     */
    public function index()
    {
        $barangs = Barang::all();
        return view('barang.index', compact('barangs'));
    }

    /**
     * Tampilkan form untuk membuat barang baru.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 3a-b.
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Simpan barang baru ke database.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 3c-e.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode_sku' => 'nullable|string|max:50|unique:barang',
            'deskripsi' => 'nullable|string',
            'harga_default' => 'required|numeric|min:0',
        ]);

        Barang::create($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail barang tertentu.
     */
    public function show(Barang $barang)
    {
        return view('barang.show', compact('barang'));
    }

    /**
     * Tampilkan form untuk mengedit barang.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 4a-b.
     */
    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    /**
     * Perbarui barang di database.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 4c-e.
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode_sku' => 'nullable|string|max:50|unique:barang,kode_sku,' . $barang->barang_id . ',barang_id',
            'deskripsi' => 'nullable|string',
            'harga_default' => 'required|numeric|min:0',
        ]);

        $barang->update($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui!');
    }

    /**
     * Hapus barang dari database.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 5a-d.
     */
    public function destroy(Barang $barang)
    {
        // Anda mungkin ingin menambahkan logika untuk memeriksa apakah barang sedang digunakan di invoice
        // sebelum menghapusnya, sesuai dengan praktik terbaik.
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }
}