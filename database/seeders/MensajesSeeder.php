<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MensajesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mensajes para Juan Pérez
        DB::table('mensajes')->insert([
            [
                'contacto_id' => 1,
                'mensaje' => '¡Hola! Necesito agendar una cita para consulta.',
                'estado' => 'entrada',
                'fecha' => now()->subMinutes(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => 1,
                'mensaje' => 'Por supuesto, ¿para qué día le gustaría agendar su cita?',
                'estado' => 'salida',
                'fecha' => now()->subMinutes(25),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => 1,
                'mensaje' => 'Me gustaría para mañana en la mañana si es posible.',
                'estado' => 'entrada',
                'fecha' => now()->subMinutes(20),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Mensajes para María González
        DB::table('mensajes')->insert([
            [
                'contacto_id' => 2,
                'mensaje' => 'Buenos días, quisiera información sobre los servicios.',
                'estado' => 'entrada',
                'fecha' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => 2,
                'mensaje' => '¿Puedo agendar una cita para la próxima semana?',
                'estado' => 'entrada',
                'fecha' => now()->subHours(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Mensajes para Carlos Rodríguez
        DB::table('mensajes')->insert([
            [
                'contacto_id' => 3,
                'mensaje' => 'Necesito una cita urgente para hoy.',
                'estado' => 'entrada',
                'fecha' => now()->subHours(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contacto_id' => 3,
                'mensaje' => 'Tenemos un espacio disponible en 2 horas, ¿le parece bien?',
                'estado' => 'salida',
                'fecha' => now()->subHours(2)->subMinutes(45),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 