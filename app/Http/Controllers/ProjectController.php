<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra una lista de todos los proyectos.
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Muestra el formulario para crear un nuevo proyecto.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Almacena un nuevo proyecto en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_entrega' => 'nullable|date',
            'precio' => 'required|numeric|min:0',
            'saldo' => 'required|numeric|min:0',
            'implementado_en' => 'nullable|date',
            'monto_anual' => 'required|numeric|min:0',
            'tiene_pago_unico' => 'boolean',
            'monto_unico' => 'nullable|numeric|min:0|required_if:tiene_pago_unico,true',
            'tiene_pago_mensual' => 'boolean',
            'monto_mensual' => 'nullable|numeric|min:0|required_if:tiene_pago_mensual,true',
        ]);

        // Asegurar que los valores booleanos sean correctos
        $validated['tiene_pago_unico'] = $request->has('tiene_pago_unico');
        $validated['tiene_pago_mensual'] = $request->has('tiene_pago_mensual');

        try {
            Project::create($validated);
            return redirect()->route('projects.index')
                ->with('success', 'Proyecto creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear el proyecto. Por favor, intente nuevamente.']);
        }
    }

    /**
     * Muestra un proyecto específico.
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    /**
     * Muestra el formulario para editar un proyecto.
     */
    public function edit(Project $project)
    {
        if (request()->ajax()) {
            try {
                // Formatear los datos para la respuesta JSON
                $projectData = [
                    'id' => $project->id,
                    'nombre' => $project->nombre,
                    'descripcion' => $project->descripcion,
                    'fecha_entrega' => optional($project->fecha_entrega)->format('Y-m-d'),
                    'precio' => number_format($project->precio, 2, '.', ''),
                    'saldo' => number_format($project->saldo, 2, '.', ''),
                    'monto_anual' => number_format($project->monto_anual, 2, '.', ''),
                    'tiene_pago_mensual' => (bool) $project->tiene_pago_mensual,
                    'monto_mensual' => $project->monto_mensual ? number_format($project->monto_mensual, 2, '.', '') : null,
                    'tiene_pago_unico' => (bool) $project->tiene_pago_unico,
                    'monto_unico' => $project->monto_unico ? number_format($project->monto_unico, 2, '.', '') : null,
                ];

                return response()->json($projectData);
            } catch (\Exception $e) {
                \Log::error('Error al cargar proyecto: ' . $e->getMessage());
                return response()->json(['error' => 'Error al cargar los datos del proyecto'], 500);
            }
        }
        
        return view('projects.edit', compact('project'));
    }

    /**
     * Actualiza un proyecto específico en la base de datos.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_entrega' => 'nullable|date',
            'precio' => 'required|numeric|min:0',
            'saldo' => 'required|numeric|min:0',
            'implementado_en' => 'nullable|date',
            'monto_anual' => 'required|numeric|min:0',
            'tiene_pago_unico' => 'boolean',
            'monto_unico' => 'nullable|numeric|min:0|required_if:tiene_pago_unico,true',
            'tiene_pago_mensual' => 'boolean',
            'monto_mensual' => 'nullable|numeric|min:0|required_if:tiene_pago_mensual,true',
        ]);

        // Asegurar que los valores booleanos sean correctos
        $validated['tiene_pago_unico'] = $request->has('tiene_pago_unico');
        $validated['tiene_pago_mensual'] = $request->has('tiene_pago_mensual');

        try {
            $project->update($validated);
            return redirect()->route('projects.index')
                ->with('success', 'Proyecto actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el proyecto. Por favor, intente nuevamente.']);
        }
    }

    /**
     * Elimina un proyecto específico.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto eliminado exitosamente.');
    }
} 