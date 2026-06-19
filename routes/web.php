<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/layanan', [HomeController::class, 'services'])->name('services');

// Booking routes (public)
Route::get('/pesan/create', [BookingController::class, 'create'])->name('booking.create');
Route::post('/pesan/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/status-pesanan', [BookingController::class, 'status'])->name('booking.status');
Route::get('/riwayat', [BookingController::class, 'history'])->name('booking.history');
Route::delete('/riwayat/{order}/delete', [BookingController::class, 'deleteOrder'])->name('booking.delete');
Route::post('/riwayat/{order}/refund', [BookingController::class, 'requestRefund'])->name('booking.refund');
Route::get('/api/price-quote', [BookingController::class, 'priceQuote'])->name('booking.price_quote');
// API: return available dates in a range (used by datepicker)
Route::get('/api/slot-availability', [BookingController::class, 'availableDates'])->name('booking.slot_availability');
// API: return slots for a specific date (accepts optional service_id)
Route::get('/api/slots-for-date', [BookingController::class, 'slotsForDate'])->name('booking.slots_for_date');
// API: return discounts for a service
Route::get('/api/discounts', [BookingController::class, 'getDiscounts'])->name('booking.discounts');
// API: return available schedules
Route::get('/api/available-schedules', [BookingController::class, 'getAvailableSchedules']);

// Public payment proof route
Route::get('/payment-proof/{filename}', [BookingController::class, 'showPaymentProof'])->name('payment-proof');

// Payment routes
Route::get('/payment/confirmation/{order}', [PaymentController::class, 'showConfirmation'])->name('payment.confirmation');
Route::post('/payment/process/{order}', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/bank-transfer/{order}', [PaymentController::class, 'showBankTransfer'])->name('payment.bank-transfer');
Route::post('/payment/upload-proof/{order}', [PaymentController::class, 'uploadPaymentProof'])->name('payment.upload-proof');
Route::get('/payment/ewallet/{order}', [PaymentController::class, 'showEwallet'])->name('payment.ewallet');
Route::get('/payment/status/{order}', [PaymentController::class, 'checkPaymentStatus'])->name('payment.status');

// User profile routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/profil', [UserController::class, 'profile'])->name('profile');
    Route::post('/profil', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}/delete', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [NotificationController::class, 'getRecentNotifications'])->name('notifications.recent');
});

// Admin login routes (public)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Admin routes (protected with admin middleware)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/verifications', [AdminController::class, 'verifications'])->name('admin.verifications');
        Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
        Route::get('/pending-payments', [AdminController::class, 'pendingPayments'])->name('admin.pending-payments');
        Route::post('/orders/{id}/update', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
        Route::delete('/orders/{id}/delete', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');
        Route::post('/payment/verify/{order}', [PaymentController::class, 'verifyPayment'])->name('admin.payment.verify');
        Route::post('/refund/{order}/process', [AdminController::class, 'processRefund'])->name('admin.refund.process');
        
        // Admin notification routes
        Route::get('/api/notifications', [NotificationController::class, 'getAdminNotifications'])->name('admin.notifications.api');
        Route::get('/api/notifications/unread-count', [NotificationController::class, 'getAdminUnreadCount'])->name('admin.notifications.unread-count');

        // Services management
        Route::get('/services', [AdminController::class, 'services'])->name('admin.services.index');
        Route::get('/services/create', [AdminController::class, 'createService'])->name('admin.services.create');
        Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
        Route::get('/services/{service}/edit', [AdminController::class, 'editService'])->name('admin.services.edit');
        Route::put('/services/{service}', [AdminController::class, 'updateService'])->name('admin.services.update');
        Route::delete('/services/{service}', [AdminController::class, 'destroyService'])->name('admin.services.destroy');

        // Discounts management
        Route::get('/discounts', [AdminController::class, 'discounts'])->name('admin.discounts.index');
        Route::get('/discounts/create', [AdminController::class, 'createDiscount'])->name('admin.discounts.create');
        Route::post('/discounts', [AdminController::class, 'storeDiscount'])->name('admin.discounts.store');
        Route::get('/discounts/{discount}/edit', [AdminController::class, 'editDiscount'])->name('admin.discounts.edit');
        Route::put('/discounts/{discount}', [AdminController::class, 'updateDiscount'])->name('admin.discounts.update');
        Route::delete('/discounts/{discount}', [AdminController::class, 'destroyDiscount'])->name('admin.discounts.destroy');

        // Admin Profile
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

        // Jadwal & Slot Management - Simple Interface
        Route::get('/jadwal-slot', [\App\Http\Controllers\Admin\SimpleScheduleController::class, 'index'])->name('admin.slots.index');
        Route::post('/jadwal-slot', [\App\Http\Controllers\Admin\SimpleScheduleController::class, 'store'])->name('admin.slots.store');
        Route::get('/jadwal-slot/list', [\App\Http\Controllers\Admin\SimpleScheduleController::class, 'list']);
        Route::post('/jadwal-slot/toggle', [\App\Http\Controllers\Admin\SimpleScheduleController::class, 'toggle']);
        Route::post('/jadwal-slot/delete', [\App\Http\Controllers\Admin\SimpleScheduleController::class, 'delete']);
        Route::post('/jadwal-slot/slot', [\App\Http\Controllers\Admin\SlotController::class, 'store'])->name('admin.slots.store');
        Route::patch('/jadwal-slot/slot/{slot}', [\App\Http\Controllers\Admin\SlotController::class, 'update'])->name('admin.slots.update');
        Route::delete('/jadwal-slot/slot/{slot}', [\App\Http\Controllers\Admin\SlotController::class, 'destroy'])->name('admin.slots.destroy');
        Route::post('/jadwal-slot/operational-days', [\App\Http\Controllers\Admin\SlotController::class, 'updateOperationalDays'])->name('admin.operational-days.update');
        Route::post('/jadwal-slot/disable-date', [\App\Http\Controllers\Admin\SlotController::class, 'disableDate'])->name('admin.slots.disable-date');
        
        // Alias routes untuk compatibility
        Route::post('/operational-days', [\App\Http\Controllers\Admin\OperationalDayController::class, 'update'])->name('admin.operational-days.update');
        Route::post('/operational-days/generate-slots', [\App\Http\Controllers\Admin\OperationalDayController::class, 'generateSlots'])->name('admin.operational-days.generate-slots');
        
        // Payment proof route
        Route::get('/payment-proof/{filename}', [AdminController::class, 'showPaymentProof'])->name('admin.payment-proof');
    });
});

// Laravel Breeze dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
