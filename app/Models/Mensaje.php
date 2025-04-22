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
        'contacto_id',
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

    /**
     * Obtener el contacto asociado al mensaje.
     */
    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    protected static function booted()
    {
        static::created(function ($mensaje) {
            if ($mensaje->estado === 'salida') {
                try {
                    $whatsapp = new WhatsAppController();
                    $mensaje_texto = $mensaje->mensaje;
                    
                    // Obtenemos el número del contacto asociado
                    $whatsapp->sendMessage($mensaje->contacto->numero, $mensaje_texto);
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