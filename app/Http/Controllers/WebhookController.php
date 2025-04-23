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
        // Establecer zona horaria para Guayaquil
        date_default_timezone_set('America/Guayaquil');
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
                
                // Extraer nombre si se presenta
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

                // Analizar el mensaje para personalizar la respuesta
                $nombreSaludo = $contacto->nombre ? " {$contacto->nombre}" : "";
                $mensajeBienvenida = "";

                // Patrones comunes en mensajes iniciales
                $patrones = [
                    'cotización|cotizar|precio|costo' => "¡Hola{$nombreSaludo}! 😊 Me alegra que quieras conocer nuestras soluciones. En Eteria creamos: 📱 apps, 🛍️ ecommerce y 🤖 sistemas a medida. ¿Nos cuentas más sobre el proyecto que tienes en mente? 💡",
                    'página|pagina|web|sitio|website' => "¡Hola{$nombreSaludo}! 😊 ¡Genial que estés pensando en una web! Desarrollamos sitios que destacan y convierten. ¿Qué tipo de web necesitas: informativa, tienda online o sistema personalizado? 🎯",
                    'app|aplicación|aplicacion|móvil|movil' => "¡Hola{$nombreSaludo}! 😊 ¡Excelente decisión apostar por una app! Creamos aplicaciones móviles y web que transforman negocios. ¿Nos cuentas qué funcionalidades necesitas? 📱",
                    'sistema|software|programa|automatización|automatizacion' => "¡Hola{$nombreSaludo}! 😊 ¡Perfecto! Nos especializamos en crear sistemas que automatizan y optimizan procesos. ¿Qué procesos de tu negocio quieres mejorar? 🚀",
                    'ecommerce|tienda|online|ventas' => "¡Hola{$nombreSaludo}! 😊 ¡Genial que quieras vender online! Creamos tiendas virtuales que impulsan las ventas. ¿Ya tienes un catálogo de productos definido? 🛍️"
                ];

                $mensajeEncontrado = false;
                foreach ($patrones as $patron => $respuesta) {
                    if (preg_match("/$patron/i", $receivedMessage)) {
                        $mensajeBienvenida = $respuesta;
                        $mensajeEncontrado = true;
                        break;
                    }
                }

                // Mensaje por defecto si no se detecta un patrón específico
                if (!$mensajeEncontrado) {
                    $mensajeBienvenida = "¡Hola{$nombreSaludo}! 😊 Soy el asistente virtual de Eteria. Creamos soluciones digitales: 📱 apps, 🛍️ ecommerce y 🤖 sistemas a medida. ¿Nos cuentas qué tipo de proyecto tienes en mente? 💡";
                }

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
            $hoyGuayaquil = Carbon::now('America/Guayaquil');
            $manana = $hoyGuayaquil->copy()->addDay()->format('Y-m-d');
            
            $contextBase = 'Eres un asistente virtual de Eteria. ' .
                         'HOY es ' . $hoyGuayaquil->format('Y-m-d') . ' en Guayaquil. ' .
                         'SOLO puedes agendar citas a partir de ' . $manana . '. ' .
                         'Guía la conversación para obtener la siguiente información: ' .
                         '1) Tipo de proyecto/servicio que necesitan, ' .
                         '2) Fecha preferida (días laborables L-V, desde mañana en adelante), ' .
                         '3) Hora preferida (9:00 a 17:00), ' .
                         '4) Breve descripción del proyecto. ' .
                         'Solo cuando tengas TODA esta información, responde con el formato: ' .
                         'TURNO_CONFIRMADO:YYYY-MM-DD HH:mm:MOTIVO. ' .
                         'Si falta información, continúa preguntando amablemente. ' .
                         'Si intentan agendar para hoy, indícales amablemente que solo podemos agendar desde mañana. ' .
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
            // Convertir la fecha y hora a objeto Carbon con zona horaria de Guayaquil
            $fechaTurno = Carbon::parse($fechaHora)->setTimezone('America/Guayaquil');
            $ahora = Carbon::now('America/Guayaquil');
            $manana = $ahora->copy()->addDay()->startOfDay();

            // Validar que la fecha sea desde mañana en adelante
            if ($fechaTurno < $manana) {
                return (new MessagingResponse())
                    ->message("Lo siento, solo podemos agendar citas a partir de mañana " . $manana->format('d/m/Y') . ". Por favor, elige una fecha futura. Horario: L-V, 9:00-17:00 📅")
                    ->__toString();
            }

            // Validar que la fecha no sea en el pasado
            if ($fechaTurno->isPast()) {
                return (new MessagingResponse())
                    ->message("Lo siento, la fecha seleccionada ya pasó. Por favor, elige una fecha futura. Horario: L-V, 9:00-17:00 📅")
                    ->__toString();
            }

            // Validar que sea día laboral (Lunes a Viernes)
            if ($fechaTurno->isWeekend()) {
                return (new MessagingResponse())
                    ->message("Solo agendamos de lunes a viernes. ¿Te gustaría elegir otro día? Horario: 9:00-17:00 📅")
                    ->__toString();
            }

            // Validar horario laboral (9:00 a 17:00)
            $hora = (int)$fechaTurno->format('H');
            $minutos = (int)$fechaTurno->format('i');
            if ($hora < 9 || ($hora == 17 && $minutos > 0) || $hora > 17) {
                return (new MessagingResponse())
                    ->message("Nuestro horario de atención es de 9:00 a 17:00. ¿Te gustaría elegir otra hora? 🕒")
                    ->__toString();
            }

            // Verificar si ya tiene un turno pendiente
            $turnoExistente = Turno::where('contacto_id', $contacto->id)
                ->where('fecha_turno', '>=', $ahora)
                ->first();

            if ($turnoExistente) {
                $saludo = $contacto->nombre ? "{$contacto->nombre}" : "Estimado/a";
                $mensaje = "¡Hola {$saludo}! Ya tienes una cita para el " . 
                          $turnoExistente->fecha_turno->format('d/m/Y H:i') . 
                          ". Contáctanos si necesitas modificarla 📅";
            } else {
                // Verificar si ya existe un turno en esa fecha y hora
                $turnoMismaFecha = Turno::where('fecha_turno', $fechaTurno)->first();

                if ($turnoMismaFecha) {
                    // Sugerir el siguiente horario disponible
                    $siguienteHorario = $this->encontrarSiguienteHorarioDisponible($fechaTurno);
                    $mensaje = "Ese horario ya está reservado. ¿Te gustaría agendar para el " . 
                              $siguienteHorario->format('d/m/Y H:i') . "? 📅";
                } else {
                    // Crear el nuevo turno
                    Turno::create([
                        'contacto_id' => $contacto->id,
                        'fecha_turno' => $fechaTurno,
                        'motivo' => $motivo
                    ]);

                    $saludo = $contacto->nombre ? "{$contacto->nombre}" : "Estimado/a";
                    $mensaje = "¡Listo {$saludo}! 😊 Tu cita está confirmada para el " . 
                              $fechaTurno->format('d/m/Y') . " a las " . 
                              $fechaTurno->format('H:i') . ". Recibirás una llamada para conocer más sobre tu proyecto y presentarte a nuestro equipo. 🤝";
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
                ->message('Lo siento, hubo un error al procesar el turno. Por favor, intenta nuevamente.')
                ->__toString();
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
} 