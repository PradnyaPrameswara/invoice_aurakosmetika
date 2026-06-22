<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AuthController; // Import AuthController

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute untuk otentikasi (login/logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Mengarahkan root URL ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Grup rute yang memerlukan otentikasi
Route::middleware(['auth'])->group(function () {
    // Rute untuk Admin
    Route::resource('admin', AdminController::class);

    // Rute untuk Barang
    Route::resource('barang', BarangController::class);

    // Rute untuk Pelanggan
    Route::resource('pelanggan', PelangganController::class);

    // Rute export Excel rekapan penjualan (harus didefinisikan sebelum resource agar tidak tertangkap invoice/{invoice})
    Route::get('invoice/export-excel', [InvoiceController::class, 'exportExcel'])->name('invoice.exportExcel');

    // Rute untuk Invoice
    Route::resource('invoice', InvoiceController::class);

    // Rute khusus untuk update status invoice
    Route::put('invoice/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoice.updateStatus');

    // Rute khusus untuk download PDF invoice
    Route::get('invoice/{invoice}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('invoice.downloadPdf');
});

// Rute publik untuk verifikasi keaslian invoice via QR (menggunakan Signed URL)
Route::get('verify/invoice/{invoice}', [InvoiceController::class, 'verify'])
    ->name('invoice.verify')
    ->middleware('signed');
