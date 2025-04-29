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
            'contexto' => 'Eteria, una empresa de desarrollo de software ubicada en Quito, Ecuador. Nos especializamos en crear soluciones tecnológicas personalizadas para empresas. Puedes conocer nuestros proyectos en https://eteriaecuador.com/. 🤝'
        ]);

        // Ejecutar los seeders en orden
        $this->call([
            ContactosSeeder::class,
            MensajesSeeder::class,
            ContactoWebsSeeder::class,
            ChatWebsSeeder::class,
            TurnosSeeder::class,
        ]);
    }
}
