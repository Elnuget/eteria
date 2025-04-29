<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('turnos')->insert([
            [
                'contacto_id' => 1,
                'fecha_turno' => now()->addDay()->setHour(9)->setMinute(0),
                'motivo' => 'Consulta general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => 2,
                'fecha_turno' => now()->addWeek()->setHour(14)->setMinute(30),
                'motivo' => 'Primera consulta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => 3,
                'fecha_turno' => now()->addHours(2),
                'motivo' => 'Consulta urgente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 