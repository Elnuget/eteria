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
            $table->string('chat_id');
            $table->foreignId('contacto_web_id')->constrained('contacto_webs')->onDelete('cascade'); // Clave foránea
            $table->text('mensaje');
            $table->enum('tipo', ['usuario', 'bot', 'admin']); // Añadir 'admin' si aún no está
            $table->timestamps();

            // Índices para búsquedas eficientes
            $table->index(['chat_id']);
            $table->index(['contacto_web_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_webs', function (Blueprint $table) {
            $table->dropForeign(['contacto_web_id']);
        });
        Schema::dropIfExists('chat_webs');
    }
};
