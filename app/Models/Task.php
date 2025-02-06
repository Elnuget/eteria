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
        'prioridad',
        'dificultad',
        'desarrollado_por',
        'completado_por',
        'tiempo_estimado',
        'tiempo_real',
        'fecha_asignacion',
        'fecha_limite',
        'tiempo_inicio',
        'fecha_recordatorio',
        'recordatorio_enviado'
    ];

    protected $casts = [
        'tiempo_estimado' => 'integer',
        'tiempo_real' => 'integer',
        'recordatorio_enviado' => 'boolean',
        'fecha_asignacion' => 'datetime',
        'tiempo_inicio' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'fecha_asignacion',
        'fecha_limite',
        'tiempo_inicio',
        'fecha_recordatorio'
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