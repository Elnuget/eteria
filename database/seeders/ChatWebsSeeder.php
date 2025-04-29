<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatWebsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chat para Ana Martínez
        $chatId1 = Str::random(10);
        DB::table('chat_webs')->insert([
            [
                'chat_id' => $chatId1,
                'contacto_web_id' => 1,
                'mensaje' => '¡Hola! Me gustaría conocer más sobre sus servicios de desarrollo.',
                'tipo' => 'usuario',
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ],
            [
                'chat_id' => $chatId1,
                'contacto_web_id' => 1,
                'mensaje' => 'Bienvenida Ana, ¿en qué tipo de proyecto está interesada?',
                'tipo' => 'bot',
                'created_at' => now()->subMinutes(29),
                'updated_at' => now()->subMinutes(29),
            ],
            [
                'chat_id' => $chatId1,
                'contacto_web_id' => 1,
                'mensaje' => 'Necesito una aplicación web para mi negocio. ¿Podría agendar una reunión?',
                'tipo' => 'usuario',
                'created_at' => now()->subMinutes(28),
                'updated_at' => now()->subMinutes(28),
            ],
            [
                'chat_id' => $chatId1,
                'contacto_web_id' => 1,
                'mensaje' => 'Por supuesto, te agendaré una cita con nuestro equipo técnico.',
                'tipo' => 'admin',
                'created_at' => now()->subMinutes(27),
                'updated_at' => now()->subMinutes(27),
            ],
        ]);

        // Chat para Pedro Sánchez
        $chatId2 = Str::random(10);
        DB::table('chat_webs')->insert([
            [
                'chat_id' => $chatId2,
                'contacto_web_id' => 2,
                'mensaje' => 'Buenos días, quisiera información sobre desarrollo de apps móviles',
                'tipo' => 'usuario',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'chat_id' => $chatId2,
                'contacto_web_id' => 2,
                'mensaje' => 'Hola Pedro, con gusto te ayudamos. ¿Para qué plataforma necesitas la app?',
                'tipo' => 'bot',
                'created_at' => now()->subHours(2)->addMinutes(1),
                'updated_at' => now()->subHours(2)->addMinutes(1),
            ],
            [
                'chat_id' => $chatId2,
                'contacto_web_id' => 2,
                'mensaje' => 'Necesito para Android e iOS. ¿Podemos agendar una reunión para mañana?',
                'tipo' => 'usuario',
                'created_at' => now()->subHours(2)->addMinutes(2),
                'updated_at' => now()->subHours(2)->addMinutes(2),
            ],
        ]);
    }
} 