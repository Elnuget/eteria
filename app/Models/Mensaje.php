<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\WhatsAppController;

class Mensaje extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'numero',
        'nombre',
        'mensaje',
        'estado',
        'fecha'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha' => 'datetime'
    ];

    protected static function booted()
    {
        static::created(function ($mensaje) {
            if ($mensaje->estado === 'salida') {
                try {
                    $whatsapp = new WhatsAppController();
                    // Usamos una plantilla predefinida de WhatsApp
                    $mensaje_texto = "Nuevo mensaje de Eteria:\n\n";
                    $mensaje_texto .= $mensaje->mensaje;
                    
                    // Información de contacto como pie de mensaje
                    $mensaje_texto .= "\n\n";
                    $mensaje_texto .= "Para más información:\n";
                    $mensaje_texto .= "WhatsApp: +593 98 316 3609\n";
                    $mensaje_texto .= "www.eteria.ec";
                    
                    $whatsapp->sendMessage($mensaje->numero, $mensaje_texto);
                } catch (\Exception $e) {
                    \Log::error('Error al enviar mensaje de WhatsApp: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Obtener el usuario que envió el mensaje.
     */
    public function remitenteUser()
    {
        return $this->belongsTo(User::class, 'remitente');
    }

    /**
     * Obtener el usuario destinatario del mensaje.
     */
    public function destinatarioUser()
    {
        return $this->belongsTo(User::class, 'destinatario');
    }

    /**
     * Marcar el mensaje como leído.
     */
    public function marcarComoLeido()
    {
        $this->fecha_lectura = now();
        $this->estado = 'leido';
        $this->save();
    }

    /**
     * Marcar el mensaje como entregado.
     */
    public function marcarComoEntregado()
    {
        $this->estado = 'entregado';
        $this->save();
    }

    /**
     * Marcar el mensaje como eliminado.
     */
    public function marcarComoEliminado()
    {
        $this->estado = 'eliminado';
        $this->save();
    }

    /**
     * Scope para filtrar mensajes de entrada
     */
    public function scopeEntrada($query)
    {
        return $query->where('estado', 'entrada');
    }

    /**
     * Scope para filtrar mensajes de salida
     */
    public function scopeSalida($query)
    {
        return $query->where('estado', 'salida');
    }
} 