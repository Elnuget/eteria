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
            $table->string('chat_id')->unique(); // chatweb1, chatweb2, etc.
            $table->text('mensaje');
            $table->enum('tipo', ['usuario', 'bot']);
            $table->timestamps();
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
