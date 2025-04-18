<?php

namespace App\Http\Controllers;

use App\Models\Contexto;
use Illuminate\Http\Request;

class ContextoController extends Controller
{
    /**
     * Mostrar una lista de los registros.
     */
    public function index()
    {
        $contextos = Contexto::latest()->get();
        return view('contextos.index', compact('contextos'));
    }

    /**
     * Almacenar un nuevo registro.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contexto' => 'required|string'
        ]);

        Contexto::create($request->all());
        return redirect()->route('contextos.index')->with('success', 'Contexto creado exitosamente');
    }

    /**
     * Mostrar un registro específico.
     */
    public function show(Contexto $contexto)
    {
        return response()->json($contexto);
    }

    /**
     * Actualizar un registro específico.
     */
    public function update(Request $request, Contexto $contexto)
    {
        $request->validate([
            'contexto' => 'required|string'
        ]);

        $contexto->update($request->all());
        return redirect()->route('contextos.index')->with('success', 'Contexto actualizado exitosamente');
    }

    /**
     * Eliminar un registro específico.
     */
    public function destroy(Contexto $contexto)
    {
        $contexto->delete();
        return redirect()->route('contextos.index')->with('success', 'Contexto eliminado exitosamente');
    }
} 