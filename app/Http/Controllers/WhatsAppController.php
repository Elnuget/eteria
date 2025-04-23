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
            // Limpiar el número de teléfono de espacios y formatear
            $phoneNumber = '+593' . preg_replace('/\s+/', '', $request->phone_number);
            
            // Guardar o actualizar el contacto
            $contacto = \App\Models\Contacto::firstOrCreate(
                ['numero' => $phoneNumber],
                [
                    'nombre' => '',
                    'estado' => 'por iniciar'
                ]
            );

            // Crear el mensaje en la base de datos
            $mensaje = new \App\Models\Mensaje([
                'contacto_id' => $contacto->id,
                'mensaje' => 'Saludo inicial de bienvenida',
                'estado' => 'salida',
                'fecha' => now()
            ]);
            $mensaje->save();
            
            // Enviar el mensaje usando la plantilla
            $result = $this->sendTemplateMessage($phoneNumber);

            if ($result['success']) {
                // Actualizar el estado del contacto a 'iniciado' después de enviar el mensaje
                $contacto->estado = 'iniciado';
                $contacto->save();
                
                return redirect()->route('whatsapp.index')
                    ->with('success', 'Saludo enviado exitosamente a ' . $phoneNumber);
            } else {
                return redirect()->route('whatsapp.index')
                    ->with('error', 'Error al enviar el saludo: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('whatsapp.index')
                ->with('error', 'Error al enviar el saludo: ' . $e->getMessage());
        }
    }

    protected function sendTemplateMessage($to)
    {
        try {
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
} 