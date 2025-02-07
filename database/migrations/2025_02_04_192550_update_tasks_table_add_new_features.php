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
                      ->default('intermedia');
            }
            
            // Agregar campos faltantes
            if (!Schema::hasColumn('tasks', 'prioridad')) {
                $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            }
            if (!Schema::hasColumn('tasks', 'fecha_asignacion')) {
                $table->timestamp('fecha_asignacion')->nullable();
            }
            if (!Schema::hasColumn('tasks', 'fecha_limite')) {
                $table->timestamp('fecha_limite')->nullable();
            }
            if (!Schema::hasColumn('tasks', 'fecha_recordatorio')) {
                $table->timestamp('fecha_recordatorio')->nullable();
            }
            if (!Schema::hasColumn('tasks', 'recordatorio_enviado')) {
                $table->boolean('recordatorio_enviado')->default(false);
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
            if (Schema::hasColumn('tasks', 'prioridad')) {
                $table->dropColumn('prioridad');
            }
            if (Schema::hasColumn('tasks', 'fecha_asignacion')) {
                $table->dropColumn('fecha_asignacion');
            }
            if (Schema::hasColumn('tasks', 'fecha_limite')) {
                $table->dropColumn('fecha_limite');
            }
            if (Schema::hasColumn('tasks', 'fecha_recordatorio')) {
                $table->dropColumn('fecha_recordatorio');
            }
            if (Schema::hasColumn('tasks', 'recordatorio_enviado')) {
                $table->dropColumn('recordatorio_enviado');
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