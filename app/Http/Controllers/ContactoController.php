<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    /**
     * Mostrar una lista de los contactos.
     */
    public function index()
    {
        $contactos = Contacto::orderBy('created_at', 'desc')->get();
        return view('contactos.index', compact('contactos'));
    }

    /**
     * Almacenar un nuevo contacto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string',
            'nombre' => 'nullable|string|max:255',
            'estado' => 'required|in:iniciado,por iniciar'
        ]);

        $contacto = Contacto::create($request->all());

        return redirect()->route('contactos.index')->with('success', 'Contacto registrado exitosamente');
    }

    /**
     * Actualizar un contacto específico.
     */
    public function update(Request $request, Contacto $contacto)
    {
        $request->validate([
            'numero' => 'required|string',
            'nombre' => 'nullable|string|max:255',
            'estado' => 'required|in:iniciado,por iniciar'
        ]);

        $contacto->update($request->all());

        return redirect()->route('contactos.index')->with('success', 'Contacto actualizado exitosamente');
    }

    /**
     * Eliminar un contacto específico.
     */
    public function destroy(Contacto $contacto)
    {
        $contacto->delete();
        return redirect()->route('contactos.index')->with('success', 'Contacto eliminado exitosamente');
    }
} 