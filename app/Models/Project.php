<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}