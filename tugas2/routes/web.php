<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CartController::class, 'index'])->name('products.index');
Route::get('/index', [CartController::class, 'index']);
Route::get('/keranjang', [CartController::class, 'cart'])->name('cart.index');
Route::get('/tugas2/index', [CartController::class, 'index']);
Route::get('/tugas2/keranjang', [CartController::class, 'cart']);

Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::post('/tugas2/cart/add/{product}', [CartController::class, 'add']);
Route::post('/tugas2/cart/update/{product}', [CartController::class, 'update']);
Route::post('/tugas2/cart/remove/{product}', [CartController::class, 'remove']);
Route::post('/tugas2/cart/clear', [CartController::class, 'clear']);
