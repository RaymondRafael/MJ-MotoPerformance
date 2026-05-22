<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 
        'sparepart_id', 
        'historical_name', 
        'historical_price',
        'quantity', 
        'price', 
        'subtotal'
    ];

    public function sparepart() 
    {
        return $this->belongsTo(Sparepart::class);
    }
}