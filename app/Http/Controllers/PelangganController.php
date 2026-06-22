<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Tampilkan daftar pelanggan.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 2.
     */
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('pelanggan.index', compact('pelanggans'));
    }

    /**
     * Tampilkan form untuk membuat pelanggan baru.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 3a-b.
     */
    public function create()
    {
        return view('pelanggan.create');
    }

    /**
     * Simpan pelanggan baru ke database.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 3c-e.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'email' => 'nullable|email|unique:pelanggan',
            'no_telepon' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
        ]);

        Pelanggan::create($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail pelanggan tertentu.
     */
    public function show(Pelanggan $pelanggan)
    {
        return view('pelanggan.show', compact('pelanggan'));
    }

    /**
     * Tampilkan form untuk mengedit pelanggan.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 4a-b.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Perbarui pelanggan di database.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 4c-e.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'email' => 'nullable|email|unique:pelanggan,email,' . $pelanggan->pelanggan_id . ',pelanggan_id',
            'no_telepon' => 'nullable|string|max:20',
            'alamat_lengkap' => 'nullable|string',
        ]);

        $pelanggan->update($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui!');
    }

    /**
     * Hapus pelanggan dari database.
     * Sesuai dengan Alur Kerja Utama (Main Flow) langkah 5a-d.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        // Periksa apakah pelanggan memiliki invoice terkait sebelum menghapus
        if ($pelanggan->invoices()->count() > 0) {
            return redirect()->route('pelanggan.index')->with('error', 'Gagal! Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus!');
    }
}