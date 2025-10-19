<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/layanan', [HomeController::class, 'services'])->name('services');

Route::get('/pesan/create', [BookingController::class, 'create'])->name('booking.create');
Route::post('/pesan/store', [BookingController::class, 'store'])->name('booking.store');

Route::get('/status-pesanan', [BookingController::class, 'status'])->name('booking.status');

Route::get('/riwayat', [BookingController::class, 'history'])->name('booking.history');

Route::get('/profil', [UserController::class, 'profile'])->name('profile');
Route::post('/profil', [UserController::class, 'updateProfile'])->name('profile.update');

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::post('/orders/{id}/update', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
});
