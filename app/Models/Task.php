<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'proyecto_id',
        'estado',
        'desarrollado_por',
        'completado_por',
        'tiempo_estimado',
        'tiempo_real',
        'tiempo_inicio',
    ];

    protected $casts = [
        'tiempo_estimado' => 'integer',
        'tiempo_real' => 'integer',
    ];

    protected $dates = [
        'tiempo_inicio',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'proyecto_id');
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'desarrollado_por');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completado_por');
    }
} 