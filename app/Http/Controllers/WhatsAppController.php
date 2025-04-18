<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppController extends Controller
{
    private $twilio;
    private $fromNumber;

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
    }

    public function index()
    {
        return view('whatsapp.index', [
            'toNumber' => config('services.twilio.to_number')
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $toNumber = "whatsapp:" . config('services.twilio.to_number');
            return $this->sendMessage($toNumber, $request->message);
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

            $message = $this->twilio->messages->create(
                $toNumber,
                [
                    "from" => $this->fromNumber,
                    "body" => $messageBody
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