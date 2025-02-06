<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tasks = Task::with(['project', 'developer', 'completedBy'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(9); // Cambiado de get() a paginate()
        $projects = Project::all();
        $users = User::all();
        return view('tasks.index', compact('tasks', 'projects', 'users'));
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
            'tiempo_estimado' => 'nullable|integer|min:0',
        ]);

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
            'tiempo_estimado' => 'nullable|integer|min:0',
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
            'tiempo_inicio' => now()
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
        $mensaje = '';

        if ($tiempoReal < 60) {
            // Menos de un minuto
            $mensaje = (int)$tiempoReal . ' segundos';
        } elseif ($tiempoReal < 3600) {
            // Menos de una hora
            $minutos = (int)floor($tiempoReal / 60);
            $segundos = (int)($tiempoReal % 60);
            $mensaje = $minutos . ' minutos y ' . $segundos . ' segundos';
        } elseif ($tiempoReal < 86400) {
            // Menos de un día
            $horas = (int)floor($tiempoReal / 3600);
            $minutos = (int)floor(($tiempoReal % 3600) / 60);
            $segundos = (int)($tiempoReal % 60);
            $mensaje = $horas . ' horas, ' . $minutos . ' minutos y ' . $segundos . ' segundos';
        } else {
            // Más de un día
            $dias = (int)floor($tiempoReal / 86400);
            $horas = (int)floor(($tiempoReal % 86400) / 3600);
            $minutos = (int)floor(($tiempoReal % 3600) / 60);
            $segundos = (int)($tiempoReal % 60);
            $mensaje = $dias . ' días, ' . $horas . ' horas, ' . $minutos . ' minutos y ' . $segundos . ' segundos';
        }

        $task->update([
            'estado' => 'completada',
            'completado_por' => auth()->id(),
            'tiempo_real' => $tiempoReal
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea completada exitosamente en ' . $mensaje);
    }
}