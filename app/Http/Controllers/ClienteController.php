<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Project;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::with('proyectos')->orderBy('nombre')->paginate(10);
        $proyectos = Project::orderBy('nombre')->get();
        return view('clientes.index', compact('clientes', 'proyectos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(Cliente::$rules);

        $cliente = Cliente::create($request->all());

        if ($request->has('proyectos')) {
            $cliente->proyectos()->attach($request->proyectos);
        }

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        $cliente->load('proyectos');
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $proyectos = Project::orderBy('nombre')->get();
        return view('clientes.edit', compact('cliente', 'proyectos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $rules = Cliente::$rules;
        $rules['correo'] = 'required|email|unique:clientes,correo,'.$cliente->id;
        
        $request->validate($rules);

        $cliente->update($request->all());

        if ($request->has('proyectos')) {
            $cliente->proyectos()->sync($request->proyectos);
        }

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Asocia un proyecto al cliente
     */
    public function attachProject(Request $request, Cliente $cliente)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:projects,id'
        ]);

        try {
            $cliente->proyectos()->attach($request->proyecto_id);
            return response()->json(['message' => 'Proyecto asociado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'El proyecto ya está asociado al cliente'], 422);
        }
    }

    /**
     * Desasocia un proyecto del cliente
     */
    public function detachProject(Request $request, Cliente $cliente)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:projects,id'
        ]);

        $cliente->proyectos()->detach($request->proyecto_id);
        return response()->json(['message' => 'Proyecto desasociado exitosamente']);
    }
}
