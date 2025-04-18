<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    /**
     * Mostrar una lista de los mensajes.
     */
    public function index()
    {
        $mensajes = Mensaje::orderBy('fecha', 'desc')->get();
        return view('mensajes.index', compact('mensajes'));
    }

    /**
     * Mostrar el formulario para crear un nuevo mensaje.
     */
    public function create()
    {
        return view('mensajes.create');
    }

    /**
     * Almacenar un nuevo mensaje.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string',
            'nombre' => 'nullable|string|max:255',
            'mensaje' => 'required|string',
            'estado' => 'required|in:entrada,salida'
        ]);

        $mensaje = Mensaje::create([
            'numero' => $request->numero,
            'nombre' => $request->nombre,
            'mensaje' => $request->mensaje,
            'estado' => $request->estado,
            'fecha' => now()
        ]);

        return redirect()->route('mensajes.index')->with('success', 'Mensaje registrado exitosamente');
    }

    /**
     * Mostrar un mensaje específico.
     */
    public function show(Mensaje $mensaje)
    {
        return view('mensajes.show', compact('mensaje'));
    }

    /**
     * Mostrar el formulario para editar un mensaje.
     */
    public function edit(Mensaje $mensaje)
    {
        return view('mensajes.edit', compact('mensaje'));
    }

    /**
     * Actualizar un mensaje específico.
     */
    public function update(Request $request, Mensaje $mensaje)
    {
        $request->validate([
            'numero' => 'required|string',
            'nombre' => 'nullable|string|max:255',
            'mensaje' => 'required|string',
            'estado' => 'required|in:entrada,salida'
        ]);

        $mensaje->update($request->all());

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
} 