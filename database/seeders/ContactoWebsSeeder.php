<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactoWebsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('contacto_webs')->insert([
            [
                'nombre' => 'Ana Martínez',
                'celular' => '593995555555',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Pedro Sánchez',
                'celular' => '593996666666',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 