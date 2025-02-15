<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_entrega',
        'implementado_en',
        'estado'
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'implementado_en' => 'date',
    ];

    /**
     * Obtiene los clientes asociados al proyecto.
     */
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_proyecto', 'proyecto_id', 'cliente_id')
                    ->withTimestamps();
    }
}