<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    protected $fillable = ['name', 'price', 'stock', 'description'];

    // Relasi: 1 Sparepart bisa tercatat di banyak Detail Servis
    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetail::class);
    }
}