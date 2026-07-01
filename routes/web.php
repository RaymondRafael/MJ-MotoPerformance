<?php

use Illuminate\Support\Facades\Route;

// Import semua Controller
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerWebAuthController; // <-- Controller Baru
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\MechanicController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Http\Request;

// =========================================================================
// 1. JALUR LOGIN SATU PINTU (Admin & Pelanggan)
// =========================================================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Lupa Password (Admin)
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');

// =========================================================================
// 2. JALUR REGISTER PELANGGAN WEB
// =========================================================================
// Gunakan CustomerWebAuthController khusus untuk fungsi register saja
Route::get('/register', [CustomerWebAuthController::class, 'showRegisterForm'])->name('customer.register')->middleware('guest:customer');
Route::post('/register', [CustomerWebAuthController::class, 'register'])->name('customer.register.submit');

// =========================================================================
// 3. RUTE PUBLIK
// =========================================================================
Route::get('/', function () {
    return view('welcome');
});
Route::get('/mobile-reset-password', function (Request $request) {
    $token = $request->query('token');
    $email = $request->query('email');
    return view('mobile_redirect', compact('token', 'email'));
});

// =========================================================================
// 4. HALAMAN KHUSUS PELANGGAN (Wajib Login sebagai Pelanggan)
// =========================================================================
// Perhatikan penggunaan middleware 'auth:customer'
Route::middleware(['auth:customer'])->group(function () {
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::post('/tracking', [TrackingController::class, 'search'])->name('tracking.search');
});

// =========================================================================
// 5. HALAMAN KHUSUS ADMIN (Wajib Login sebagai Admin)
// =========================================================================
// Perhatikan: /dashboard sudah dipindahkan ke blok Admin ini
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('mechanics', MechanicController::class);
    Route::resource('spareparts', SparepartController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('services', ServiceController::class);
    
    Route::put('services/{id}/status', [ServiceController::class, 'updateStatus'])->name('services.updateStatus');
    Route::post('services/{id}/add-sparepart', [ServiceController::class, 'addSparepart'])->name('services.addSparepart');
    Route::delete('services/{id}/remove-sparepart/{detail_id}', [ServiceController::class, 'removeSparepart'])->name('services.removeSparepart');
    Route::put('services/{id}/update-cost', [ServiceController::class, 'updateServiceCost'])->name('services.updateCost');

    Route::resource('purchases', PurchaseController::class);
    Route::delete('purchases/item/{id}', [PurchaseController::class, 'destroyItem'])->name('purchases.destroyItem');
});