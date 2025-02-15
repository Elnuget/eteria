<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Muestra una lista de todos los pagos.
     */
    public function index(Request $request): View
    {
        $query = Payment::with(['balance', 'balance.proyecto', 'balance.cliente']);

        // Filtro por método de pago
        if ($request->filled('metodo')) {
            $query->where('metodo_pago', $request->metodo);
        }

        // Filtro por período
        if ($request->filled('periodo')) {
            $today = now();
            switch ($request->periodo) {
                case 'hoy':
                    $query->whereDate('fecha_pago', $today);
                    break;
                case 'semana':
                    $query->whereBetween('fecha_pago', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                case 'mes':
                    $query->whereBetween('fecha_pago', [
                        $today->copy()->startOfMonth(),
                        $today->copy()->endOfMonth()
                    ]);
                    break;
                case 'mes_anterior':
                    $query->whereBetween('fecha_pago', [
                        $today->copy()->subMonth()->startOfMonth(),
                        $today->copy()->subMonth()->endOfMonth()
                    ]);
                    break;
                case 'antiguos':
                    $query->where('fecha_pago', '<', $today->copy()->subMonths(2)->startOfMonth());
                    break;
            }
        }

        // Filtro por monto
        if ($request->filled('monto')) {
            switch ($request->monto) {
                case 'bajo':
                    $query->where('monto', '<=', 1000);
                    break;
                case 'medio':
                    $query->whereBetween('monto', [1000, 5000]);
                    break;
                case 'alto':
                    $query->where('monto', '>', 5000);
                    break;
            }
        }

        $payments = $query->orderBy('created_at', 'desc')->get();
        $balances = Balance::with(['proyecto', 'cliente'])->get();
        
        return view('payments.index', compact('payments', 'balances'));
    }

    /**
     * Muestra el formulario para crear un nuevo pago.
     */
    public function create(): View
    {
        return view('payments.create');
    }

    /**
     * Almacena un nuevo pago en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'balance_id' => 'required|exists:balances,id',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $payment = Payment::create($validated);

        // Actualizar el balance
        $balance = Balance::find($validated['balance_id']);
        
        // Actualizar monto_pagado sumando el nuevo pago
        $balance->monto_pagado += $validated['monto'];
        
        // Recalcular monto_pendiente
        $balance->monto_pendiente = $balance->monto - $balance->monto_pagado;
        
        // Verificar si el balance está completamente pagado
        if ($balance->monto_pendiente <= 0) {
            $balance->pagado_completo = true;
        }
        
        $balance->save();

        return redirect()->route('payments.index')
            ->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Muestra un pago específico.
     */
    public function show(Payment $payment): View
    {
        return view('payments.show', compact('payment'));
    }

    /**
     * Muestra el formulario para editar un pago.
     */
    public function edit(Payment $payment): View
    {
        return view('payments.edit', compact('payment'));
    }

    /**
     * Actualiza un pago específico en la base de datos.
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'balance_id' => 'required|exists:balances,id',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $payment->update($validated);

        return redirect()->route('payments.index')
            ->with('success', 'Pago actualizado exitosamente.');
    }

    /**
     * Elimina un pago específico de la base de datos.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        // Obtener el balance relacionado
        $balance = $payment->balance;
        
        // Restar el monto del pago del monto_pagado
        $balance->monto_pagado -= $payment->monto;
        
        // Recalcular monto pendiente
        $balance->monto_pendiente = $balance->monto - $balance->monto_pagado;
        
        // Verificar si el balance ya no está completamente pagado
        if ($balance->monto_pendiente > 0) {
            $balance->pagado_completo = false;
        }
        
        $balance->save();
        
        // Eliminar el pago
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Pago eliminado exitosamente.');
    }
}