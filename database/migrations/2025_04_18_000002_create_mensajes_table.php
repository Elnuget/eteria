<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contacto_id')->constrained('contactos')->onDelete('cascade');
            $table->text('mensaje');
            $table->enum('estado', ['entrada', 'salida']);
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();

            // Índices para mejorar el rendimiento de las consultas
            $table->index('contacto_id');
            $table->index('estado');
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
}; 