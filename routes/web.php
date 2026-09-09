<?php

use App\Http\Controllers\AdminWithdrawalController;
use App\Http\Controllers\AdminCaregiverController;
use App\Http\Controllers\AdminPaymentReconciliationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CaregiverController;
use App\Http\Controllers\CaregiverProfileController;
use App\Http\Controllers\CaregiverWalletController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Schedule::command('app:cancel-expired-bookings')->hourly();

Route::post('/midtrans-callback', [PaymentCallbackController::class, 'receive']);

Route::middleware('auth')->group(function () {
    // Profil (Bisa diakses semua role yang login)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/caregiver-rate', [CaregiverProfileController::class, 'updateRate'])
        ->middleware('role:caregiver')
        ->name('profile.caregiver-rate.update');

    // Katalog Perawat (Bisa dilihat siapa saja)
    Route::get('/caregivers', [CaregiverController::class, 'index'])->name('caregivers.index');
    Route::get('/caregivers/{caregiver}', [CaregiverController::class, 'show'])->name('caregivers.show');

    // ==========================================
    // 2. RUANGAN KHUSUS KLIEN (Dijaga role:client)
    // ==========================================
    Route::middleware(['role:client'])->group(function () {
        Route::resource('patients', PatientController::class);

        // Proses Booking & Ulasan
        Route::get('/booking/create/{caregiver_id}', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
        Route::get('/bookings/{id}/payment-success', [BookingController::class, 'paymentSuccess'])->name('bookings.payment.success');
        Route::get('/bookings/{id}/payment-status', [BookingController::class, 'paymentStatus'])->name('bookings.payment.status');
        Route::patch('/bookings/{id}/confirm-finish', [BookingController::class, 'confirmFinish'])->name('bookings.confirm_finish');
        Route::post('/bookings/{booking}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });

    // ==============================================
    // 3. RUANGAN KHUSUS PERAWAT (Dijaga role:caregiver)
    // ==============================================
    Route::middleware(['role:caregiver', 'verified-caregiver'])->prefix('caregiver')->group(function () {
        // Kelola Pesanan Masuk
        Route::get('/incoming-bookings', [BookingController::class, 'incomingBookings'])->name('caregiver.bookings');
        Route::patch('/bookings/{id}/update-status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
        Route::patch('/bookings/{id}/start', [BookingController::class, 'startService'])->name('bookings.start');
        Route::patch('/bookings/{id}/request-finish', [BookingController::class, 'requestFinish'])->name('bookings.request_finish');

        // Kelola Keuangan
        Route::get('/wallet', [CaregiverWalletController::class, 'index'])->name('caregiver.wallet');
        Route::post('/withdraw', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    });

    // ==========================================
    // 4. RUANGAN KHUSUS ADMIN (Dijaga role:admin)
    // ==========================================
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/caregivers', [AdminCaregiverController::class, 'index'])->name('admin.caregivers.index');
        Route::patch('/caregivers/{caregiver}/verification', [AdminCaregiverController::class, 'updateVerification'])
            ->name('admin.caregivers.verification.update');
        Route::get('/payment-reconciliation', [AdminPaymentReconciliationController::class, 'index'])
            ->name('admin.payments.reconciliation.index');
        Route::patch('/payment-reconciliation/{payment}/refunded', [AdminPaymentReconciliationController::class, 'markRefunded'])
            ->name('admin.payments.reconciliation.refunded');
        Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('admin.withdrawals.index');
        Route::post('/withdrawals/{id}/approve', [AdminWithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
        Route::post('/withdrawals/{id}/reject', [AdminWithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');
    });
});

require __DIR__.'/auth.php';
