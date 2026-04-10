<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rute Terlindungi (Hanya bisa diakses jika memiliki Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Rute Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']); 

    // --- KELOMPOK 1: RUTE STATIS (Wajib di atas) ---
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']); 
    Route::get('/services/create', [ServiceController::class, 'create']);
    
    // --- KELOMPOK 2: RUTE DINAMIS BEREKOR {id} (Wajib di bawah) ---
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::put('/services/{id}/status', [ServiceController::class, 'updateStatus']);
    Route::put('/services/{id}/cost', [ServiceController::class, 'updateCost']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    Route::post('/services/{id}/sparepart', [ServiceController::class, 'addSparepart']);
    Route::delete('/services/{id}/sparepart/{detail_id}', [ServiceController::class, 'removeSparepart']);
    Route::put('/services/{id}', [App\Http\Controllers\Api\ServiceController::class, 'update']);

    // RUTE PELANGGAN (Member Area)
    Route::get('/my-garage', [CustomerController::class, 'myGarage']);
});