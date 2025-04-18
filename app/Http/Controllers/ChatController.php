<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Contexto;

class ChatController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://api.deepseek.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key', env('DEEPSEEK_API_KEY'));
        Log::info('API Key configurada: ' . ($this->apiKey ? 'Presente' : 'No presente'));
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

            // Obtener todos los contextos de la base de datos
            $contextos = Contexto::latest()->get();
            $contextoCombinado = '';

            // Combinar todos los contextos en uno solo
            foreach ($contextos as $contexto) {
                $contextoCombinado .= $contexto->contexto . "\n";
            }

            // Si no hay contextos, usar un contexto por defecto
            if (empty($contextoCombinado)) {
                $contextoCombinado = 'Eres un asistente virtual de Eteria, una empresa de desarrollo web.';
            }

            Log::info('Intentando conexión con DeepSeek API');
            
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $contextoCombinado
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
                Log::info('Respuesta exitosa de DeepSeek', ['response' => $responseData]);
                
                if (isset($responseData['choices'][0]['message']['content'])) {
                    return response()->json([
                        'response' => $responseData['choices'][0]['message']['content']
                    ]);
                } else {
                    Log::error('Respuesta de API inválida: ' . json_encode($responseData));
                    throw new \Exception('Formato de respuesta inválido');
                }
            } else {
                $errorBody = $response->body();
                Log::error('Error en la API', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                    'headers' => $response->headers()
                ]);
                return response()->json([
                    'response' => 'Error en la API: ' . $errorBody
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error en el chat: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'response' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
