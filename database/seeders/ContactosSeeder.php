<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('contactos')->insert([
            [
                'numero' => '593987654321',
                'nombre' => 'Juan Pérez',
                'estado' => 'iniciado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'numero' => '593998765432',
                'nombre' => 'María González',
                'estado' => 'por iniciar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'numero' => '593976543210',
                'nombre' => 'Carlos Rodríguez',
                'estado' => 'iniciado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 