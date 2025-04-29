<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Contexto;
use App\Models\Turno;
use App\Models\ChatWeb;
use Carbon\Carbon;

class ChatController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://api.deepseek.com/v1/chat/completions';
    protected $formatoTurno = '/TURNO_CONFIRMADO:(\d{4}-\d{2}-\d{2} \d{2}:\d{2}):(.+)/';

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key', env('DEEPSEEK_API_KEY'));
        Log::info('API Key configurada: ' . ($this->apiKey ? 'Presente' : 'No presente'));
        // Establecer zona horaria para Guayaquil
        date_default_timezone_set('America/Guayaquil');
    }

    public function chat(Request $request)
    {
        try {
            $userMessage = $request->input('message');
            $chatId = $request->input('chat_id');
            $nombre = $request->input('nombre');
            $email = $request->input('email');

            if (empty($userMessage)) {
                return response()->json([
                    'error' => 'El mensaje es requerido'
                ], 400);
            }

            // Guardar mensaje del usuario
            ChatWeb::create([
                'chat_id' => $chatId,
                'mensaje' => $userMessage,
                'tipo' => 'usuario',
                'nombre' => $nombre,
                'email' => $email
            ]);

            // Obtener contexto base
            $hoyGuayaquil = Carbon::now('America/Guayaquil');
            $manana = $hoyGuayaquil->copy()->addDay()->format('Y-m-d');
            
            $contextBase = 'Eres un asistente comercial estratégico de Eteria. ' .
                         'HOY es ' . $hoyGuayaquil->format('Y-m-d') . ' en Quito. ' .
                         'IMPORTANTE: Tus respuestas deben ser cortas y en una sola línea, sin saltos de línea. Usa máximo 2 emojis por mensaje.';

            // Obtener historial de mensajes
            $historialMensajes = ChatWeb::where('chat_id', $chatId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Preparar mensajes para la API
            $messages = [
                [
                    'role' => 'system',
                    'content' => $contextBase
                ]
            ];

            foreach ($historialMensajes as $mensaje) {
                $messages[] = [
                    'role' => $mensaje->tipo === 'usuario' ? 'user' : 'assistant',
                    'content' => $mensaje->mensaje
                ];
            }

            Log::info('Intentando conexión con DeepSeek API');
            
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'deepseek-chat',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 150
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Respuesta exitosa de DeepSeek', ['response' => $responseData]);
                
                if (isset($responseData['choices'][0]['message']['content'])) {
                    $aiResponse = $responseData['choices'][0]['message']['content'];

                    // Guardar respuesta del bot
                    ChatWeb::create([
                        'chat_id' => $chatId,
                        'mensaje' => $aiResponse,
                        'tipo' => 'bot',
                        'nombre' => 'Asistente',
                        'email' => null
                    ]);

                    return response()->json([
                        'response' => $aiResponse
                    ]);
                }
            }

            throw new \Exception('Error en la respuesta de la API');

        } catch (\Exception $e) {
            Log::error('Error en el chat: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al procesar el mensaje'
            ], 500);
        }
    }

    public function getChatHistory(Request $request)
    {
        try {
            $chatId = $request->input('chat_id', 'chatweb1');
            
            $mensajes = ChatWeb::where('chat_id', $chatId)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'mensajes' => $mensajes
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener historial: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener el historial de chat'
            ], 500);
        }
    }

    /**
     * Procesa la confirmación de un turno
     */
    protected function procesarConfirmacionTurno($chatId, $fechaHora, $motivo)
    {
        try {
            // Convertir la fecha y hora a objeto Carbon con zona horaria de Guayaquil
            $fechaTurno = Carbon::parse($fechaHora)->setTimezone('America/Guayaquil');
            $ahora = Carbon::now('America/Guayaquil');
            $manana = $ahora->copy()->addDay()->startOfDay();

            // Validar que la fecha sea desde mañana en adelante
            if ($fechaTurno < $manana) {
                return "Lo siento, solo podemos agendar citas a partir de mañana " . $manana->format('d/m/Y') . ". Por favor, elige una fecha futura. Horario: L-V, 9:00-17:00 📅";
            }

            // Validar que la fecha no sea en el pasado
            if ($fechaTurno->isPast()) {
                return "Lo siento, la fecha seleccionada ya pasó. Por favor, elige una fecha futura. Horario: L-V, 9:00-17:00 📅";
            }

            // Validar que sea día laboral (Lunes a Viernes)
            if ($fechaTurno->isWeekend()) {
                return "Solo agendamos de lunes a viernes. ¿Te gustaría elegir otro día? Horario: 9:00-17:00 📅";
            }

            // Validar horario laboral (9:00 a 17:00)
            $hora = (int)$fechaTurno->format('H');
            $minutos = (int)$fechaTurno->format('i');
            if ($hora < 9 || ($hora == 17 && $minutos > 0) || $hora > 17) {
                return "Nuestro horario de atención es de 9:00 a 17:00. ¿Te gustaría elegir otra hora? 🕒";
            }

            // Verificar si ya tiene un turno pendiente
            $turnoExistente = Turno::where('user_id', $chatId)
                ->where('fecha_turno', '>=', $ahora)
                ->first();

            if ($turnoExistente) {
                return "Ya tienes una cita para el " . 
                      $turnoExistente->fecha_turno->format('d/m/Y H:i') . 
                      ". Contáctanos si necesitas modificarla 📅";
            }

            // Verificar si ya existe un turno en esa fecha y hora
            $turnoMismaFecha = Turno::where('fecha_turno', $fechaTurno)->first();

            if ($turnoMismaFecha) {
                // Sugerir el siguiente horario disponible
                $siguienteHorario = $this->encontrarSiguienteHorarioDisponible($fechaTurno);
                return "Ese horario ya está reservado. ¿Te gustaría agendar para el " . 
                      $siguienteHorario->format('d/m/Y H:i') . "? 📅";
            }

            // Crear el nuevo turno
            Turno::create([
                'user_id' => $chatId,
                'fecha_turno' => $fechaTurno,
                'motivo' => $motivo
            ]);

            return "¡Listo! 😊 Tu cita está confirmada para el " . 
                  $fechaTurno->format('d/m/Y') . " a las " . 
                  $fechaTurno->format('H:i') . ". Recibirás una llamada para conocer más sobre tu proyecto y presentarte a nuestro equipo. 🤝";

        } catch (\Exception $e) {
            Log::error('Error al procesar confirmación de turno: ' . $e->getMessage());
            return 'Lo siento, hubo un error al procesar el turno. Por favor, intenta nuevamente.';
        }
    }

    /**
     * Encuentra el siguiente horario disponible a partir de una fecha dada
     */
    protected function encontrarSiguienteHorarioDisponible(Carbon $fecha)
    {
        $horario = $fecha->copy();
        
        do {
            // Avanzar 1 hora
            $horario->addHour();
            
            // Si pasamos las 17:00, ir al siguiente día a las 9:00
            if ($horario->hour >= 17) {
                $horario->addDay()->setHour(9)->setMinute(0);
            }
            
            // Si es fin de semana, ir al siguiente lunes
            if ($horario->isWeekend()) {
                $horario->next(Carbon::MONDAY)->setHour(9)->setMinute(0);
            }
            
            // Verificar si el horario está disponible
            $turnoExistente = Turno::where('fecha_turno', $horario)->first();
            
        } while ($turnoExistente);
        
        return $horario;
    }

    public function generateNewId()
    {
        // Obtener el último ID de chat
        $lastChat = ChatWeb::orderBy('id', 'desc')->first();
        
        if ($lastChat) {
            // Extraer el número del último chat_id
            preg_match('/chatweb(\d+)/', $lastChat->chat_id, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Generar nuevo ID
        $newChatId = 'chatweb' . $newNumber;

        // Verificar que el ID sea único
        while (ChatWeb::where('chat_id', $newChatId)->exists()) {
            $newNumber++;
            $newChatId = 'chatweb' . $newNumber;
        }

        return response()->json(['chat_id' => $newChatId]);
    }

    public function getUserChat(Request $request)
    {
        try {
            $email = $request->input('email');
            $nombre = $request->input('nombre');

            if (empty($email) || empty($nombre)) {
                return response()->json([
                    'error' => 'El email y nombre son requeridos'
                ], 400);
            }

            $mensajes = ChatWeb::where('email', $email)
                ->where('nombre', $nombre)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'mensajes' => $mensajes
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener mensajes del usuario: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener los mensajes del chat'
            ], 500);
        }
    }

    private function generateUniqueId()
    {
        return uniqid('chat_', true);
    }
}
