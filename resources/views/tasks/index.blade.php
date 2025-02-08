@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Tareas</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
            <i class="fas fa-plus"></i> Nueva Tarea
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white" role="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter me-2"></i> Filtros
                <i class="fas fa-chevron-down ms-2"></i>
            </div>
        </div>

        <div class="collapse show" id="filtrosCollapse">
            <div class="card-body pt-0">
                <div class="row">
                    <!-- Estado -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Estado</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['estado' => '']) }}" 
                               class="btn btn-sm {{ !request('estado') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-tasks"></i> Todas
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['estado' => 'pendiente']) }}" 
                               class="btn btn-sm {{ request('estado') == 'pendiente' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-clock"></i> Pendientes
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['estado' => 'en progreso']) }}" 
                               class="btn btn-sm {{ request('estado') == 'en progreso' ? 'btn-info' : 'btn-outline-info' }}">
                                <i class="fas fa-spinner"></i> En Progreso
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['estado' => 'completada']) }}" 
                               class="btn btn-sm {{ request('estado') == 'completada' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-check"></i> Completadas
                            </a>
                        </div>
                    </div>

                    <!-- Prioridad -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Prioridad</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['prioridad' => '']) }}" 
                               class="btn btn-sm {{ !request('prioridad') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-layer-group"></i> Todas
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['prioridad' => 'urgente']) }}" 
                               class="btn btn-sm {{ request('prioridad') == 'urgente' ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="fas fa-exclamation-circle"></i> Urgente
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['prioridad' => 'alta']) }}" 
                               class="btn btn-sm {{ request('prioridad') == 'alta' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-arrow-up"></i> Alta
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['prioridad' => 'media']) }}" 
                               class="btn btn-sm {{ request('prioridad') == 'media' ? 'btn-info' : 'btn-outline-info' }}">
                                <i class="fas fa-equals"></i> Media
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['prioridad' => 'baja']) }}" 
                               class="btn btn-sm {{ request('prioridad') == 'baja' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-arrow-down"></i> Baja
                            </a>
                        </div>
                    </div>

                    <!-- Dificultad -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Dificultad</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['dificultad' => '']) }}" 
                               class="btn btn-sm {{ !request('dificultad') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-star-half-alt"></i> Todas
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['dificultad' => 'facil']) }}" 
                               class="btn btn-sm {{ request('dificultad') == 'facil' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-smile"></i> Fácil
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['dificultad' => 'intermedia']) }}" 
                               class="btn btn-sm {{ request('dificultad') == 'intermedia' ? 'btn-info' : 'btn-outline-info' }}">
                                <i class="fas fa-meh"></i> Intermedia
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['dificultad' => 'dificil']) }}" 
                               class="btn btn-sm {{ request('dificultad') == 'dificil' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-frown"></i> Difícil
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['dificultad' => 'experto']) }}" 
                               class="btn btn-sm {{ request('dificultad') == 'experto' ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="fas fa-dizzy"></i> Experto
                            </a>
                        </div>
                    </div>
                </div>

                @if(request()->anyFilled(['estado', 'prioridad', 'dificultad']))
                    <div class="mt-3 text-end">
                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar filtros
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($tareasPendientes->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-tasks"></i> Tus Tareas Pendientes ({{ $tareasPendientes->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarea</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Tiempo Estimado</th>
                                <th>Proyecto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tareasPendientes as $tareaPendiente)
                                <tr>
                                    <td>{{ $tareaPendiente->nombre }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $tareaPendiente->prioridad === 'urgente' ? 'danger' : 
                                            ($tareaPendiente->prioridad === 'alta' ? 'warning' : 
                                            ($tareaPendiente->prioridad === 'media' ? 'info' : 'secondary')) 
                                        }}">
                                            {{ ucfirst($tareaPendiente->prioridad) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tareaPendiente->estado === 'en progreso' ? 'warning' : 'secondary' }}">
                                            {{ $tareaPendiente->estado }}
                                        </span>
                                    </td>
                                    <td>⏳ {{ $tareaPendiente->tiempo_estimado }} min</td>
                                    <td>{{ $tareaPendiente->project->nombre }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($tasks->isEmpty())
        <div class="alert alert-info">
            No hay tareas registradas.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($tasks as $task)
                <div class="col mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $task->nombre }}</h5>
                            <div class="d-flex gap-2">
                                <span class="badge bg-{{ $task->estado === 'completada' ? 'success' : ($task->estado === 'en progreso' ? 'warning' : 'secondary') }}">
                                    {{ $task->estado }}
                                </span>
                                <span class="badge bg-{{ 
                                    $task->prioridad === 'urgente' ? 'danger' : 
                                    ($task->prioridad === 'alta' ? 'warning' : 
                                    ($task->prioridad === 'media' ? 'info' : 'secondary')) 
                                }}">
                                    {{ ucfirst($task->prioridad) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Proyecto:</small>
                                <p class="mb-2">{{ $task->project->nombre }}</p>
                            </div>
                            
                            @if($task->descripcion)
                            <div class="mb-3">
                                <small class="text-muted">Descripción:</small>
                                <p class="mb-2">{{ $task->descripcion }}</p>
                            </div>
                            @endif

                            <div class="mb-3">
                                <small class="text-muted">Desarrollador:</small>
                                <p class="mb-2">{{ $task->developer->name ?? 'Sin asignar' }}</p>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <small class="text-muted">Dificultad:</small>
                                    <p class="mb-0">
                                        @switch($task->dificultad)
                                            @case('facil')
                                                <span class="badge bg-success">⭐ Fácil</span>
                                                @break
                                            @case('intermedia')
                                                <span class="badge bg-info">⭐⭐ Intermedia</span>
                                                @break
                                            @case('dificil')
                                                <span class="badge bg-warning">⭐⭐⭐ Difícil</span>
                                                @break
                                            @case('experto')
                                                <span class="badge bg-danger">⭐⭐⭐⭐ Experto</span>
                                                @break
                                        @endswitch
                                    </p>
                                </div>
                                <div>
                                    <small class="text-muted">Tiempo Estimado:</small>
                                    <p class="mb-0">⏳ {{ $task->tiempo_estimado ?? 'No definido' }} min</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Fecha de Asignación:</small>
                                <p class="mb-2">
                                    @if($task->fecha_asignacion)
                                        📅 {{ $task->fecha_asignacion->format('d/m/Y H:i') }}
                                    @else
                                        @if($task->created_at)
                                            📅 {{ $task->created_at->format('d/m/Y H:i') }}
                                            <small class="text-muted">(fecha de creación)</small>
                                        @else
                                            No disponible
                                        @endif
                                    @endif
                                </p>
                            </div>

                            @if($task->fecha_limite)
                            <div class="mb-3">
                                <small class="text-muted">Fecha Límite:</small>
                                <p class="mb-2">📅 {{ $task->fecha_limite->format('d/m/Y H:i') }}</p>
                            </div>
                            @endif

                            @if($task->tiempo_real)
                            <div class="mb-3">
                                <small class="text-muted">Tiempo Real:</small>
                                <p class="mb-2">⏰ {{ App\Helpers\TimeFormatter::formatSeconds($task->tiempo_real) }}</p>
                            </div>
                            @endif

                            @if($task->fecha_recordatorio && !$task->recordatorio_enviado)
                            <div class="alert alert-warning mb-3">
                                <small>🔔 Recordatorio programado para:</small>
                                <p class="mb-0">{{ $task->fecha_recordatorio->format('d/m/Y H:i') }}</p>
                            </div>
                            @endif

                            @if($task->completado_por)
                                <div class="mb-3">
                                    <small class="text-muted">Completado por:</small>
                                    <p class="mb-2">{{ $task->completedBy->name }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex flex-column gap-2">
                                @if(!$task->desarrollado_por)
                                    <form action="{{ route('tasks.tomar', $task) }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-hand-pointer"></i> Tomar Tarea
                                        </button>
                                    </form>
                                @elseif($task->desarrollado_por == auth()->id())
                                    <div class="d-flex flex-column gap-2 w-100">
                                        <span class="btn btn-secondary disabled w-100">
                                            <i class="fas fa-user-check"></i> Tarea Asignada a Ti
                                        </span>
                                        @if($task->estado !== 'completada')
                                            <form action="{{ route('tasks.completar', $task) }}" method="POST" class="w-100">
                                                @csrf
                                                <button type="submit" class="btn btn-info w-100" 
                                                        onclick="return confirm('¿Estás seguro de completar esta tarea?')">
                                                    <i class="fas fa-check"></i> Completar Tarea
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="btn btn-info disabled w-100">
                                        <i class="fas fa-user"></i> Asignada a {{ $task->developer->name }}
                                    </span>
                                @endif

                                @if($task->desarrollado_por == auth()->id() || auth()->user()->is_admin)
                                    <div class="d-flex gap-2 w-100">
                                        <button type="button" class="btn btn-warning flex-grow-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editTaskModal-{{ $task->id }}">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100" 
                                                    onclick="return confirm('¿Está seguro de eliminar esta tarea?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de Edición - Movido dentro del foreach -->
                <div class="modal fade" id="editTaskModal-{{ $task->id }}" tabindex="-1" aria-labelledby="editTaskModalLabel-{{ $task->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('tasks.update', $task) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editTaskModalLabel-{{ $task->id }}">Editar Tarea</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="nombre-{{ $task->id }}" class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" id="nombre-{{ $task->id }}" 
                                            name="nombre" value="{{ $task->nombre }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="descripcion-{{ $task->id }}" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion-{{ $task->id }}" 
                                            name="descripcion" rows="3">{{ $task->descripcion }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="proyecto_id-{{ $task->id }}" class="form-label">Proyecto *</label>
                                        <select class="form-select" id="proyecto_id-{{ $task->id }}" name="proyecto_id" required>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" {{ $task->proyecto_id == $project->id ? 'selected' : '' }}>
                                                    {{ $project->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="estado-{{ $task->id }}" class="form-label">Estado *</label>
                                        <select class="form-select" id="estado-{{ $task->id }}" name="estado" required>
                                            <option value="pendiente" {{ $task->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="en progreso" {{ $task->estado == 'en progreso' ? 'selected' : '' }}>En Progreso</option>
                                            <option value="completada" {{ $task->estado == 'completada' ? 'selected' : '' }}>Completada</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="prioridad-{{ $task->id }}" class="form-label">Prioridad *</label>
                                        <select class="form-select" id="prioridad-{{ $task->id }}" name="prioridad" required>
                                            <option value="baja" {{ $task->prioridad == 'baja' ? 'selected' : '' }}>Baja</option>
                                            <option value="media" {{ $task->prioridad == 'media' ? 'selected' : '' }}>Media</option>
                                            <option value="alta" {{ $task->prioridad == 'alta' ? 'selected' : '' }}>Alta</option>
                                            <option value="urgente" {{ $task->prioridad == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                        </select>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="dificultad-{{ $task->id }}" class="form-label">Dificultad *</label>
                                            <select class="form-select" id="dificultad-{{ $task->id }}" name="dificultad" required>
                                                <option value="facil" {{ $task->dificultad == 'facil' ? 'selected' : '' }}>Fácil</option>
                                                <option value="intermedia" {{ $task->dificultad == 'intermedia' ? 'selected' : '' }}>Intermedia</option>
                                                <option value="dificil" {{ $task->dificultad == 'dificil' ? 'selected' : '' }}>Difícil</option>
                                                <option value="experto" {{ $task->dificultad == 'experto' ? 'selected' : '' }}>Experto</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tiempo_estimado-{{ $task->id }}" class="form-label">Tiempo Estimado (min) *</label>
                                            <input type="number" class="form-control" id="tiempo_estimado-{{ $task->id }}" 
                                                name="tiempo_estimado" value="{{ $task->tiempo_estimado }}" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Modal de Creación - Fuera del foreach porque no necesita $task -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTaskModalLabel">Nueva Tarea</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="proyecto_id" class="form-label">Proyecto *</label>
                            <select class="form-select @error('proyecto_id') is-invalid @enderror" 
                                id="proyecto_id" name="proyecto_id" required>
                                <option value="">Seleccionar proyecto</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('proyecto_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proyecto_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select @error('estado') is-invalid @enderror" 
                                id="estado" name="estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="en progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="prioridad" class="form-label">Prioridad *</label>
                            <select class="form-select @error('prioridad') is-invalid @enderror" 
                                id="prioridad" name="prioridad" required>
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                            @error('prioridad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="dificultad" class="form-label">Dificultad *</label>
                                <select class="form-select" id="dificultad" name="dificultad" required>
                                    <option value="facil">Fácil</option>
                                    <option value="intermedia" selected>Intermedia</option>
                                    <option value="dificil">Difícil</option>
                                    <option value="experto">Experto</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tiempo_estimado" class="form-label">Tiempo Estimado (min) *</label>
                                <input type="number" class="form-control" id="tiempo_estimado" name="tiempo_estimado" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .card-footer {
        padding: 1rem;
    }
    .btn {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    .disabled {
        cursor: not-allowed;
        opacity: 0.8;
    }
    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .badge {
        font-size: 0.8rem;
    }
    .card-body small.text-muted {
        font-size: 0.8rem;
    }
    .card-body p {
        color: #333;
        font-size: 0.95rem;
    }
    .table-responsive {
        margin: 0;
        padding: 0;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table td, .table th {
        vertical-align: middle;
    }
    
    .card-header h5 {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .input-group-text {
        border-right: 0;
    }
    .form-select {
        cursor: pointer;
    }
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .input-group:hover .input-group-text,
    .input-group:hover .form-select {
        border-color: #86b7fe;
    }
    .btn-outline-secondary:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-group {
        flex-wrap: wrap;
        gap: 5px;
    }
    .btn-group .btn {
        border-radius: 20px !important;
        padding: 0.375rem 1rem;
        font-size: 0.9rem;
    }
    .btn-check:checked + .btn-outline-secondary {
        background-color: #6c757d;
        color: white;
    }
    .btn-check:checked + .btn-outline-warning {
        background-color: #ffc107;
        color: black;
    }
    .btn-check:checked + .btn-outline-info {
        background-color: #0dcaf0;
        color: white;
    }
    .btn-check:checked + .btn-outline-success {
        background-color: #198754;
        color: white;
    }
    .btn-check:checked + .btn-outline-danger {
        background-color: #dc3545;
        color: white;
    }
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
        color: white;
    }
    .filter-sections {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .filter-section {
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
    }

    .filter-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .filter-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .filter-options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .filter-pill .btn {
        border-radius: 20px;
        font-size: 0.85rem;
        padding: 0.3rem 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s ease;
    }

    .filter-pill .btn i {
        font-size: 0.8rem;
    }

    .btn-check:checked + .btn {
        transform: scale(1.05);
    }

    .btn-check:checked + .btn-outline-secondary {
        background-color: #6c757d;
        color: white;
    }

    .card-header {
        cursor: pointer;
        user-select: none;
    }

    .btn-sm {
        padding: 0.35rem 0.8rem;
        font-size: 0.9rem;
        border-radius: 20px;
        white-space: nowrap;
        font-weight: 500;
    }

    .btn-sm i {
        font-size: 0.85rem;
        margin-right: 0.3rem;
    }

    .gap-1 {
        gap: 0.4rem !important;
    }

    label {
        font-size: 1rem;
        font-weight: 600;
        color: #555;
        margin-bottom: 0.5rem;
    }

    .card-header {
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }

    .card-header:hover {
        background-color: #f8f9fa;
    }

    /* Ajustes para el diseño horizontal */
    .d-flex.flex-wrap {
        margin: -0.2rem;
    }

    .d-flex.flex-wrap > * {
        margin: 0.2rem;
    }

    /* Estilos para los botones activos */
    .btn-secondary, .btn-outline-secondary:hover,
    .btn-warning, .btn-outline-warning:hover,
    .btn-info, .btn-outline-info:hover,
    .btn-success, .btn-outline-success:hover,
    .btn-danger, .btn-outline-danger:hover {
        color: white;
    }

    .btn-warning, .btn-outline-warning:hover {
        color: #000;
    }

    /* Ajustes para el espaciado vertical */
    .col-md-4 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    /* Ajustes para el contenedor de filtros */
    .card-body {
        padding: 1rem 1.25rem;
    }

    /* Mejorar la visibilidad de los botones activos */
    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn.active {
        transform: scale(1.05);
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script>
function setFilter(type, value) {
    document.getElementById(type + '_filter').value = value;
    document.getElementById('filterForm').submit();
}
</script>
@endpush 