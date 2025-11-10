<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Kasir
        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@example.com',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
            'is_active' => true,
            'shift_id' => 1, // sesuaikan shift id yang ada
        ]);

        // Customer
        User::create([
            'name' => 'Customer Member',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'is_active' => true,
        ]);
    }
}
