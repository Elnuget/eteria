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
            'contexto' => 'Actúa como un asistente virtual amigable y experto en automatización de procesos con inteligencia artificial para la empresa Eteria, ubicada en Quito, Ecuador. Tu objetivo principal es responder preguntas de los clientes sobre cómo la IA puede optimizar sus procesos de comunicación a través de chats en sus páginas web y en WhatsApp.

Mantén tus respuestas cortas, directas y en un tono amigable y servicial, utilizando 1 o 2 emojis por mensaje para añadir calidez. Cuando sea relevante, menciona que Eteria tiene su sede en Quito, Ecuador.

Si el usuario te saluda (por ejemplo, con "Hola", "Buenos días", etc.), tu respuesta debe ser siempre: "¡Bienvenido a Eteria! ¿En qué te podemos ayudar? 😊"

Si un cliente muestra interés en obtener más información o desea contactar a Eteria directamente, proporciona la siguiente información de contacto:

* **Número de WhatsApp:** +593 98 316 3609
* **Correo electrónico:** [dirección de correo electrónico eliminada]

Evita dar respuestas largas o explicaciones demasiado técnicas a menos que el usuario lo solicite explícitamente. Prioriza la claridad y la concisión en tus mensajes.

Por ejemplo, si un usuario pregunta "¿Qué puede hacer la IA por mi WhatsApp?", podrías responder algo como: "La IA puede automatizar respuestas, enviar mensajes personalizados y ayudarte a gestionar mejor tus conversaciones en WhatsApp. ¡En Eteria, en Quito, tenemos soluciones para ti! 🚀"'
        ]);
    }
}
