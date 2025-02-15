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
            $table->dropForeign(['proyecto_id']);
            $table->foreignId('proyecto_id')->nullable()->change();
            $table->foreign('proyecto_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
            $table->foreignId('proyecto_id')->nullable(false)->change();
            $table->foreign('proyecto_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }
};
