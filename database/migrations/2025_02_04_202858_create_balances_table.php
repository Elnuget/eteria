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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('projects')->onDelete('cascade');
            $table->decimal('monto', 10, 2)->default(0.00); // Monto total del saldo
            $table->decimal('monto_pagado', 10, 2)->default(0.00); // Monto pagado hasta el momento
            $table->decimal('monto_pendiente', 10, 2)->default(0.00); // Monto aún pendiente
            $table->date('fecha_generacion'); // Fecha en que se generó este saldo
            $table->enum('tipo_saldo', ['anual', 'mensual', 'unico'])->default('anual'); // Tipo de saldo
            $table->string('motivo')->nullable(); // Motivo del saldo
            $table->boolean('pagado_completo')->default(false); // Indica si este saldo ya se pagó completamente
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};
