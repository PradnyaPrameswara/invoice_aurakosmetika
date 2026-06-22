<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk facade Auth
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use App\Models\Admin; // Model Admin yang akan digunakan untuk otentikasi

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Tangani proses login.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba otentikasi menggunakan guard 'web' (default)
        // Laravel secara default akan mencoba otentikasi terhadap tabel 'users'.
        // Karena kita menggunakan tabel 'admin', kita perlu menyesuaikan guard atau model.
        // Untuk otentikasi manual terhadap model Admin:
        $admin = Admin::where('username', $credentials['username'])->first();

        if ($admin && Hash::check($credentials['password'], $admin->password_hash)) {
            // Login berhasil
            Auth::login($admin); // Login user Admin

            $request->session()->regenerate();

            // Redirect ke halaman dashboard atau halaman yang diinginkan setelah login
            return redirect()->intended(route('invoice.index'))->with('success', 'Anda berhasil login!');
        }

        // Login gagal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Tangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Anda telah logout.');
    }
}
