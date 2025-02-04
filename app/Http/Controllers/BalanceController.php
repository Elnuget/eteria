<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra una lista de todos los balances.
     */
    public function index(): View
    {
        $balances = Balance::with('proyecto')->latest()->paginate(10);
        $proyectos = Project::all(['id', 'nombre']);
        return view('balances.index', compact('balances', 'proyectos'));
    }

    public function create(): View
    {
        return view('balances.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:projects,id',
            'monto' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'monto_pendiente' => 'required|numeric|min:0',
            'fecha_generacion' => 'required|date',
            'tipo_saldo' => 'required|in:anual,mensual,unico',
            'motivo' => 'nullable|string|max:255',
            'pagado_completo' => 'boolean'
        ]);

        Balance::create($validated);

        return redirect()->route('balances.index')
            ->with('success', 'Balance creado exitosamente.');
    }

    public function show(Balance $balance): View
    {
        return view('balances.show', compact('balance'));
    }

    public function edit(Balance $balance): View
    {
        return view('balances.edit', compact('balance'));
    }

    public function update(Request $request, Balance $balance): RedirectResponse
    {
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:projects,id',
            'monto' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'monto_pendiente' => 'required|numeric|min:0',
            'fecha_generacion' => 'required|date',
            'tipo_saldo' => 'required|in:anual,mensual,unico',
            'motivo' => 'nullable|string|max:255',
            'pagado_completo' => 'boolean'
        ]);

        $balance->update($validated);

        return redirect()->route('balances.index')
            ->with('success', 'Balance actualizado exitosamente.');
    }

    public function destroy(Balance $balance): RedirectResponse
    {
        $balance->delete();

        return redirect()->route('balances.index')
            ->with('success', 'Balance eliminado exitosamente.');
    }
} 