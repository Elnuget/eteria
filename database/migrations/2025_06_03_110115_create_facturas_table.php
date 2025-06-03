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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura', 50);
            $table->string('clave_acceso', 49)->unique();
            $table->enum('estado', ['PENDIENTE', 'FIRMADO', 'RECIBIDA', 'DEVUELTA', 'AUTORIZADA', 'NO_AUTORIZADA'])->default('PENDIENTE');
            $table->string('xml_ruta')->nullable();
            $table->string('xml_firmado_ruta')->nullable();
            $table->string('pdf_ruta')->nullable();
            $table->enum('ambiente', ['1', '2'])->comment('1=Pruebas, 2=Producción');
            
            // Campos para firma electrónica
            $table->string('certificado_ruta')->nullable()->comment('Ruta del archivo .p12');
            $table->string('certificado_password')->nullable()->comment('Password del certificado (encriptado)');
            $table->string('certificado_serial', 100)->nullable()->comment('Número serial del certificado');
            $table->string('certificado_propietario')->nullable()->comment('Propietario del certificado');
            $table->date('certificado_vigencia_hasta')->nullable()->comment('Fecha de vencimiento del certificado');
            $table->datetime('fecha_firmado')->nullable()->comment('Cuándo se firmó el XML');
            
            $table->datetime('fecha_emision');
            $table->datetime('fecha_recepcion')->nullable();
            $table->datetime('fecha_autorizacion')->nullable();
            $table->string('numero_autorizacion', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para mejorar consultas
            $table->index('clave_acceso');
            $table->index('estado');
            $table->index('ambiente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
