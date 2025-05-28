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
        Schema::create('contabilidad', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('motivo');
            $table->decimal('valor', 15, 2); // 15 dígitos en total, 2 decimales
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Índices para búsquedas eficientes
            $table->index(['fecha']);
            $table->index(['usuario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contabilidad', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
        Schema::dropIfExists('contabilidad');
    }
};
