<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Contexto;
use App\Models\Turno;
use App\Models\ChatWeb;
use App\Models\ContactoWeb;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Lee y procesa los datos de ventas del archivo CSV
     */
    private function getDatosVentas($mensaje = '')
    {
        try {
            $csvPath = public_path('data/datos_ventas.csv');
            
            if (!file_exists($csvPath)) {
                return "No se encontraron datos de ventas disponibles.";
            }

            $ventas = [];
            $handle = fopen($csvPath, 'r');
            
            if ($handle !== false) {
                // Leer la primera línea (cabeceras)
                $headers = fgetcsv($handle);
                
                // Leer todas las filas
                while (($data = fgetcsv($handle)) !== false) {
                    $ventas[] = array_combine($headers, $data);
                }
                fclose($handle);
            }

            // Procesar los datos según el contexto del mensaje
            $resumen = $this->procesarDatosVentas($ventas, $mensaje);
            
            return $resumen;

        } catch (\Exception $e) {
            Log::error('Error al leer datos de ventas: ' . $e->getMessage());
            return "Error al acceder a los datos de ventas.";
        }
    }

    /**
     * Procesa y resume los datos de ventas
     */
    private function procesarDatosVentas($ventas, $mensaje = '')
    {
        if (empty($ventas)) {
            return "No hay datos de ventas disponibles.";
        }

        // Obtener listas únicas
        $productos = array_unique(array_column($ventas, 'Producto'));
        $ciudades = array_unique(array_column($ventas, 'Ciudad'));
        $canales = array_unique(array_column($ventas, 'Canal de Venta'));
        
        // Calcular totales
        $totalUnidades = array_sum(array_column($ventas, 'Unidades Vendidas'));
        $totalVentas = 0;
        
        foreach ($ventas as $venta) {
            $totalVentas += $venta['Unidades Vendidas'] * $venta['Precio Unitario (USD)'];
        }

        // Crear resumen contextual
        $resumen = "DATOS DISPONIBLES DE VENTAS:\n";
        $resumen .= "📊 Total registros: " . count($ventas) . "\n";
        $resumen .= "📦 Total unidades vendidas: " . number_format($totalUnidades) . "\n";
        $resumen .= "💰 Total ingresos: $" . number_format($totalVentas, 2) . "\n";
        $resumen .= "🏛️ Productos: " . implode(', ', $productos) . "\n";
        $resumen .= "🌍 Ciudades: " . implode(', ', $ciudades) . "\n";
        $resumen .= "🏪 Canales: " . implode(', ', $canales) . "\n";

        // Si el mensaje contiene palabras clave específicas, dar información más detallada
        $mensaje_lower = strtolower($mensaje);
        
        if (strpos($mensaje_lower, 'producto') !== false || strpos($mensaje_lower, 'medicamento') !== false) {
            $resumen .= "\n📋 DETALLE POR PRODUCTOS:\n";
            foreach ($productos as $producto) {
                $ventasProducto = array_filter($ventas, function($v) use ($producto) {
                    return $v['Producto'] === $producto;
                });
                $unidadesProducto = array_sum(array_column($ventasProducto, 'Unidades Vendidas'));
                $resumen .= "- {$producto}: " . number_format($unidadesProducto) . " unidades\n";
            }
        }

        if (strpos($mensaje_lower, 'ciudad') !== false || strpos($mensaje_lower, 'región') !== false) {
            $resumen .= "\n🏙️ DETALLE POR CIUDADES:\n";
            foreach ($ciudades as $ciudad) {
                $ventasCiudad = array_filter($ventas, function($v) use ($ciudad) {
                    return $v['Ciudad'] === $ciudad;
                });
                $unidadesCiudad = array_sum(array_column($ventasCiudad, 'Unidades Vendidas'));
                $resumen .= "- {$ciudad}: " . number_format($unidadesCiudad) . " unidades\n";
            }
        }

        return $resumen;
    }

    /**
     * Handles user messages, interacts with AI, and stores conversation.
     */
    public function chat(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'message' => 'required|string',
                'chat_id' => 'required|string',
                'contacto_web_id' => 'required|exists:contacto_webs,id' // Validar que el contacto exista
            ]);

            $userMessage = $validatedData['message'];
            $chatId = $validatedData['chat_id'];
            $contactoWebId = $validatedData['contacto_web_id'];

            // Guardar mensaje del usuario
            ChatWeb::create([
                'chat_id' => $chatId,
                'contacto_web_id' => $contactoWebId,
                'mensaje' => $userMessage,
                'tipo' => 'usuario',
            ]);

            // Obtener contexto base (adaptado de WebhookController)
            $hoyGuayaquil = Carbon::now('America/Guayaquil');
            $manana = $hoyGuayaquil->copy()->addDay()->format('Y-m-d');
            
            // Obtener datos de ventas para incluir en el contexto
            $datosVentas = $this->getDatosVentas($userMessage);
            
            $contextBase = 'Eres un asistente comercial estratégico de Eteria especializado en análisis de datos de ventas farmacéuticas. ' .
                         'HOY es ' . $hoyGuayaquil->format('Y-m-d') . '. ' .
                         'IMPORTANTE: Tus respuestas deben ser cortas y en una sola línea. Usa máximo 2 emojis por mensaje. ' .
                         
                         'DATOS DE VENTAS DISPONIBLES: ' . $datosVentas . ' ' .
                         
                         'FUNCIÓN PRINCIPAL: Puedes responder preguntas sobre productos farmacéuticos, ciudades, ventas y análisis de datos. ' .
                         'Si preguntan sobre productos específicos, ciudades, unidades vendidas o canales de venta, usa la información de los datos anteriores. ' .
                         
                         'FLUJO DE CONVERSACIÓN: ' .
                         '1) Si preguntan sobre datos de ventas, productos o ciudades: Responde con información específica de los datos disponibles, ' .
                         '2) Si no es sobre datos: Entiende el negocio y sus desafíos actuales, ' .
                         '3) Luego, identifica una oportunidad de mejora y presenta una propuesta de valor específica para su caso, ' .
                         '4) Si muestra interés, sugiere agendar una reunión virtual para presentar una solución detallada. ' .
                         
                         'Para agendar reuniones virtuales (turnos): Solo L-V desde ' . $manana . ', hora: 9:00-17:00. ' .
                         'Cuando tengas fecha y hora confirmadas por el usuario, usa internamente el formato: TURNO_CONFIRMADO:YYYY-MM-DD HH:mm:MOTIVO para registrarlo. '. // Aclaración para el LLM
                         'Al usuario confirma la cita de forma amigable sin mostrar el formato interno. Ejemplo: "¡Perfecto! Tu reunión está agendada para el..." ' .
                         
                         'EJEMPLOS DE RESPUESTAS SOBRE DATOS: ' .
                         'Si preguntan por productos: "Tenemos DolorFree 500mg y VitaBoost C1000. DolorFree lidera en Quito y Guayaquil 💊 ¿Te interesa alguno en particular?" ' .
                         'Si preguntan por ciudades: "Operamos en Quito, Guayaquil y Cuenca. Quito tiene las mejores ventas 🏙️ ¿Qué ciudad te interesa?" ' .
                         'Si preguntan por ventas: "Hemos vendido [X] unidades por $[Y]. Canal farmacia domina 📈 ¿Quieres análisis detallado?" ' .
                         
                         'EJEMPLOS DE PROPUESTAS COMERCIALES: ' .
                         'Si mencionan ventas: "Con nuestra solución podrías aumentar tus ventas un 30% automatizando seguimiento de clientes 💡 ¿Te gustaría conocer cómo en una breve reunión?" ' .
                         'Si mencionan tiempo: "Podríamos ahorrarte 15 horas semanales automatizando esos procesos ⚡ ¿Te interesa ver cómo en una reunión virtual?" ' .
                         
                         'RECUERDA: Mensajes cortos, máximo 2 emojis, prioriza responder con datos específicos cuando pregunten sobre productos/ciudades/ventas, sino enfócate en beneficios comerciales y conseguir la reunión.';
            
            // Verificar si el contacto ya tiene un turno pendiente
            $turnoExistente = Turno::where('contacto_web_id', $contactoWebId)
                ->where('fecha_turno', '>=', $hoyGuayaquil)
                ->first();

            // Agregar información sobre turno existente si lo hay
            if ($turnoExistente) {
                 // Recuperar el nombre del contacto para un mensaje más personalizado
                $contacto = ContactoWeb::find($contactoWebId);
                $nombreContacto = $contacto ? $contacto->nombre : 'tú';
                $contextBase .= ' IMPORTANTE: Este contacto ('.$nombreContacto.') ya tiene una reunión agendada para el ' . 
                              $turnoExistente->fecha_turno->format('d/m/Y H:i') . 
                              '. Motivo: ' . $turnoExistente->motivo . 
                              '. Infórmale amablemente que ya tiene una cita y que contactaremos pronto, no intentes agendar otra.';
            }
            

            // Obtener historial de mensajes para este chat_id
            $historialMensajes = ChatWeb::where('chat_id', $chatId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Preparar mensajes para la API
            $messages = [
                ['role' => 'system', 'content' => $contextBase]
            ];
            foreach ($historialMensajes as $mensaje) {
                $messages[] = [
                    'role' => $mensaje->tipo === 'usuario' ? 'user' : 'assistant',
                    'content' => $mensaje->mensaje
                ];
            }

            Log::info('Intentando conexión con DeepSeek API para chat_id: ' . $chatId);
            
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

                    // Procesar turno si aplica
                    $turnoProcesadoResultado = null;
                    if (preg_match($this->formatoTurno, $aiResponse, $matches)) {
                       $turnoProcesadoResultado = $this->procesarConfirmacionTurno($chatId, $contactoWebId, $matches[1], $matches[2]);
                       // Si la validación del turno falló, sobrescribir la respuesta del bot con el mensaje de error
                       if (is_string($turnoProcesadoResultado) && !empty($turnoProcesadoResultado)) {
                           $aiResponse = $turnoProcesadoResultado;
                       }
                    }

                    // Guardar respuesta del bot (puede ser la original o el mensaje de error del turno)
                    ChatWeb::create([
                        'chat_id' => $chatId,
                        'contacto_web_id' => $contactoWebId, // Usar el mismo ID de contacto
                        'mensaje' => $aiResponse,
                        'tipo' => 'bot',
                    ]);

                    return response()->json(['response' => $aiResponse]);
                }
            } 
            
            // Si la respuesta no fue exitosa o no tuvo contenido
            Log::error('Error en la respuesta de la API de DeepSeek', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Error en la respuesta de la API: ' . $response->status());

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en chat: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json(['error' => 'Datos inválidos.', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error general en el chat: ' . $e->getMessage());
            return response()->json(['error' => 'Error al procesar el mensaje'], 500);
        }
    }

    /**
     * Finds an existing ContactoWeb or creates a new one, 
     * then finds the latest chat_id or creates a new one for that contact.
     */
    public function findOrCreateChat(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'celular' => 'required|string|regex:/^[0-9+ ]{9,}$/', // Cambiado de email a celular, ajustar regex según necesidad
                'nombre' => 'required|string|max:255',
            ]);

            $celular = preg_replace('/\s+/', '', $validatedData['celular']); // Limpiar espacios si es necesario
            $nombre = $validatedData['nombre'];

            // Buscar o crear el contacto web por celular
            $contactoWeb = ContactoWeb::firstOrCreate(
                ['celular' => $celular], // Criterio de búsqueda por celular
                ['nombre' => $nombre]  // Datos para crear si no existe
            );

            // Si existe y el nombre es diferente, actualizarlo (opcional)
            if ($contactoWeb->wasRecentlyCreated === false && $contactoWeb->nombre !== $nombre) {
                $contactoWeb->nombre = $nombre;
                $contactoWeb->save();
            }

            // Buscar el chat_id más reciente para este contacto
            $lastChatMessage = ChatWeb::where('contacto_web_id', $contactoWeb->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $chatId = $lastChatMessage ? $lastChatMessage->chat_id : $this->generateNewUniqueId();

            return response()->json([
                'chat_id' => $chatId,
                'contacto_web_id' => $contactoWeb->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en findOrCreateChat: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json(['error' => 'Datos inválidos.', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al buscar o crear chat: ' . $e->getMessage());
            return response()->json(['error' => 'Error al iniciar el chat'], 500);
        }
    }

    /**
     * Gets chat history for a specific chat_id.
     */
    public function getChatHistory(Request $request)
    {
        try {
            $chatId = $request->input('chat_id');
            if (!$chatId) {
                return response()->json(['error' => 'chat_id es requerido'], 400);
            }
            
            $mensajes = ChatWeb::where('chat_id', $chatId)
                ->orderBy('created_at', 'asc') // Mantener ascendente para mostrar en orden cronológico
                ->get();

            return response()->json(['mensajes' => $mensajes]);
        } catch (\Exception $e) {
            Log::error('Error al obtener historial: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener el historial de chat'], 500);
        }
    }

    /**
     * Handles admin replies.
     */
/*
    public function adminReply(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'message' => 'required|string',
                'chat_id' => 'required|string|exists:chat_webs,chat_id', // Asegurar que el chat exista
            ]);

            // Encontrar el contacto_web_id asociado a este chat_id
            // Asumimos que todos los mensajes de un chat_id pertenecen al mismo contacto
            $firstMessage = ChatWeb::where('chat_id', $validatedData['chat_id'])->first();
            if (!$firstMessage) {
                 return response()->json(['success' => false, 'message' => 'Chat ID no encontrado.'], 404);
            }
            $contactoWebId = $firstMessage->contacto_web_id;

            // Guardar mensaje del administrador
            ChatWeb::create([
                'chat_id' => $validatedData['chat_id'],
                'contacto_web_id' => $contactoWebId,
                'mensaje' => $validatedData['message'],
                'tipo' => 'admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado correctamente'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en adminReply: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Datos inválidos.', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al enviar mensaje de administrador: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al enviar el mensaje'], 500);
        }
    }
*/
    // --- Métodos auxiliares (procesarConfirmacionTurno, encontrarSiguienteHorarioDisponible, generateNewUniqueId) ---

    protected function procesarConfirmacionTurno($chatId, $contactoWebId, $fechaHora, $motivo)
    {
        try {
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
            $turnoExistente = Turno::where('contacto_web_id', $contactoWebId) // Cambiado de user_id a contacto_web_id
                ->where('fecha_turno', '>=', $ahora)
                ->first();

            if ($turnoExistente) {
                // Recuperar el nombre del contacto para un mensaje más personalizado
                $contacto = ContactoWeb::find($contactoWebId);
                $nombreContacto = $contacto ? $contacto->nombre : 'tú';
                return "¡Hola {$nombreContacto}! Ya tienes una cita para el " . 
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
                // 'user_id' => $chatId, // Eliminado user_id
                'contacto_web_id' => $contactoWebId,
                'fecha_turno' => $fechaTurno,
                'motivo' => $motivo
            ]);
            
            Log::info("Turno creado para chatId: {$chatId}, contactoId: {$contactoWebId}");
            // No retornar mensaje aquí, la respuesta original del bot ya es la confirmación.
            // Devolver null indica éxito y que se debe usar la respuesta original del bot.
            return null; 

        } catch (\Exception $e) {
            Log::error('Error al procesar confirmación de turno para contacto web: ' . $e->getMessage());
            // Devolver un mensaje de error genérico si algo sale mal
            return 'Lo siento, hubo un problema interno al intentar agendar tu cita. Por favor, intenta de nuevo más tarde.';
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

    private function generateNewUniqueId()
    {
        // Obtener el último ID de chat numérico
        $lastChat = ChatWeb::where('chat_id', 'like', 'chatweb%')
                           ->orderByRaw('CAST(SUBSTRING(chat_id, 8) AS UNSIGNED) DESC')
                           ->first();

        $newNumber = 1;
        if ($lastChat) {
            preg_match('/chatweb(\d+)/i', $lastChat->chat_id, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $newNumber = $lastNumber + 1;
        }

        // Generar nuevo ID y verificar unicidad
        do {
            $newChatId = 'chatweb' . $newNumber;
            $exists = ChatWeb::where('chat_id', $newChatId)->exists();
            if ($exists) {
                $newNumber++;
            }
        } while ($exists);

        return $newChatId;
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
}
