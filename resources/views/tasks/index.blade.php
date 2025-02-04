@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Tareas</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
            <i class="fas fa-plus"></i> Nueva Tarea
        </button>
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
                            <span class="badge bg-{{ $task->estado === 'completada' ? 'success' : ($task->estado === 'en progreso' ? 'warning' : 'secondary') }}">
                                {{ $task->estado }}
                            </span>
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
            @endforeach
        </div>
    @endif
</div>

<!-- Modal Crear Tarea -->
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

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="tiempo_estimado" class="form-label">Tiempo Estimado (min)</label>
                                <input type="number" class="form-control" id="tiempo_estimado" name="tiempo_estimado" min="0">
                            </div>
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

<!-- Modales de Edición -->
@foreach($tasks as $task)
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

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="tiempo_estimado-{{ $task->id }}" class="form-label">Tiempo Estimado (min)</label>
                                <input type="number" class="form-control" id="tiempo_estimado-{{ $task->id }}" 
                                    name="tiempo_estimado" value="{{ $task->tiempo_estimado }}" min="0">
                            </div>
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
</style>
@endpush 