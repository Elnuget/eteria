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
        if ($contexto) {
            return response()->json([
                'success' => true,
                'data' => $contexto
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Contexto no encontrado'
        ], 404);
    }

    /**
     * Actualizar un registro específico.
     */
    public function update(Request $request, Contexto $contexto)
    {
        $request->validate([
            'contexto' => 'required|string'
        ]);

        if (!$contexto) {
            return redirect()->route('contextos.index')->with('error', 'Contexto no encontrado');
        }

        $contexto->update([
            'contexto' => $request->contexto
        ]);

        return redirect()->route('contextos.index')->with('success', 'Contexto actualizado exitosamente');
    }

    /**
     * Eliminar un registro específico.
     */
    public function destroy(Contexto $contexto)
    {
        if (!$contexto) {
            return redirect()->route('contextos.index')->with('error', 'Contexto no encontrado');
        }

        $contexto->delete();
        return redirect()->route('contextos.index')->with('success', 'Contexto eliminado exitosamente');
    }
} 