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
     * Atributos que no se guardan en la base de datos
     */
    // protected $appends = ['_skip_twilio']; // Comentado o eliminado

    /**
     * Obtener el contacto asociado al mensaje.
     */
    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    protected static function booted()
    {
        // Eliminar el evento 'creating' si existe
        /*
        static::creating(function ($mensaje) {
            // ... lógica eliminada ...
        });
        */

        static::created(function ($mensaje) {
            // Ya no necesitamos verificar _skip_twilio_flag
            if ($mensaje->estado === 'salida') { 
                try {
                    $whatsapp = app(WhatsAppController::class);
                    $mensaje_texto = $mensaje->mensaje;
                    
                    // Asegurarse de que el número tenga el formato correcto
                    $numero = $mensaje->contacto->numero;
                    if (!str_starts_with($numero, '593')) {
                        $numero = '593' . $numero;
                    }
                    
                    \Log::info('Intentando enviar mensaje de WhatsApp', [
                        'numero' => $numero,
                        'mensaje' => $mensaje_texto
                    ]);
                    
                    $resultado = $whatsapp->sendMessage($numero, $mensaje_texto);
                    
                    \Log::info('Resultado del envío de mensaje', $resultado);
                } catch (\Exception $e) {
                    \Log::error('Error al enviar mensaje de WhatsApp: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                        'mensaje_id' => $mensaje->id,
                        'contacto_id' => $mensaje->contacto_id
                    ]);
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