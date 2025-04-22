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
            
            // Limpiar el número de teléfono eliminando "whatsapp:" y "+"
            $cleanNumber = str_replace(['whatsapp:', '+'], '', $fromNumber);

            // Buscar o crear el contacto
            $contacto = Contacto::firstOrCreate(
                ['numero' => $cleanNumber],
                ['estado' => 'iniciado']
            );

            // Verificar si el mensaje es una confirmación de turno
            if (preg_match($this->formatoTurno, $receivedMessage, $matches)) {
                return $this->procesarConfirmacionTurno($contacto, $matches[1], $matches[2]);
            }

            // Guardar el mensaje recibido
            Mensaje::create([
                'contacto_id' => $contacto->id,
                'mensaje' => $receivedMessage,
                'estado' => 'entrada',
                'fecha' => now()
            ]);

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
            $contextBase = 'Eres un asistente virtual de Eteria, una empresa de desarrollo web. ' .
                         'Para agendar un turno, debes responder con el formato exacto: ' .
                         'TURNO_CONFIRMADO:YYYY-MM-DD HH:mm:MOTIVO ' .
                         'Por ejemplo: TURNO_CONFIRMADO:2024-03-20 15:30:Consulta desarrollo web';

            // Agregar información sobre turno existente si lo hay
            if ($turnoExistente) {
                $contextBase .= "\nEste contacto ya tiene un turno agendado para el " . 
                              $turnoExistente->fecha_turno->format('d/m/Y H:i') . 
                              " con motivo: " . $turnoExistente->motivo . 
                              ". Debes informarle que no puede agendar otro turno hasta que este se complete.";
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
                $mensaje = "Ya tienes un turno agendado para el " . 
                          $turnoExistente->fecha_turno->format('d/m/Y H:i') . 
                          ". No puedes agendar otro turno hasta que este se complete.";
            } else {
                // Crear el nuevo turno
                Turno::create([
                    'contacto_id' => $contacto->id,
                    'fecha_turno' => Carbon::parse($fechaHora),
                    'motivo' => $motivo
                ]);

                $mensaje = "Turno registrado";
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