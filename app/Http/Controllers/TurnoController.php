<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Contacto;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    /**
     * Mostrar una lista de los turnos.
     */
    public function index()
    {
        $turnos = Turno::with(['contacto', 'contactoWeb'])
            ->orderBy('fecha_turno', 'asc')
            ->get()
            ->groupBy(function($turno) {
                return $turno->fecha_turno->format('Y-m-d');
            });

        $contactos = Contacto::orderBy('nombre')->get();

        return view('turnos.index', compact('turnos', 'contactos'));
    }

    /**
     * Almacenar un nuevo turno.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contacto_id' => 'required|exists:contactos,id',
            'fecha_turno' => 'required|date',
            'motivo' => 'required|string|max:255',
        ]);

        $turno = Turno::create($request->all());

        return redirect()->route('turnos.index')->with('success', 'Turno registrado exitosamente');
    }

    /**
     * Actualizar un turno específico.
     */
    public function update(Request $request, Turno $turno)
    {
        $request->validate([
            'fecha_turno' => 'required|date',
            'motivo' => 'required|string|max:255',
        ]);

        $turno->update($request->all());

        return redirect()->route('turnos.index')->with('success', 'Turno actualizado exitosamente');
    }

    /**
     * Eliminar un turno específico.
     */
    public function destroy(Turno $turno)
    {
        $turno->delete();
        return redirect()->route('turnos.index')->with('success', 'Turno eliminado exitosamente');
    }
} 