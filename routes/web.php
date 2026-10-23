
<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/dashboard', DashboardController::class)
    ->name('dashboard');

// Route yang boleh diakses admin, kasir, dan manager
Route::middleware(['auth', 'role:admin,kasir,manager'])->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->name('transactions.show');
});

// Route khusus admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('categories', CategoryController::class)
        ->except('show');

    Route::resource('products', ProductController::class)
        ->except(['show', 'destroy']);
});

// Route POS khusus admin dan kasir
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('/pos', [TransactionController::class, 'create'])
        ->name('pos.create');

    Route::post('/pos', [TransactionController::class, 'store'])
        ->name('transactions.store');
});

// Halaman login untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.store');
});

// Logout hanya untuk pengguna yang sudah login
Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');