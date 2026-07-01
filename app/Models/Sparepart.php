<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    // KUNCI PERBAIKANNYA ADA DI SINI:
    // Hapus 'category', ganti menjadi 'category_id'
    protected $fillable = [
        'code', 
        'name', 
        'brand', 
        'category_id', 
        'price', 
        'stock'
    ];

    // Relasi: Setiap sparepart terikat pada satu Kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}