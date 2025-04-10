<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class WhatsAppController extends Controller
{
    private $twilio;
    private $fromNumber = "whatsapp:+14155238886";

    public function __construct()
    {
        $this->middleware('auth');
        
        $sid = "ACe55f4b5247caa78f85d09ebfb904a951";
        $token = "851e00940270135db865dbd5c6d986aa";
        $this->twilio = new Client($sid, $token);
    }

    public function index()
    {
        return view('whatsapp.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $message = $this->twilio->messages
                ->create("whatsapp:" . $request->phone,
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