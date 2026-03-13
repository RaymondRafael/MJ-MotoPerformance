<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'vehicle_id', 'mechanic_id', 'complaint', 'status', 
        'service_cost', 'total_cost', 'notes'
    ];

    // Relasi balik: Servis ini untuk kendaraan apa?
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relasi balik: Siapa mekanik yang mengerjakan?
    public function mechanic()
    {
        return $this->belongsTo(Mechanic::class);
    }

    // Relasi: 1 Servis bisa menghabiskan banyak macam Sparepart (Detail)
    public function details()
    {
        return $this->hasMany(ServiceDetail::class);
    }
}