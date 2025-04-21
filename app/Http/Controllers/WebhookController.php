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

            // Guardar el mensaje recibido
            Mensaje::create([
                'numero' => $fromNumber,
                'mensaje' => $receivedMessage,
                'estado' => 'entrada',
                'fecha' => now()
            ]);

            // Obtener el contexto combinado
            $contextos = Contexto::latest()->get();
            $contextoCombinado = $contextos->pluck('contexto')->join("\n") ?: 
                'Eres un asistente virtual de Eteria, una empresa de desarrollo web.';

            // Obtener respuesta de DeepSeek
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $contextoCombinado
                    ],
                    [
                        'role' => 'user',
                        'content' => $receivedMessage
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 150
            ]);

            if ($response->successful()) {
                $aiResponse = $response->json()['choices'][0]['message']['content'];

                // Guardar la respuesta enviada
                Mensaje::create([
                    'numero' => $fromNumber,
                    'mensaje' => $aiResponse,
                    'estado' => 'salida',
                    'fecha' => now()
                ]);

                // Crear respuesta TwiML
                $messagingResponse = new MessagingResponse();
                $messagingResponse->message($aiResponse);

                return response($messagingResponse, 200)
                    ->header('Content-Type', 'text/xml');
            } else {
                throw new \Exception('Error en la API de DeepSeek');
            }
        } catch (\Exception $e) {
            Log::error('Error en webhook de Twilio: ' . $e->getMessage());
            
            $messagingResponse = new MessagingResponse();
            $messagingResponse->message('Lo siento, hubo un error al procesar tu mensaje.');
            
            return response($messagingResponse, 200)
                ->header('Content-Type', 'text/xml');
        }
    }
} 