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
        $this->sandboxMode = config('services.twilio.sandbox_mode', true);
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
            'message' => 'required|string',
        ]);

        try {
            // Limpiar el número de teléfono de espacios y formatear
            $phoneNumber = '+593' . preg_replace('/\s+/', '', $request->phone_number);
            
            // Verificar si el número está permitido en modo sandbox
            if ($this->sandboxMode) {
                $cleanNumber = ltrim($phoneNumber, '+');
                if (!in_array($cleanNumber, $this->allowedNumbers)) {
                    return redirect()->route('whatsapp.index')
                        ->with('error', 'En modo sandbox, solo se puede enviar mensajes a números verificados. Por favor, verifica el número en tu cuenta de Twilio.');
                }
            }
            
            $result = $this->sendMessage($phoneNumber, $request->message);

            if ($result['success']) {
                return redirect()->route('whatsapp.index')
                    ->with('success', 'Mensaje enviado exitosamente a ' . $phoneNumber);
            } else {
                return redirect()->route('whatsapp.index')
                    ->with('error', 'Error al enviar el mensaje: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('whatsapp.index')
                ->with('error', 'Error al enviar el mensaje: ' . $e->getMessage());
        }
    }

    public function sendMessage($to, $messageBody)
    {
        try {
            // Asegurarse de que el número tenga el formato correcto para WhatsApp
            $toNumber = "whatsapp:" . ltrim($to, '+');

            // Usar una plantilla aprobada por WhatsApp
            $message = $this->twilio->messages->create(
                $toNumber,
                [
                    "from" => $this->fromNumber,
                    "body" => $messageBody,
                    // Usar una plantilla predefinida
                    "template" => [
                        "name" => "eteria_notification",
                        "language" => [
                            "code" => "es"
                        ],
                        "components" => [
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $messageBody
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

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
} 