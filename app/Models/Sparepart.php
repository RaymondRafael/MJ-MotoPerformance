<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    // Hanya tersisa 3 kolom ini
    protected $fillable = ['name', 'brand', 'category', 'price', 'stock'];
}