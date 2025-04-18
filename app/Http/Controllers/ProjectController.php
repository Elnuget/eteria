<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra una lista de todos los proyectos.
     */
    public function index(Request $request)
    {
        $query = Project::with('clientes');

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por implementación (basado en fechas)
        if ($request->filled('implementado')) {
            $today = now();
            switch ($request->implementado) {
                case 'esta_semana':
                    $query->whereBetween('implementado_en', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                case 'este_mes':
                    $query->whereBetween('implementado_en', [
                        $today->copy()->startOfMonth(),
                        $today->copy()->endOfMonth()
                    ]);
                    break;
                case 'sin_implementar':
                    $query->whereNull('implementado_en');
                    break;
                case 'implementado':
                    $query->whereNotNull('implementado_en');
                    break;
            }
        }

        // Filtro por fecha de entrega
        if ($request->filled('periodo')) {
            $today = now();
            switch ($request->periodo) {
                case 'semana':
                    $query->whereBetween('fecha_entrega', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;
                case 'mes':
                    $query->whereBetween('fecha_entrega', [
                        $today->copy()->startOfMonth(),
                        $today->copy()->endOfMonth()
                    ]);
                    break;
            }
        }

        $projects = $query->latest()->get();
        $clientes = Cliente::orderBy('nombre')->get();

        return view('projects.index', compact('projects', 'clientes'));
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

    /**
     * Obtiene los clientes asociados y disponibles para un proyecto
     */
    public function getClients(Project $project)
    {
        $associatedClients = $project->clientes;
        $availableClients = Cliente::whereNotIn('id', $project->clientes->pluck('id'))->get();

        return response()->json([
            'associated' => $associatedClients,
            'available' => $availableClients
        ]);
    }

    /**
     * Asocia un cliente a un proyecto
     */
    public function attachClient(Project $project, Cliente $client)
    {
        try {
            $project->clientes()->attach($client->id);
            return response()->json(['message' => 'Cliente asociado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al asociar el cliente'], 500);
        }
    }

    /**
     * Desasocia un cliente de un proyecto
     */
    public function detachClient(Project $project, Cliente $client)
    {
        try {
            $project->clientes()->detach($client->id);
            return response()->json(['message' => 'Cliente desasociado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al desasociar el cliente'], 500);
        }
    }
}