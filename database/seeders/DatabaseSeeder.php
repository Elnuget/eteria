<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Carlos',
            'email' => 'cangulo009@outlook.es',
            'email_verified_at' => now(),
            'password' => Hash::make('eteria2024'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Jahxs',
            'email' => 'jahxs2328@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('eteria2024'),
            'is_admin' => true,
        ]);
    }
}
