<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['customer_id', 'license_plate', 'brand', 'model', 'color'];

    // Relasi balik: Kendaraan ini milik siapa?
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi: 1 Kendaraan bisa punya banyak riwayat Servis
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}