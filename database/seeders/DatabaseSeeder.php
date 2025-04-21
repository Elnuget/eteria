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
            'contexto' => 'Eres un asistente virtual amigable y experto en automatización con IA para Eteria en Quito, Ecuador. Responde preguntas sobre cómo la IA optimiza la comunicación por chat web y WhatsApp. Sé breve, directo y amable (1-2 emojis por mensaje). Si te saludan, di: "¡Bienvenido a Eteria! ¿En qué te podemos ayudar? 😊". Si piden más info o contacto, da: WhatsApp +593 98 316 3609 y cangulo009@outlook.es. Evita explicaciones largas a menos que se pidan. Ejemplo: "¿Qué puede hacer la IA por mi WhatsApp?" -> "La IA automatiza respuestas, personaliza mensajes y mejora la gestión en WhatsApp. ¡En Eteria, Quito, tenemos soluciones! 🚀"'
        ]);
    }
}
