<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Project;
use App\Models\Cliente;
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
        $query = Balance::with(['proyecto', 'cliente']);

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

        $balances = $query->latest()->get();
        $proyectos = Project::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre')->get();
        
        return view('balances.index', compact('balances', 'proyectos', 'clientes'));
    }

    public function create(): View
    {
        return view('balances.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'proyecto_id' => 'nullable|exists:projects,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'monto' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'monto_pendiente' => 'required|numeric|min:0',
            'fecha_generacion' => 'required|date',
            'tipo_saldo' => 'required|in:anual,mensual,unico',
            'motivo' => 'nullable|string',
            'pagado_completo' => 'nullable|boolean'
        ]);

        try {
            // Asegurarnos de que los campos opcionales sean null si están vacíos
            $validated['proyecto_id'] = $request->proyecto_id ?: null;
            $validated['cliente_id'] = $request->cliente_id ?: null;
            $validated['pagado_completo'] = $request->has('pagado_completo');

            Balance::create($validated);
            return redirect()->route('balances.index')
                ->with('success', 'Balance creado exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear balance: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear el balance: ' . $e->getMessage()]);
        }
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
            'proyecto_id' => 'nullable|exists:projects,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'monto' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'monto_pendiente' => 'required|numeric|min:0',
            'fecha_generacion' => 'required|date',
            'tipo_saldo' => 'required|in:anual,mensual,unico',
            'motivo' => 'nullable|string',
            'pagado_completo' => 'nullable|boolean'
        ]);

        try {
            $balance->update($validated);
            return redirect()->route('balances.index')
                ->with('success', 'Balance actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el balance.']);
        }
    }

    public function destroy(Balance $balance): RedirectResponse
    {
        $balance->delete();

        return redirect()->route('balances.index')
            ->with('success', 'Balance eliminado exitosamente.');
    }

    public function updateBalances(Request $request)
    {
        try {
            $balancesPagados = Balance::where('pagado_completo', true)
                ->whereIn('tipo_saldo', ['mensual', 'anual'])
                ->get();

            $balancesActualizados = 0;

            foreach ($balancesPagados as $balance) {
                // Calcular la siguiente fecha según el tipo de saldo
                $nextFechaGeneracion = $balance->tipo_saldo === 'mensual' 
                    ? $balance->fecha_generacion->addMonth() 
                    : $balance->fecha_generacion->addYear();

                // Verificar si ya existe un balance para la siguiente fecha
                $existingBalance = Balance::where(function($query) use ($balance, $nextFechaGeneracion) {
                    $query->where('fecha_generacion', $nextFechaGeneracion);
                    
                    if ($balance->proyecto_id) {
                        $query->where('proyecto_id', $balance->proyecto_id);
                    } else {
                        $query->whereNull('proyecto_id');
                    }
                    
                    if ($balance->cliente_id) {
                        $query->where('cliente_id', $balance->cliente_id);
                    } else {
                        $query->whereNull('cliente_id');
                    }
                })->first();

                if (!$existingBalance) {
                    Balance::create([
                        'proyecto_id' => $balance->proyecto_id,
                        'cliente_id' => $balance->cliente_id,
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
                    'success' => true,
                    'message' => 'No se encontraron balances para actualizar o ya existen balances para los siguientes períodos.'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error al actualizar balances: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar los balances: ' . $e->getMessage()
            ]);
        }
    }
}