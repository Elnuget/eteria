<?php

namespace App\Http\Controllers;

use App\Models\Contabilidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContabilidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contabilidad = Contabilidad::with('usuario')
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        return view('contabilidad.index', compact('contabilidad'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        return view('contabilidad.create', compact('usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'motivo' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'usuario_id' => 'required|exists:users,id'
        ]);

        Contabilidad::create($request->all());

        return redirect()->route('contabilidad.index')
            ->with('success', 'Registro de contabilidad creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contabilidad $contabilidad)
    {
        $contabilidad->load('usuario');
        return view('contabilidad.show', compact('contabilidad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contabilidad $contabilidad)
    {
        $usuarios = User::orderBy('name')->get();
        return view('contabilidad.edit', compact('contabilidad', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contabilidad $contabilidad)
    {
        $request->validate([
            'fecha' => 'required|date',
            'motivo' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'usuario_id' => 'required|exists:users,id'
        ]);

        $contabilidad->update($request->all());

        return redirect()->route('contabilidad.index')
            ->with('success', 'Registro de contabilidad actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contabilidad $contabilidad)
    {
        $contabilidad->delete();

        return redirect()->route('contabilidad.index')
            ->with('success', 'Registro de contabilidad eliminado exitosamente.');
    }
}
