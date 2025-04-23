<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppController extends Controller
{
    private $twilio;
    private $fromNumber;
    private $sandboxMode;
    private $allowedNumbers;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['sendMessage']]);
        
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        
        if (empty($sid) || empty($token)) {
            throw new \RuntimeException('Las credenciales de Twilio no están configuradas correctamente.');
        }
        
        $this->twilio = new Client($sid, $token);
        $this->fromNumber = "whatsapp:" . config('services.twilio.from_number');
        $this->sandboxMode = false;
        $this->allowedNumbers = explode(',', config('services.twilio.allowed_numbers', ''));
    }

    public function index()
    {
        $data = [
            'sandboxMode' => $this->sandboxMode,
            'allowedNumbers' => $this->allowedNumbers
        ];
        return view('whatsapp.index', $data);
    }

    public function send(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|regex:/^[0-9 ]{9,}$/',
        ]);

        try {
            // Limpiar el número de teléfono de espacios
            $cleanNumber = preg_replace('/\s+/', '', $request->phone_number);
            
            // Número para guardar en la base de datos (sin +)
            $dbNumber = '593' . $cleanNumber;
            
            // Número para enviar por WhatsApp (con +)
            $whatsappNumber = '+' . $dbNumber;
            
            // Guardar o actualizar el contacto
            $contacto = \App\Models\Contacto::firstOrCreate(
                ['numero' => $dbNumber],
                [
                    'nombre' => '',
                    'estado' => 'por iniciar'
                ]
            );

            // Enviar el mensaje usando la plantilla
            $result = $this->sendTemplateMessage($whatsappNumber);

            if ($result['success']) {
                // Deshabilitar eventos temporalmente
                \App\Models\Mensaje::withoutEvents(function () use ($contacto) {
                    // Crear el mensaje en la base de datos
                    $mensaje = new \App\Models\Mensaje();
                    $mensaje->contacto_id = $contacto->id;
                    $mensaje->mensaje = 'Cordial saludo, 👋 Tenemos algo que podría ser de su interés si busca optimizar la comunicación con sus clientes por WhatsApp y potenciar sus ventas con plataformas inteligentes. ¿Le gustaría saber más o conoce a alguien a quien le pueda interesar?';
                    $mensaje->estado = 'salida';
                    $mensaje->fecha = now();
                    $mensaje->save();
                });
                
                // Actualizar el estado del contacto a 'iniciado'
                $contacto->estado = 'iniciado';
                $contacto->save();
                
                return redirect()->route('whatsapp.index')
                    ->with('success', 'Saludo enviado exitosamente a ' . $dbNumber);
            } else {
                return redirect()->route('whatsapp.index')
                    ->with('error', 'Error al enviar el saludo: ' . $result['message']);
            }
        } catch (\Exception $e) {
            \Log::error('Error en WhatsAppController::send: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('whatsapp.index')
                ->with('error', 'Error al enviar el saludo: ' . $e->getMessage());
        }
    }

    protected function sendTemplateMessage($to)
    {
        try {
            // Asegurarse de que el número tenga el formato correcto para WhatsApp
            $toNumber = "whatsapp:" . ltrim($to, '+');

            $message = $this->twilio->messages->create($toNumber, [
                "from" => $this->fromNumber,
                "contentSid" => "HX16a66d9718a7a24bfde5217b70b28d8a",
                "contentVariables" => json_encode([
                    "1" => "Bienvenido a Eteria"
                ])
            ]);

            return [
                'success' => true,
                'message' => 'Mensaje enviado exitosamente',
                'sid' => $message->sid
            ];
        } catch (\Exception $e) {
            \Log::error('Error al enviar mensaje de WhatsApp: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendMessage($to, $message)
    {
        try {
            // Asegurarse de que el número tenga el formato correcto para WhatsApp
            if (!str_starts_with($to, '+')) {
                $to = '+' . $to;
            }
            $toNumber = "whatsapp:" . $to;

            $message = $this->twilio->messages->create($toNumber, [
                "from" => $this->fromNumber,
                "body" => $message
            ]);

            return [
                'success' => true,
                'message' => 'Mensaje enviado exitosamente',
                'sid' => $message->sid
            ];
        } catch (\Exception $e) {
            \Log::error('Error al enviar mensaje de WhatsApp: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'numbers' => 'required|array',
            'numbers.*' => 'required|string|regex:/^[0-9]{12}$/'
        ]);

        $results = [
            'success' => true,
            'total' => count($request->numbers),
            'enviados' => 0,
            'errores' => []
        ];

        foreach ($request->numbers as $number) {
            try {
                // Número para WhatsApp (con +)
                $whatsappNumber = '+' . $number;
                
                // Obtener el contacto
                $contacto = \App\Models\Contacto::where('numero', $number)->first();
                
                if (!$contacto) {
                    $results['errores'][] = "No se encontró el contacto con número: {$number}";
                    continue;
                }

                // Enviar el mensaje usando la plantilla
                $sendResult = $this->sendTemplateMessage($whatsappNumber);

                if ($sendResult['success']) {
                    // Deshabilitar eventos temporalmente
                    \App\Models\Mensaje::withoutEvents(function () use ($contacto) {
                        // Crear el mensaje en la base de datos
                        $mensaje = new \App\Models\Mensaje();
                        $mensaje->contacto_id = $contacto->id;
                        $mensaje->mensaje = 'Cordial saludo, 👋 Tenemos algo que podría ser de su interés si busca optimizar la comunicación con sus clientes por WhatsApp y potenciar sus ventas con plataformas inteligentes. ¿Le gustaría saber más o conoce a alguien a quien le pueda interesar?';
                        $mensaje->estado = 'salida';
                        $mensaje->fecha = now();
                        $mensaje->save();
                    });
                    
                    // Actualizar el estado del contacto a 'iniciado'
                    $contacto->estado = 'iniciado';
                    $contacto->save();
                    
                    $results['enviados']++;
                } else {
                    $results['errores'][] = "Error al enviar a {$number}: {$sendResult['message']}";
                }
            } catch (\Exception $e) {
                \Log::error("Error al enviar saludo a {$number}: " . $e->getMessage());
                $results['errores'][] = "Error al enviar a {$number}: " . $e->getMessage();
            }
        }

        $results['success'] = $results['enviados'] > 0;
        
        if (!empty($results['errores'])) {
            $results['message'] = "Se enviaron {$results['enviados']} de {$results['total']} mensajes. Hubo algunos errores.";
        } else {
            $results['message'] = "Se enviaron todos los mensajes exitosamente ({$results['enviados']}).";
        }

        return response()->json($results);
    }
} 