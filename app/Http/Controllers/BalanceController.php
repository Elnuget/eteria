<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class BalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra una lista de todos los balances.
     */
    public function index(Request $request): View
    {
        $query = Balance::with('proyecto');

        // Filtro por estado de pago
        if ($request->filled('estado')) {
            switch ($request->estado) {
                case 'pendiente':
                    $query->where('pagado_completo', false)
                          ->where('fecha_generacion', '>=', now());
                    break;
                case 'pagado':
                    $query->where('pagado_completo', true);
                    break;
                case 'vencido':
                    $query->where('pagado_completo', false)
                          ->where('fecha_generacion', '<', now());
                    break;
            }
        }

        // Filtro por tipo de saldo
        if ($request->filled('tipo')) {
            $query->where('tipo_saldo', $request->tipo);
        }

        // Filtro por período
        if ($request->filled('periodo')) {
            $today = now();
            switch ($request->periodo) {
                case 'mes_actual':
                    $query->whereBetween('fecha_generacion', [
                        $today->copy()->startOfMonth(),
                        $today->copy()->endOfMonth()
                    ]);
                    break;
                case 'mes_anterior':
                    $query->whereBetween('fecha_generacion', [
                        $today->copy()->subMonth()->startOfMonth(),
                        $today->copy()->subMonth()->endOfMonth()
                    ]);
                    break;
                case 'proximo_mes':
                    $query->whereBetween('fecha_generacion', [
                        $today->copy()->addMonth()->startOfMonth(),
                        $today->copy()->addMonth()->endOfMonth()
                    ]);
                    break;
            }
        }

        $balances = $query->orderBy('created_at', 'desc')->get();
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

    public function edit(Balance $balance)
    {
        return response()->json($balance);
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

    public function updateBalances(Request $request)
    {
        $balancesPagados = Balance::where('pagado_completo', true)
            ->whereIn('tipo_saldo', ['mensual', 'anual'])
            ->get();

        $balancesActualizados = 0;
        $mensajeError = '';

        foreach ($balancesPagados as $balance) {
            // Calcular la siguiente fecha según el tipo de saldo
            $nextFechaGeneracion = $balance->tipo_saldo === 'mensual' 
                ? $balance->fecha_generacion->addMonth() 
                : $balance->fecha_generacion->addYear();

            // Verificar si ya existe un balance para la siguiente fecha
            $existingBalance = Balance::where('proyecto_id', $balance->proyecto_id)
                ->where('fecha_generacion', $nextFechaGeneracion)
                ->first();

            if (!$existingBalance) {
                Balance::create([
                    'proyecto_id' => $balance->proyecto_id,
                    'monto' => $balance->monto,
                    'monto_pagado' => 0,
                    'monto_pendiente' => $balance->monto,
                    'fecha_generacion' => $nextFechaGeneracion,
                    'tipo_saldo' => $balance->tipo_saldo,
                    'motivo' => $balance->motivo,
                    'pagado_completo' => false
                ]);
                $balancesActualizados++;
            }
        }

        if ($balancesActualizados > 0) {
            return response()->json([
                'success' => true,
                'message' => "Se han creado {$balancesActualizados} nuevos balances."
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron balances para actualizar o ya existen balances para los siguientes períodos.'
            ]);
        }
    }
}