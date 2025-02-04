<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_entrega',
        'precio',
        'saldo',
        'implementado_en',
        'monto_anual',
        'tiene_pago_unico',
        'monto_unico',
        'tiene_pago_mensual',
        'monto_mensual'
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'implementado_en' => 'date',
        'tiene_pago_unico' => 'boolean',
        'tiene_pago_mensual' => 'boolean',
        'precio' => 'decimal:2',
        'saldo' => 'decimal:2',
        'monto_anual' => 'decimal:2',
        'monto_unico' => 'decimal:2',
        'monto_mensual' => 'decimal:2',
    ];
} 