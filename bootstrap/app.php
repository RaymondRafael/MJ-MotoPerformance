<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // --- LOGIKA PENYELEKSI ROLE LARAVEL 11 (VERSI FINAL & AMAN) ---
        $middleware->redirectUsersTo(function () {
            // Pastikan user benar-benar sudah terbaca sistem sebelum dicek rolenya
            if (auth()->check()) {
                return auth()->user()->role === 'admin' ? '/dashboard' : '/tracking';
            }
            
            // Jika terjadi kegagalan sistem, kembalikan ke halaman awal
            return '/'; 
        });
        // --------------------------------------------------------------

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();