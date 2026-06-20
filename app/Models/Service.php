<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'vehicle_id', 
        'mechanic_id', 
        'historical_mechanic_name',
        'complaint', 
        'status', 
        'service_cost', 
        'total_cost', 
        'notes',
        'historical_customer_name',
        'historical_customer_phone',
        'historical_license_plate',
        'historical_vehicle_motor'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function details()
    {
        return $this->hasMany(ServiceDetail::class);
    }
}