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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balance_id')->constrained('balances')->onDelete('cascade'); // Relación con balances
            $table->decimal('monto', 10, 2)->default(0.00); // Monto del pago
            $table->date('fecha_pago'); // Fecha en que se realizó el pago
            $table->string('metodo_pago')->nullable(); // Método de pago (Ej: transferencia, tarjeta, efectivo)
            $table->text('descripcion')->nullable(); // Información adicional del pago
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
