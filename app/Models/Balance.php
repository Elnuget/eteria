<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    protected $fillable = [
        'proyecto_id',
        'monto',
        'monto_pagado',
        'monto_pendiente',
        'fecha_generacion',
        'tipo_saldo',
        'motivo',
        'pagado_completo'
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
        'pagado_completo' => 'boolean',
        'monto' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'monto_pendiente' => 'decimal:2',
    ];

    /**
     * Obtiene el proyecto al que pertenece este balance.
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'proyecto_id');
    }
}