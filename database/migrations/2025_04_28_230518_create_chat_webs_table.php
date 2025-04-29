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
        Schema::create('chat_webs', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id');  // Ya no es unique
            $table->string('nombre')->nullable();
            $table->string('email')->nullable();
            $table->text('mensaje');
            $table->enum('tipo', ['usuario', 'bot']);
            $table->timestamps();

            // Índice compuesto para búsquedas eficientes
            $table->index(['email', 'chat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_webs');
    }
};
