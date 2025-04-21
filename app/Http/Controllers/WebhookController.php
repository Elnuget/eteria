<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Contexto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class WebhookController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://api.deepseek.com/v1/chat/completions';

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

            // Guardar el mensaje recibido
            Mensaje::create([
                'numero' => $cleanNumber,
                'mensaje' => $receivedMessage,
                'estado' => 'entrada',
                'fecha' => now()
            ]);

            // Obtener historial de mensajes para este número
            $historialMensajes = Mensaje::where('numero', $cleanNumber)
                ->orderBy('fecha', 'asc')
                ->get();

            // Preparar mensajes para la API
            $messages = [];
            
            // Obtener el contexto combinado
            $contextos = Contexto::latest()->get();
            $contextoCombinado = $contextos->pluck('contexto')->join("\n") ?: 
                'Eres un asistente virtual de Eteria, una empresa de desarrollo web. Brinda atención directa a los usuarios que te contactan. Si en algún momento necesitan hablar con un agente humano, pueden escribir al número de WhatsApp +593983468115.';

            // Agregar el contexto del sistema
            $messages[] = [
                'role' => 'system',
                'content' => $contextoCombinado
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
                    'numero' => $cleanNumber,
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
} 