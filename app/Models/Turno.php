<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'contacto_id',
        'fecha_turno',
        'motivo'
    ];

    protected $casts = [
        'fecha_turno' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Obtener el contacto asociado al turno.
     */
    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }
} 