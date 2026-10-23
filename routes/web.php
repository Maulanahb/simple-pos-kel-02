<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Redirect root
Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'manager') {
        return redirect()->route('transactions.index');
    }

    return redirect()->route('pos.create');
});

// Autentikasi untuk tamu
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Area terproteksi autentikasi
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Transaksi (Dapat diakses oleh Admin, Kasir, dan Manager)
    Route::middleware('role:admin,kasir,manager')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    });

    // POS Kasir (Khusus Admin dan Kasir)
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('/pos', [TransactionController::class, 'create'])->name('pos.create');
        Route::post('/pos', [TransactionController::class, 'store'])->name('transactions.store');
    });

    // Manajemen Produk & Kategori (Khusus Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except(['show', 'destroy']);
    });
});
