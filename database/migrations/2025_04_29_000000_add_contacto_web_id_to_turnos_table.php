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
        Schema::table('turnos', function (Blueprint $table) {
            // Hacer nullable la columna existente contacto_id
            $table->unsignedBigInteger('contacto_id')->nullable()->change();

            // Añadir la nueva columna contacto_web_id
            $table->foreignId('contacto_web_id')
                  ->nullable() // Puede ser nulo si el turno es de WhatsApp
                  ->after('contacto_id') // Colocar después de contacto_id
                  ->constrained('contacto_webs') // Referencia a la tabla contacto_webs
                  ->onDelete('cascade'); // O 'set null' según prefieras

            // Añadir índice para mejorar rendimiento
            $table->index('contacto_web_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            // Eliminar la clave foránea y la columna
            $table->dropForeign(['contacto_web_id']);
            $table->dropIndex(['contacto_web_id']);
            $table->dropColumn('contacto_web_id');

            // Revertir contacto_id a non-nullable (si tenía esa restricción antes)
            // Nota: Asegúrate de que esto sea correcto para tu estado anterior.
            // Si contacto_id ya podía ser null, elimina la siguiente línea.
            $table->unsignedBigInteger('contacto_id')->nullable(false)->change();
        });
    }
}; 