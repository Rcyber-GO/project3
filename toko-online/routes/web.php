<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'index'])->name('products.index');
Route::get('/produk', [StoreController::class, 'index'])->name('products.list');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/keranjang', [StoreController::class, 'cart'])->name('cart.index');
    Route::post('/keranjang/{product}/tambah', [StoreController::class, 'add'])->name('cart.add');
    Route::put('/keranjang/{product}', [StoreController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{product}', [StoreController::class, 'remove'])->name('cart.remove');
    Route::post('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::get('/pesanan', [StoreController::class, 'orders'])->name('orders.index');
    Route::get('/pesanan/{order}', [StoreController::class, 'showOrder'])->name('orders.show');
});
