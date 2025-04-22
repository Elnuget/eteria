<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Contacto;
use App\Models\Contexto;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Twilio\TwiML\MessagingResponse;

class WebhookController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://api.deepseek.com/v1/chat/completions';
    protected $formatoTurno = '/TURNO_CONFIRMADO:(\d{4}-\d{2}-\d{2} \d{2}:\d{2}):(.+)/';

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key');
    }

    public function handleTwilioWebhook(Request $request)
    {
        try {
            // Obtener el mensaje y número de WhatsApp
            $receivedMessage = $request->input('Body');
            $fromNumber = $request->input('From');
            $profileName = $request->input('ProfileName');
            
            // Limpiar el número de teléfono eliminando "whatsapp:" y "+"
            $cleanNumber = str_replace(['whatsapp:', '+'], '', $fromNumber);

            // Buscar el contacto
            $contacto = Contacto::where('numero', $cleanNumber)->first();

            if (!$contacto) {
                // Crear nuevo contacto
                $contacto = new Contacto();
                $contacto->numero = $cleanNumber;
                $contacto->estado = 'iniciado';
                
                // Si tenemos el nombre del perfil de WhatsApp, lo usamos
                if ($profileName) {
                    $contacto->nombre = $profileName;
                }
                
                // Si el primer mensaje parece ser una presentación con nombre, extraerlo
                if (preg_match('/(?:me llamo|soy|hola[,]? soy|mi nombre es) ([A-Za-zÁáÉéÍíÓóÚúÑñ\s]+)/i', $receivedMessage, $matches)) {
                    $contacto->nombre = trim($matches[1]);
                }
                
                $contacto->save();

                // Guardar el mensaje recibido
                Mensaje::create([
                    'contacto_id' => $contacto->id,
                    'mensaje' => $receivedMessage,
                    'estado' => 'entrada',
                    'fecha' => now()
                ]);

                // Mensaje de bienvenida personalizado que incluye la solicitud de información
                $nombreSaludo = $contacto->nombre ? " {$contacto->nombre}" : "";
                $mensajeBienvenida = "¡Hola{$nombreSaludo}! 😊 Soy el asistente virtual de Eteria. Nos especializamos en soluciones digitales para hacer crecer tu negocio: 📱 apps web/móvil, 🛍️ tiendas online, 🤖 automatización y 📊 gestión. ¿Nos cuentas sobre tu negocio? 🚀";

                Mensaje::create([
                    'contacto_id' => $contacto->id,
                    'mensaje' => $mensajeBienvenida,
                    'estado' => 'salida',
                    'fecha' => now()
                ]);

                return (new MessagingResponse())
                    ->message($mensajeBienvenida)
                    ->__toString();
            }

            // Para contactos existentes, solo guardar el mensaje recibido
            Mensaje::create([
                'contacto_id' => $contacto->id,
                'mensaje' => $receivedMessage,
                'estado' => 'entrada',
                'fecha' => now()
            ]);

            // Si es un mensaje que contiene un nombre y el contacto no tiene nombre aún
            if (!$contacto->nombre && preg_match('/(?:me llamo|soy|hola[,]? soy|mi nombre es) ([A-Za-zÁáÉéÍíÓóÚúÑñ\s]+)/i', $receivedMessage, $matches)) {
                $contacto->nombre = trim($matches[1]);
                $contacto->save();
            }

            // Verificar si ya tiene un turno pendiente
            $turnoExistente = Turno::where('contacto_id', $contacto->id)
                ->where('fecha_turno', '>=', now())
                ->first();

            // Obtener historial de mensajes para este contacto
            $historialMensajes = Mensaje::where('contacto_id', $contacto->id)
                ->orderBy('fecha', 'asc')
                ->get();

            // Preparar mensajes para la API
            $messages = [];
            
            // Obtener el contexto específico para este contacto
            $contextBase = 'Eres un asistente virtual de Eteria. Guía la conversación para obtener la siguiente información: ' .
                         '1) Tipo de proyecto/servicio que necesitan, ' .
                         '2) Fecha preferida (días laborables L-V), ' .
                         '3) Hora preferida (9:00 a 17:00), ' .
                         '4) Breve descripción del proyecto. ' .
                         'Solo cuando tengas TODA esta información, responde con el formato: ' .
                         'TURNO_CONFIRMADO:YYYY-MM-DD HH:mm:MOTIVO. ' .
                         'Si falta información, continúa preguntando amablemente. ' .
                         'Mantén un tono profesional y cercano.';

            // Agregar información sobre turno existente si lo hay
            if ($turnoExistente) {
                $contextBase .= "\nEste contacto ya tiene una cita agendada para el " . 
                              $turnoExistente->fecha_turno->format('d/m/Y H:i') . 
                              ". Motivo: " . $turnoExistente->motivo . 
                              ". Infórmale amablemente que debe esperar a que esta cita se complete antes de agendar una nueva.";
            }

            // Agregar el contexto del sistema
            $messages[] = [
                'role' => 'system',
                'content' => $contextBase
            ];

            // Agregar el historial de mensajes
            foreach ($historialMensajes as $mensaje) {
                $messages[] = [
                    'role' => $mensaje->estado === 'entrada' ? 'user' : 'assistant',
                    'content' => $mensaje->mensaje
                ];
            }

            // Obtener respuesta de DeepSeek
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'deepseek-chat',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 150
            ]);

            if ($response->successful()) {
                $aiResponse = $response->json()['choices'][0]['message']['content'];

                // Guardar la respuesta enviada
                Mensaje::create([
                    'contacto_id' => $contacto->id,
                    'mensaje' => $aiResponse,
                    'estado' => 'salida',
                    'fecha' => now()
                ]);

                // Verificar si hay un mensaje de confirmación de turno en los últimos mensajes
                $ultimosMensajes = Mensaje::where('contacto_id', $contacto->id)
                    ->orderBy('fecha', 'desc')
                    ->take(5)
                    ->get();

                foreach ($ultimosMensajes as $mensaje) {
                    if (preg_match($this->formatoTurno, $mensaje->mensaje, $matches)) {
                        return $this->procesarConfirmacionTurno($contacto, $matches[1], $matches[2]);
                    }
                }

                // Crear y retornar respuesta TwiML
                return (new MessagingResponse())
                    ->message($aiResponse)
                    ->__toString();
            } else {
                throw new \Exception('Error en la API de DeepSeek');
            }
        } catch (\Exception $e) {
            Log::error('Error en webhook de Twilio: ' . $e->getMessage());
            
            return (new MessagingResponse())
                ->message('Lo siento, hubo un error al procesar tu mensaje.')
                ->__toString();
        }
    }

    /**
     * Procesa la confirmación de un turno
     */
    protected function procesarConfirmacionTurno($contacto, $fechaHora, $motivo)
    {
        try {
            // Verificar si ya tiene un turno pendiente
            $turnoExistente = Turno::where('contacto_id', $contacto->id)
                ->where('fecha_turno', '>=', now())
                ->first();

            if ($turnoExistente) {
                $saludo = $contacto->nombre ? "Hola {$contacto->nombre}" : "Hola";
                $mensaje = "{$saludo}, ya tienes una cita agendada para el " . 
                          $turnoExistente->fecha_turno->format('d/m/Y H:i') . 
                          ". Por favor, espera a que esta cita se complete antes de agendar una nueva. Si necesitas modificarla, contáctanos directamente. 🗓️";
            } else {
                // Convertir la fecha y hora a objeto Carbon
                $fechaTurno = Carbon::parse($fechaHora);

                // Verificar si ya existe un turno en esa fecha y hora
                $turnoMismaFecha = Turno::where('fecha_turno', $fechaTurno)->first();

                if ($turnoMismaFecha) {
                    $mensaje = "Lo siento, el horario seleccionado ya está reservado. ¿Te gustaría agendar en otro horario? Tenemos disponibilidad de lunes a viernes, de 9:00 a 17:00. 📅";
                } else {
                    // Crear el nuevo turno
                    Turno::create([
                        'contacto_id' => $contacto->id,
                        'fecha_turno' => $fechaTurno,
                        'motivo' => $motivo
                    ]);

                    $saludo = $contacto->nombre ? "{$contacto->nombre}" : "Estimado/a cliente";
                    $mensaje = "¡Perfecto {$saludo}! Tu cita ha sido confirmada para el " . 
                              $fechaTurno->format('d/m/Y') . " a las " . 
                              $fechaTurno->format('H:i') . ". \n\n" .
                              "📋 Motivo: " . $motivo . "\n" .
                              "📍 Ubicación: Quito, Ecuador\n" .
                              "🌐 Más información sobre nosotros: https://eteriaecuador.com\n\n" .
                              "Te esperamos para discutir tu proyecto. Si necesitas hacer algún cambio, no dudes en avisarnos.";
                }
            }

            // Guardar el mensaje de respuesta
            Mensaje::create([
                'contacto_id' => $contacto->id,
                'mensaje' => $mensaje,
                'estado' => 'salida',
                'fecha' => now()
            ]);

            return (new MessagingResponse())
                ->message($mensaje)
                ->__toString();

        } catch (\Exception $e) {
            Log::error('Error al procesar confirmación de turno: ' . $e->getMessage());
            
            return (new MessagingResponse())
                ->message('Lo siento, hubo un error al procesar el turno.')
                ->__toString();
        }
    }
} 