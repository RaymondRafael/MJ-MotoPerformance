<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Cari user dengan email ini. Jika ada, update. Jika tidak, buat baru.
        User::updateOrCreate(
            ['email' => 'admin.mjmoto@gmail.com'], // Kunci pencarian
            [
                'name' => 'Administrator MJ',
                'password' => Hash::make('12345'),
                'role' => 'admin',
            ]
        );
    }
}