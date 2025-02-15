<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'direccion'
    ];

    // Reglas de validación
    public static $rules = [
        'nombre' => 'required|string|max:255',
        'correo' => 'required|email|unique:clientes,correo',
        'telefono' => 'nullable|string|max:50',
        'direccion' => 'nullable|string'
    ];

    /**
     * Obtiene los proyectos asociados al cliente.
     */
    public function proyectos()
    {
        return $this->belongsToMany(Project::class, 'cliente_proyecto', 'cliente_id', 'proyecto_id')
                    ->withTimestamps();
    }
}
