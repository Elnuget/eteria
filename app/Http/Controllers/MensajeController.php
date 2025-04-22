<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MensajeController extends Controller
{
    /**
     * Mostrar una lista de los mensajes.
     */
    public function index()
    {
        $mensajesAgrupados = Mensaje::with('contacto')
            ->orderBy('fecha', 'desc')
            ->get()
            ->groupBy(function($mensaje) {
                return $mensaje->contacto->numero;
            });
            
        // Obtener los contactos únicos
        $contactos = Contacto::orderBy('nombre')
            ->get();

        return view('mensajes.index', compact('mensajesAgrupados', 'contactos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo mensaje.
     */
    public function create()
    {
        $contactos = Contacto::orderBy('nombre')->get();
        return view('mensajes.create', compact('contactos'));
    }

    /**
     * Almacenar un nuevo mensaje.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contacto_id' => 'required|exists:contactos,id',
            'mensaje' => 'required|string',
            'estado' => 'required|in:entrada,salida'
        ]);

        $mensaje = Mensaje::create([
            'contacto_id' => $request->contacto_id,
            'mensaje' => $request->mensaje,
            'estado' => $request->estado,
            'fecha' => now()
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->route('mensajes.index')->with('success', 'Mensaje registrado exitosamente');
    }

    /**
     * Mostrar un mensaje específico.
     */
    public function show(Mensaje $mensaje)
    {
        $mensaje->load('contacto');
        return view('mensajes.show', compact('mensaje'));
    }

    /**
     * Mostrar el formulario para editar un mensaje.
     */
    public function edit(Mensaje $mensaje)
    {
        $contactos = Contacto::orderBy('nombre')->get();
        return view('mensajes.edit', compact('mensaje', 'contactos'));
    }

    /**
     * Actualizar un mensaje específico.
     */
    public function update(Request $request, Mensaje $mensaje)
    {
        $request->validate([
            'contacto_id' => 'required|exists:contactos,id',
            'mensaje' => 'required|string',
            'estado' => 'required|in:entrada,salida'
        ]);

        $mensaje->update($request->only(['contacto_id', 'mensaje', 'estado']));

        return redirect()->route('mensajes.index')->with('success', 'Mensaje actualizado exitosamente');
    }

    /**
     * Eliminar un mensaje específico.
     */
    public function destroy(Mensaje $mensaje)
    {
        $mensaje->delete();
        return redirect()->route('mensajes.index')->with('success', 'Mensaje eliminado exitosamente');
    }

    public function eliminarConversacion($contactoId)
    {
        try {
            // Eliminar todos los mensajes del contacto
            Mensaje::where('contacto_id', $contactoId)->delete();
            
            return response()->json(['message' => 'Conversación eliminada con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la conversación'], 500);
        }
    }
} 