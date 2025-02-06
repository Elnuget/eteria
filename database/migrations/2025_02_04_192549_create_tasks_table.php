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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('proyecto_id')->constrained('projects')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'en progreso', 'completada'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('dificultad', ['facil', 'intermedia', 'dificil', 'experto'])->default('intermedia');
            $table->foreignId('desarrollado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('completado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('tiempo_estimado')->nullable(); // En minutos
            $table->integer('tiempo_real')->nullable(); // En minutos
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamp('fecha_limite')->nullable();
            $table->timestamp('tiempo_inicio')->nullable();
            $table->timestamp('fecha_recordatorio')->nullable();
            $table->boolean('recordatorio_enviado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
