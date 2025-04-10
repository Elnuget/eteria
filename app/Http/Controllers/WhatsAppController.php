<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppController extends Controller
{
    private $twilio;
    private $fromNumber;
    private $toNumber;

    public function __construct()
    {
        $this->middleware('auth');
        
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        
        if (empty($sid) || empty($token)) {
            throw new \RuntimeException('Las credenciales de Twilio no están configuradas correctamente.');
        }
        
        $this->twilio = new Client($sid, $token);
        $this->fromNumber = "whatsapp:" . config('services.twilio.from_number');
        $this->toNumber = "whatsapp:" . config('services.twilio.to_number');
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
            $message = $this->twilio->messages
                ->create($this->toNumber,
                    array(
                        "from" => $this->fromNumber,
                        "body" => $request->message
                    )
                );

            return redirect()->route('whatsapp.index')
                ->with('success', 'Mensaje enviado exitosamente!');
        } catch (\Exception $e) {
            return redirect()->route('whatsapp.index')
                ->with('error', 'Error al enviar el mensaje: ' . $e->getMessage());
        }
    }
} 