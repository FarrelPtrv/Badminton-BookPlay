<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminCourtController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

// Public Routes (Guest & Customer)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courts', [HomeController::class, 'courts'])->name('courts.index');
Route::get('/courts/{court}', [HomeController::class, 'courtDetail'])->name('courts.show');
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Customer Booking Operations
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/my-bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/my-bookings/{booking}/payment', [BookingController::class, 'uploadPayment'])->name('bookings.payment');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/my-bookings/{booking}/ticket', [BookingController::class, 'printTicket'])->name('bookings.ticket');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Master Lapangan
        Route::resource('courts', AdminCourtController::class)->except(['show']);

        // Booking & Walk-in & Schedule Blocking
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/walkin', [AdminBookingController::class, 'createWalkin'])->name('bookings.walkin');
        Route::post('/bookings/walkin', [AdminBookingController::class, 'storeWalkin'])->name('bookings.walkin.store');
        Route::get('/bookings/block-schedule', [AdminBookingController::class, 'blockSchedule'])->name('bookings.block');
        Route::post('/bookings/block-schedule', [AdminBookingController::class, 'storeBlockSchedule'])->name('bookings.block.store');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/verify', [AdminBookingController::class, 'verifyPayment'])->name('bookings.verify');

        // Laporan Keuangan
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/print', [AdminReportController::class, 'print'])->name('reports.print');
        Route::get('/reports/export-csv', [AdminReportController::class, 'exportCsv'])->name('reports.export_csv');
    });
});
