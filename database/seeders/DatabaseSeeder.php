<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya buat 1 Akun Admin / Owner
        User::create([
            'name' => 'Administrator MJ',
            'email' => 'admin@mjmoto.com',
            'password' => Hash::make('12345'),
            'role' => 'admin',
        ]);
    }
}