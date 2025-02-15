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
        Schema::create('cliente_proyecto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('proyecto_id')->constrained('projects')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            
            // Índice único compuesto
            $table->unique(['cliente_id', 'proyecto_id'], 'unico_cliente_proyecto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_proyecto');
    }
};
