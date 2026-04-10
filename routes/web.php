<?php

use Illuminate\Support\Facades\Route;

// Import semua Controller
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\MechanicController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DashboardController;

// 1. Route Autentikasi (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Lupa Password (Lengkap)
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');


// 2. Route Publik
Route::get('/', function () {
    return view('welcome');
});

// Route Tracking (Sekarang kita kunci dengan middleware 'auth' agar hanya yang login yang bisa melacak)
Route::middleware(['auth'])->group(function () {
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::post('/tracking', [TrackingController::class, 'search'])->name('tracking.search');
});

// 3. Route Admin (Dikunci dengan middleware 'auth'!)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('mechanics', MechanicController::class);
    Route::resource('spareparts', SparepartController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('services', ServiceController::class);
    Route::put('services/{id}/status', [ServiceController::class, 'updateStatus'])->name('services.updateStatus');
    // Rute untuk Fitur Kalkulasi & Nota Servis
    Route::post('services/{id}/add-sparepart', [ServiceController::class, 'addSparepart'])->name('services.addSparepart');
    Route::delete('services/{id}/remove-sparepart/{detail_id}', [ServiceController::class, 'removeSparepart'])->name('services.removeSparepart');
    Route::put('services/{id}/update-cost', [ServiceController::class, 'updateServiceCost'])->name('services.updateCost');
});

// 4. Route Dashboard Admin
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');