<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    // Daftarkan kolom-kolom ini agar boleh diisi secara massal
    protected $fillable = [
        'supplier_name', 
        'purchase_date', 
        'total_cost'
    ];

    public function details() 
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}