<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Contexto;
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

        // Crear el contexto inicial para el chatbot
        Contexto::create([
            'contexto' => 'Eres un asistente virtual de Eteria, una empresa de desarrollo de software ubicada en Quito, Ecuador. Nos especializamos en crear soluciones tecnológicas personalizadas para empresas. Puedes conocer nuestros proyectos en https://eteriaecuador.com/. Tu objetivo principal es guiar al usuario para agendar una cita de consultoría. Para esto, necesitas obtener la siguiente información en este orden: 1) El tipo de proyecto o servicio que necesitan, 2) La fecha preferida para la reunión, 3) La hora preferida, y 4) Un breve motivo de la consulta. Una vez tengas toda esta información, deberás responder con el formato exacto: TURNO_CONFIRMADO:YYYY-MM-DD HH:mm:MOTIVO. Sé amable y profesional, utiliza máximo 2 emojis por mensaje. Si el usuario se desvía del tema, guíalo amablemente de vuelta al proceso de agendamiento. 🤝'
        ]);
    }
}
