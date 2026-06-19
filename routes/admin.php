<?php

// Admin routes untuk backward compatibility
// Route utama sudah dipindah ke web.php

use App\Http\Controllers\Admin\OperationalDayController;
use App\Http\Controllers\Admin\BookingSlotController;

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Legacy routes - keep for existing links
    Route::get('/operational-days', [OperationalDayController::class, 'index'])->name('admin.operational-days.index');
    Route::post('/operational-days/generate-slots', [OperationalDayController::class, 'generateSlots'])->name('admin.operational-days.generate-slots');
    
    Route::get('/booking-slots', [BookingSlotController::class, 'index'])->name('admin.booking-slots.index');
    Route::post('/booking-slots', [BookingSlotController::class, 'store'])->name('admin.booking-slots.store');
    
    // Quick Actions
    Route::post('/booking-slots/{slot}/toggle-status', [\App\Http\Controllers\Admin\BookingSlotQuickActionController::class, 'toggleStatus'])->name('admin.booking-slots.toggle-status');
    Route::post('/booking-slots/toggle-all', [\App\Http\Controllers\Admin\BookingSlotQuickActionController::class, 'toggleAll'])->name('admin.booking-slots.toggle-all');
});