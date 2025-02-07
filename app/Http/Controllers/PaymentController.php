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
    public function index(): View
    {
        $payments = Payment::with('balance')
                         ->orderBy('created_at', 'desc')
                         ->paginate(9);
        $balances = Balance::all();
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