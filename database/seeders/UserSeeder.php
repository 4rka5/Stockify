<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Stockify',
            'email' => 'admin@stockify.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Manajer Gudang
        User::create([
            'name' => 'Manajer Gudang',
            'email' => 'manajer@stockify.com',
            'password' => Hash::make('password'),
            'role' => 'manajer gudang',
        ]);

        // Staff Gudang
        User::create([
            'name' => 'Staff Gudang 1',
            'email' => 'staff1@stockify.com',
            'password' => Hash::make('password'),
            'role' => 'staff gudang',
        ]);

        User::create([
            'name' => 'Staff Gudang 2',
            'email' => 'staff2@stockify.com',
            'password' => Hash::make('password'),
            'role' => 'staff gudang',
        ]);
    }
}
