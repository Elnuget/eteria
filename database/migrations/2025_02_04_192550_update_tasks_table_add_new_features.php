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
        Schema::table('tasks', function (Blueprint $table) {
            // Modificar el campo tiempo_real para almacenar segundos en lugar de minutos
            $table->integer('tiempo_real')->nullable()->change();
            
            // Agregar campo para la dificultad si no existe
            if (!Schema::hasColumn('tasks', 'dificultad')) {
                $table->enum('dificultad', ['facil', 'intermedia', 'dificil', 'experto'])
                      ->default('intermedia')
                      ->after('prioridad');
            }
            
            // Agregar índices para mejorar el rendimiento
            if (!Schema::hasIndex('tasks', 'tasks_prioridad_index')) {
                $table->index('prioridad');
            }
            if (!Schema::hasIndex('tasks', 'tasks_estado_index')) {
                $table->index('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'dificultad')) {
                $table->dropColumn('dificultad');
            }
            if (Schema::hasIndex('tasks', 'tasks_prioridad_index')) {
                $table->dropIndex(['prioridad']);
            }
            if (Schema::hasIndex('tasks', 'tasks_estado_index')) {
                $table->dropIndex(['estado']);
            }
            $table->integer('tiempo_real')->nullable()->change();
        });
    }
}; 