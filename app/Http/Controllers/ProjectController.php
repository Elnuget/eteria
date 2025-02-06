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
        $projects = Project::orderBy('created_at', 'desc')
                         ->paginate(9);
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
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_entrega' => 'nullable|date',
                'implementado_en' => 'nullable|date',
                'estado' => 'required|string|in:pendiente,en_progreso,completado,cancelado'
            ]);

            // Asignar estado por defecto si no se proporciona
            if (!isset($validated['estado'])) {
                $validated['estado'] = 'pendiente';
            }

            \Log::info('Datos validados:', $validated); // Para debugging

            $project = Project::create($validated);

            return redirect()->route('projects.index')
                ->with('success', 'Proyecto creado exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación: ' . $e->getMessage());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error al crear proyecto: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear el proyecto. Por favor, intente nuevamente. (' . $e->getMessage() . ')']);
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
                $projectData = [
                    'id' => $project->id,
                    'nombre' => $project->nombre,
                    'descripcion' => $project->descripcion,
                    'fecha_entrega' => optional($project->fecha_entrega)->format('Y-m-d'),
                    'implementado_en' => optional($project->implementado_en)->format('Y-m-d'),
                    'estado' => $project->estado
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
            'implementado_en' => 'nullable|date',
            'estado' => 'required|string|in:pendiente,en_progreso,completado,cancelado'
        ]);

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