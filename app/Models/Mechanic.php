<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mechanic extends Model
{
    // Kolom apa saja yang boleh diisi melalui form (Mass Assignment)
    protected $fillable = ['name', 'phone_number'];

    // Relasi: 1 Mekanik bisa mengerjakan banyak Servis
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}