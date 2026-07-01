<?php

namespace App\Models;

// WAJIB DITAMBAHKAN AGAR BISA LOGIN (MULTI-AUTH)
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // 1. $fillable disesuaikan dengan struktur tabel terbaru (tanpa user_id)
    protected $fillable = [
        'name', 
        'phone_number', 
        'address',
        'email',      // Tambahan baru untuk login
        'password'    // Tambahan baru untuk login
    ];

    // 2. Wajib ditambahkan untuk menyembunyikan password di respon API
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 3. Relasi ke kendaraan TETAP DIBIARKAN agar riwayat servis aman
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}