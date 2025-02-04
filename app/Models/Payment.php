<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'balance_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'descripcion'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2'
    ];

    /**
     * Obtiene el balance asociado al pago
     */
    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class);
    }
} 