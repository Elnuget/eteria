<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\ChatWeb;
use App\Models\ContactoWeb;
use Illuminate\Support\Str;

class PruebaTecnicaFarmaController extends Controller
{
    /**
     * Mostrar la vista principal de la prueba técnica
     */
    public function index()
    {
        return view('prueba-tecnica-farma.index');
    }

    /**
     * Obtener datos de ventas farmacéuticas
     */
    public function getDatosVentas(Request $request): JsonResponse
    {
        try {
            $jsonPath = public_path('data/datos_ventas.json');
            
            if (!file_exists($jsonPath)) {
                return response()->json([
                    'error' => 'Archivo de datos no encontrado'
                ], 404);
            }

            $datos = json_decode(file_get_contents($jsonPath), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'Error al procesar los datos JSON'
                ], 500);
            }

            // Filtros opcionales
            $producto = $request->get('producto');
            $ciudad = $request->get('ciudad');
            $mes = $request->get('mes');
            $canal = $request->get('canal');

            $ventasFiltradas = $datos['ventas'];

            // Aplicar filtros si se proporcionan
            if ($producto) {
                $ventasFiltradas = array_filter($ventasFiltradas, function($venta) use ($producto) {
                    return stripos($venta['producto'], $producto) !== false;
                });
            }

            if ($ciudad) {
                $ventasFiltradas = array_filter($ventasFiltradas, function($venta) use ($ciudad) {
                    return stripos($venta['ciudad'], $ciudad) !== false;
                });
            }

            if ($mes) {
                $ventasFiltradas = array_filter($ventasFiltradas, function($venta) use ($mes) {
                    return date('Y-m', strtotime($venta['fecha'])) === $mes;
                });
            }

            if ($canal) {
                $ventasFiltradas = array_filter($ventasFiltradas, function($venta) use ($canal) {
                    return stripos($venta['canal_venta'], $canal) !== false;
                });
            }

            // Recalcular resumen con datos filtrados
            $resumenFiltrado = $this->calcularResumen(array_values($ventasFiltradas));

            return response()->json([
                'success' => true,
                'metadata' => $datos['metadata'],
                'ventas' => array_values($ventasFiltradas),
                'resumen' => $resumenFiltrado,
                'filtros_aplicados' => [
                    'producto' => $producto,
                    'ciudad' => $ciudad,
                    'mes' => $mes,
                    'canal' => $canal
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular resumen de ventas
     */
    private function calcularResumen(array $ventas): array
    {
        if (empty($ventas)) {
            return [
                'total_registros' => 0,
                'total_unidades_vendidas' => 0,
                'total_ingresos' => 0,
                'productos_unicos' => [],
                'ciudades_unicas' => [],
                'canales_venta' => []
            ];
        }

        $productos = array_unique(array_column($ventas, 'producto'));
        $ciudades = array_unique(array_column($ventas, 'ciudad'));
        $canales = array_unique(array_column($ventas, 'canal_venta'));
        
        $totalUnidades = array_sum(array_column($ventas, 'unidades_vendidas'));
        $totalIngresos = array_sum(array_column($ventas, 'total_venta'));

        return [
            'total_registros' => count($ventas),
            'total_unidades_vendidas' => $totalUnidades,
            'total_ingresos' => round($totalIngresos, 2),
            'productos_unicos' => array_values($productos),
            'ciudades_unicas' => array_values($ciudades),
            'canales_venta' => array_values($canales)
        ];
    }

    /**
     * Encuentra o crea un chat para la prueba técnica usando nombre, celular y email
     */
    public function findOrCreateChat(Request $request): JsonResponse
    {
        try {
            // Debug: Verificar qué datos están llegando
            Log::info('Datos recibidos en findOrCreateChat:', $request->all());
            
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'celular' => 'required|string|min:8',
                'email' => 'required|email',
            ]);

            $nombre = $validatedData['nombre'];
            $celular = $validatedData['celular'];
            $email = $validatedData['email'];

            Log::info('Datos validados correctamente:', [
                'nombre' => $nombre, 
                'celular' => $celular, 
                'email' => $email
            ]);

            // Buscar contacto existente por celular o email
            $contactoWeb = ContactoWeb::where('celular', $celular)
                ->orWhere('email', $email)
                ->first();

            if ($contactoWeb) {
                // Actualizar datos si es necesario
                $updated = false;
                if ($contactoWeb->nombre !== $nombre) {
                    $contactoWeb->nombre = $nombre;
                    $updated = true;
                }
                if ($contactoWeb->celular !== $celular) {
                    $contactoWeb->celular = $celular;
                    $updated = true;
                }
                if ($contactoWeb->email !== $email) {
                    $contactoWeb->email = $email;
                    $updated = true;
                }
                if ($updated) {
                    $contactoWeb->save();
                }
            } else {
                // Crear nuevo contacto
                $contactoWeb = ContactoWeb::create([
                    'nombre' => $nombre,
                    'celular' => $celular,
                    'email' => $email
                ]);
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
            return response()->json([
                'message' => 'Datos inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al buscar o crear chat: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener ID del chat'
            ], 500);
        }
    }

    /**
     * Obtiene el historial del chat
     */
    public function getChatHistory(Request $request): JsonResponse
    {
        try {
            $chatId = $request->input('chat_id');
            if (!$chatId) {
                return response()->json(['error' => 'chat_id es requerido'], 400);
            }
            
            $mensajes = ChatWeb::where('chat_id', $chatId)
                ->orderBy('created_at', 'asc')
                ->get();
            
            return response()->json([
                'mensajes' => $mensajes
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener historial del chat: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar historial'], 500);
        }
    }

    /**
     * Genera un ID único para el chat
     */
    private function generateNewUniqueId(): string
    {
        return 'chat_' . Str::uuid();
    }

    /**
     * Maneja los mensajes del chat para la prueba técnica
     */
    public function chat(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'message' => 'required|string',
                'chat_id' => 'required|string',
                'contacto_web_id' => 'required|exists:contacto_webs,id'
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

            // Generar respuesta automática de la prueba técnica
            $response = $this->generateTechnicalResponse($userMessage);

            // Guardar respuesta del bot
            ChatWeb::create([
                'chat_id' => $chatId,
                'contacto_web_id' => $contactoWebId,
                'mensaje' => $response,
                'tipo' => 'bot',
            ]);

            return response()->json([
                'response' => $response
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en chat: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Datos inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en chat: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al procesar mensaje'
            ], 500);
        }
    }

    /**
     * Genera respuestas automáticas para la prueba técnica farmacéutica
     */
    private function generateTechnicalResponse(string $userMessage): string
    {
        $message = strtolower($userMessage);
        
        // Respuestas predefinidas para la prueba técnica
        if (str_contains($message, 'hola') || str_contains($message, 'comenzar') || str_contains($message, 'empezar')) {
            return "¡Perfecto! Comencemos con la prueba técnica farmacéutica. 💊\n\n" .
                   "**Pregunta 1:** ¿Cuáles son los principales principios de las Buenas Prácticas de Fabricación (GMP) en la industria farmacéutica?";
        }
        
        if (str_contains($message, 'gmp') || str_contains($message, 'buenas prácticas') || str_contains($message, 'fabricación')) {
            return "Excelente conocimiento sobre GMP. 👍\n\n" .
                   "**Pregunta 2:** ¿Qué es la validación de procesos en la industria farmacéutica y por qué es importante?";
        }
        
        if (str_contains($message, 'validación') || str_contains($message, 'proceso')) {
            return "Muy bien. La validación es fundamental en farmacéutica. ✅\n\n" .
                   "**Pregunta 3:** Explica la diferencia entre medicamentos de marca y medicamentos genéricos.";
        }
        
        if (str_contains($message, 'genérico') || str_contains($message, 'marca') || str_contains($message, 'diferencia')) {
            return "Correcto. Entiendes bien los tipos de medicamentos. 💯\n\n" .
                   "**Pregunta 4:** ¿Qué es la farmacovigilancia y cuál es su importancia?";
        }
        
        if (str_contains($message, 'farmacovigilancia') || str_contains($message, 'seguridad') || str_contains($message, 'efectos')) {
            return "Excelente comprensión de farmacovigilancia. 🎯\n\n" .
                   "**Pregunta 5:** ¿Cuáles son los principales canales de distribución farmacéutica?";
        }
        
        if (str_contains($message, 'distribución') || str_contains($message, 'canal') || str_contains($message, 'farmacia')) {
            return "¡Felicitaciones! Has completado exitosamente la prueba técnica farmacéutica. 🎉\n\n" .
                   "Has demostrado tener conocimientos sólidos en:\n" .
                   "✅ Buenas Prácticas de Fabricación\n" .
                   "✅ Validación de procesos\n" .
                   "✅ Tipos de medicamentos\n" .
                   "✅ Farmacovigilancia\n" .
                   "✅ Canales de distribución\n\n" .
                   "Gracias por participar en esta evaluación técnica. 🤝";
        }
        
        // Respuesta por defecto
        return "Interesante respuesta. Para continuar con la evaluación, por favor responde la pregunta planteada o escribe 'comenzar' para iniciar la prueba técnica.";
    }
}
