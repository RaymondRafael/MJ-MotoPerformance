<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceDetail extends Model
{
    protected $fillable = [
        'service_id', 'sparepart_id', 'quantity', 'price', 'subtotal'
    ];

    // Relasi balik: Detail ini masuk ke nota Servis yang mana?
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Relasi balik: Barang apa yang dipakai di baris ini?
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}