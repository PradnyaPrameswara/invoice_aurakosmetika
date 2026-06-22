<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Untuk hashing password

// Controller untuk Admin (UC-01)
class AdminController extends Controller
{
    /**
     * Tampilkan daftar admin.
     */
    public function index()
    {
        $admins = Admin::all();
        return view('admin.index', compact('admins'));
    }

    /**
     * Tampilkan form untuk membuat admin baru.
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Simpan admin baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:admin',
            'password' => 'required|string|min:8',
            'email' => 'nullable|email|unique:admin',
            'nama_lengkap' => 'nullable|string|max:100',
        ]);

        Admin::create([
            'username' => $request->username,
            'password_hash' => Hash::make($request->password), // Hash password
            'email' => $request->email,
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        return redirect()->route('admin.index')->with('success', 'Admin berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail admin tertentu.
     */
    public function show(Admin $admin)
    {
        return view('admin.show', compact('admin'));
    }

    /**
     * Tampilkan form untuk mengedit admin.
     */
    public function edit(Admin $admin)
    {
        return view('admin.edit', compact('admin'));
    }

    /**
     * Perbarui admin di database.
     */
    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:admin,username,' . $admin->admin_id . ',admin_id',
            'password' => 'nullable|string|min:8',
            'email' => 'nullable|email|unique:admin,email,' . $admin->admin_id . ',admin_id',
            'nama_lengkap' => 'nullable|string|max:100',
        ]);

        $admin->username = $request->username;
        if ($request->filled('password')) {
            $admin->password_hash = Hash::make($request->password);
        }
        $admin->email = $request->email;
        $admin->nama_lengkap = $request->nama_lengkap;
        $admin->save();

        return redirect()->route('admin.index')->with('success', 'Admin berhasil diperbarui!');
    }

    /**
     * Hapus admin dari database.
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admin.index')->with('success', 'Admin berhasil dihapus!');
    }
}