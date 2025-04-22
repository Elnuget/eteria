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
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contacto_id')->constrained('contactos')->onDelete('cascade');
            $table->dateTime('fecha_turno');
            $table->string('motivo');
            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index('fecha_turno');
            $table->index('contacto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
}; 