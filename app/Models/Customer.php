<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // 1. $fillable yang lama ditimpa dengan yang baru ini
    protected $fillable = ['user_id', 'name', 'phone_number', 'address'];

    // 2. Tambahkan relasi ke tabel User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Relasi ke kendaraan (yang lama) tetap dibiarkan
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}