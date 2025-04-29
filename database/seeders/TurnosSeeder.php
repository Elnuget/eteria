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
        // Turnos para contactos de WhatsApp
        DB::table('turnos')->insert([
            [
                'contacto_id' => 1,
                'contacto_web_id' => null,
                'fecha_turno' => now()->addDay()->setHour(9)->setMinute(0),
                'motivo' => 'Consulta general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => 2,
                'contacto_web_id' => null,
                'fecha_turno' => now()->addWeek()->setHour(14)->setMinute(30),
                'motivo' => 'Primera consulta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => 3,
                'contacto_web_id' => null,
                'fecha_turno' => now()->addHours(2),
                'motivo' => 'Consulta urgente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Turnos para contactos web
        DB::table('turnos')->insert([
            [
                'contacto_id' => null,
                'contacto_web_id' => 1,
                'fecha_turno' => now()->addDays(2)->setHour(10)->setMinute(0),
                'motivo' => 'Desarrollo de aplicación web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => null,
                'contacto_web_id' => 2,
                'fecha_turno' => now()->addDay()->setHour(15)->setMinute(0),
                'motivo' => 'Desarrollo de app móvil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 