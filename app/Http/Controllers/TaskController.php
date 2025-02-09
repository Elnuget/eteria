<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\TimeFormatter;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Task::with(['project', 'developer', 'completedBy']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('dificultad')) {
            $query->where('dificultad', $request->dificultad);
        }

        // Obtener todas las tareas según los filtros
        $tasks = $query->orderBy('created_at', 'desc')->get();

        // Obtener mis tareas activas (pendientes y en progreso)
        $misTareasActivas = Task::where('desarrollado_por', auth()->id())
            ->whereIn('estado', ['pendiente', 'en progreso'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener tareas del equipo (solo las que están en progreso por otros desarrolladores)
        $tareasEquipo = Task::where('desarrollado_por', '!=', auth()->id())
            ->whereNotNull('desarrollado_por')
            ->where('estado', 'en progreso')
            ->orderBy('prioridad', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $projects = Project::all();
        $users = User::all();
        
        return view('tasks.index', compact(
            'tasks', 
            'projects', 
            'users', 
            'misTareasActivas',
            'tareasEquipo'
        ));
    }

    public function create()
    {
        $projects = Project::all();
        $users = User::all();
        return view('tasks.create', compact('projects', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'proyecto_id' => 'required|exists:projects,id',
            'estado' => 'required|in:pendiente,en progreso,completada',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'dificultad' => 'required|in:facil,intermedia,dificil,experto',
            'tiempo_estimado' => 'required|integer|min:0',
            'fecha_limite' => 'nullable|date',
            'fecha_recordatorio' => 'nullable|date'
        ]);

        // Agregar fecha de asignación si hay desarrollador asignado
        if ($request->has('desarrollado_por')) {
            $validated['fecha_asignacion'] = now();
        }

        Task::create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea creada exitosamente.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = Project::all();
        $users = User::all();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'proyecto_id' => 'required|exists:projects,id',
            'estado' => 'required|in:pendiente,en progreso,completada',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'dificultad' => 'required|in:facil,intermedia,dificil,experto',
            'tiempo_estimado' => 'required|integer|min:0',
            'fecha_limite' => 'nullable|date',
            'fecha_recordatorio' => 'nullable|date'
        ]);

        // Si la tarea se marca como completada, registrar el usuario que la completó
        if ($validated['estado'] === 'completada' && $task->estado !== 'completada') {
            $validated['completado_por'] = auth()->id();
        }

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea eliminada exitosamente.');
    }

    public function tomarTarea(Task $task)
    {
        if ($task->desarrollado_por) {
            return redirect()->route('tasks.index')
                ->with('error', 'Esta tarea ya ha sido tomada por otro desarrollador.');
        }

        $task->update([
            'desarrollado_por' => auth()->id(),
            'estado' => 'en progreso',
            'tiempo_inicio' => now(),
            'fecha_asignacion' => now() // Asegurarnos de que sea un objeto Carbon
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Has tomado la tarea exitosamente.');
    }

    public function completarTarea(Task $task)
    {
        if ($task->estado === 'completada') {
            return redirect()->route('tasks.index')
                ->with('error', 'Esta tarea ya está completada.');
        }

        $tiempoReal = (int)now()->diffInSeconds($task->tiempo_inicio);

        $task->update([
            'estado' => 'completada',
            'completado_por' => auth()->id(),
            'tiempo_real' => $tiempoReal
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea completada exitosamente en ' . TimeFormatter::formatSeconds($tiempoReal));
    }
}