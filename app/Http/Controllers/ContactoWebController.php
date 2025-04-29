<?php

namespace App\Http\Controllers;

use App\Models\ContactoWeb;
use Illuminate\Http\Request;

class ContactoWebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactosWeb = ContactoWeb::orderBy('created_at', 'desc')->paginate(15); // Paginar resultados
        return view('contacto-webs.index', compact('contactosWeb'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Puedes añadir una vista para crear si es necesario
        // return view('contacto-webs.create');
        abort(404); // O simplemente no permitir creación manual por ahora
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lógica para guardar un nuevo contacto web (si se implementa create)
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactoWeb $contactoWeb)
    {
        // Puedes añadir una vista para mostrar detalles si es necesario
        // $contactoWeb->load('chatMessages'); // Cargar mensajes si se necesita
        // return view('contacto-webs.show', compact('contactoWeb'));
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactoWeb $contactoWeb)
    {
        // Puedes añadir una vista para editar si es necesario
        // return view('contacto-webs.edit', compact('contactoWeb'));
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactoWeb $contactoWeb)
    {
        // Lógica para actualizar (si se implementa edit)
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactoWeb $contactoWeb)
    {
        try {
            $contactoWeb->delete();
            return redirect()->route('contacto-webs.index')->with('success', 'Contacto web eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('contacto-webs.index')->with('error', 'Error al eliminar el contacto web.');
        }
    }
} 