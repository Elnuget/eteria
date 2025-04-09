<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://api.deepseek.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = 'sk-ff89790bd6a6420e83784e9649e89f21';
    }

    public function chat(Request $request)
    {
        try {
            $userMessage = $request->input('message');

            if (empty($userMessage)) {
                return response()->json([
                    'response' => 'Por favor, ingresa un mensaje.'
                ], 400);
            }

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un asistente virtual de Eteria, una empresa de desarrollo web. Proporciona respuestas útiles y profesionales relacionadas con desarrollo web y servicios de la empresa.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 150
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['choices'][0]['message']['content'])) {
                    return response()->json([
                        'response' => $responseData['choices'][0]['message']['content']
                    ]);
                } else {
                    Log::error('Respuesta de API inválida: ' . json_encode($responseData));
                    throw new \Exception('Formato de respuesta inválido');
                }
            } else {
                Log::error('Error en la API: ' . $response->body());
                return response()->json([
                    'response' => 'Lo siento, ha ocurrido un error al procesar tu mensaje. Código: ' . $response->status()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error en el chat: ' . $e->getMessage());
            return response()->json([
                'response' => 'Lo siento, ha ocurrido un error en el servicio. Por favor, intenta de nuevo más tarde.'
            ], 500);
        }
    }
}
