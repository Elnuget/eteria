<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;
    
    protected $table = 'facturas';
    
    protected $fillable = [
        'numero_factura',
        'clave_acceso',
        'estado',
        'xml_ruta',
        'xml_firmado_ruta',
        'pdf_ruta',
        'ambiente',
        'certificado_ruta',
        'certificado_password',
        'certificado_serial',
        'certificado_propietario',
        'certificado_vigencia_hasta',
        'fecha_firmado',
        'fecha_emision',
        'fecha_recepcion',
        'fecha_autorizacion',
        'numero_autorizacion',
        'observaciones'
    ];
    
    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_recepcion' => 'datetime',
        'fecha_autorizacion' => 'datetime',
        'fecha_firmado' => 'datetime',
        'certificado_vigencia_hasta' => 'date'
    ];
    
    // Estados disponibles
    const ESTADOS = [
        'PENDIENTE' => 'Pendiente',
        'FIRMADO' => 'Firmado',
        'RECIBIDA' => 'Recibida',
        'DEVUELTA' => 'Devuelta',
        'AUTORIZADA' => 'Autorizada',
        'NO_AUTORIZADA' => 'No Autorizada'
    ];
    
    // Ambientes disponibles
    const AMBIENTES = [
        '1' => 'Pruebas',
        '2' => 'Producción'
    ];
    
    // Scopes
    public function scopeByEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
    
    public function scopeByAmbiente($query, $ambiente)
    {
        return $query->where('ambiente', $ambiente);
    }
    
    // Accessors
    public function getEstadoLabelAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }
    
    public function getAmbienteLabelAttribute()
    {
        return self::AMBIENTES[$this->ambiente] ?? $this->ambiente;
    }
}
