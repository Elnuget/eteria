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
        Schema::table('balances', function (Blueprint $table) {
            // Primero eliminamos la restricción de clave foránea existente
            $table->dropForeign(['proyecto_id']);
            
            // Luego modificamos la columna para que acepte valores nulos
            $table->foreignId('proyecto_id')->nullable()->change();
            
            // Finalmente, volvemos a agregar la restricción de clave foránea
            $table->foreign('proyecto_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balances', function (Blueprint $table) {
            // Eliminamos la restricción de clave foránea
            $table->dropForeign(['proyecto_id']);
            
            // Volvemos a hacer la columna NOT NULL
            $table->foreignId('proyecto_id')->change();
            
            // Restauramos la restricción de clave foránea
            $table->foreign('proyecto_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }
};
