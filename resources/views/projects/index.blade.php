@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Listado de Proyectos</span>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        Nuevo Proyecto
                    </button>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @forelse ($projects as $project)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-transparent">
                                        <h5 class="card-title mb-0">{{ $project->nombre }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted">Fecha de Entrega:</small><br>
                                            {{ $project->fecha_entrega ? $project->fecha_entrega->format('d/m/Y') : 'No definida' }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Estado:</small><br>
                                            <span class="badge bg-{{ $project->estado === 'completado' ? 'success' : 
                                                ($project->estado === 'en_progreso' ? 'primary' : 
                                                ($project->estado === 'cancelado' ? 'danger' : 'warning')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $project->estado)) }}
                                            </span>
                                        </div>
                                        @if($project->implementado_en)
                                            <div class="mb-2">
                                                <small class="text-muted">Implementado en:</small><br>
                                                {{ $project->implementado_en->format('d/m/Y') }}
                                            </div>
                                        @endif
                                        @if($project->descripcion)
                                            <div class="mb-2">
                                                <small class="text-muted">Descripción:</small><br>
                                                {{ Str::limit($project->descripcion, 100) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm"
                                                    onclick="editProject({{ $project->id }})">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-success btn-sm"
                                                    onclick="createBalance({{ $project->id }})">
                                                <i class="fas fa-dollar-sign"></i> Saldo
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-info btn-sm"
                                                    onclick="createTask({{ $project->id }})">
                                                <i class="fas fa-tasks"></i> Tarea
                                            </button>
                                            <form action="{{ route('projects.destroy', $project) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('¿Estás seguro de eliminar este proyecto?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    No hay proyectos registrados.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear proyecto -->
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProjectModalLabel">Crear Nuevo Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.store') }}" method="POST" id="createProjectForm">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Proyecto *</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                            id="nombre" name="nombre" required value="{{ old('nombre') }}">
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
                        <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
                        <input type="date" class="form-control @error('fecha_entrega') is-invalid @enderror" 
                            id="fecha_entrega" name="fecha_entrega" value="{{ old('fecha_entrega', date('Y-m-d')) }}">
                    </div>

                    <div class="mb-3">
                        <label for="implementado_en" class="form-label">Fecha de Implementación</label>
                        <input type="date" class="form-control @error('implementado_en') is-invalid @enderror" 
                            id="implementado_en" name="implementado_en" value="{{ old('implementado_en', date('Y-m-d')) }}">
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select @error('estado') is-invalid @enderror" 
                            id="estado" name="estado" required>
                            <option value="">Seleccionar estado</option>
                            <option value="pendiente" {{ old('estado', 'pendiente') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_progreso" {{ old('estado') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                            <option value="completado" {{ old('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelado" {{ old('estado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar proyecto -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProjectModalLabel">Editar Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editProjectForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre del Proyecto *</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_fecha_entrega" class="form-label">Fecha de Entrega</label>
                        <input type="date" class="form-control" id="edit_fecha_entrega" name="fecha_entrega">
                    </div>

                    <div class="mb-3">
                        <label for="edit_implementado_en" class="form-label">Fecha de Implementación</label>
                        <input type="date" class="form-control" id="edit_implementado_en" name="implementado_en">
                    </div>

                    <div class="mb-3">
                        <label for="edit_estado" class="form-label">Estado *</label>
                        <select class="form-select" id="edit_estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Proyecto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para crear balance -->
<div class="modal fade" id="createBalanceModal" tabindex="-1" aria-labelledby="createBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBalanceModalLabel">Crear Nuevo Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('balances.store') }}" method="POST" id="createBalanceForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="proyecto_id" name="proyecto_id">
                    
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_generacion" class="form-label">Fecha de Generación *</label>
                        <input type="date" class="form-control" id="fecha_generacion" name="fecha_generacion" 
                               required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label for="tipo_saldo" class="form-label">Tipo de Saldo *</label>
                        <select class="form-select" id="tipo_saldo" name="tipo_saldo" required>
                            <option value="anual">Anual</option>
                            <option value="mensual">Mensual</option>
                            <option value="unico">Único</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto Total *</label>
                        <input type="number" step="0.01" class="form-control" id="monto" name="monto" 
                               required value="0" onchange="calcularMontoPendiente()">
                    </div>

                    <div class="mb-3">
                        <label for="monto_pagado" class="form-label">Monto Pagado *</label>
                        <input type="number" step="0.01" class="form-control" id="monto_pagado" 
                               name="monto_pagado" required value="0" onchange="calcularMontoPendiente()">
                    </div>

                    <div class="mb-3">
                        <label for="monto_pendiente" class="form-label">Monto Pendiente</label>
                        <input type="number" step="0.01" class="form-control" id="monto_pendiente" 
                               name="monto_pendiente" readonly>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pagado_completo" 
                                   name="pagado_completo">
                            <label class="form-check-label" for="pagado_completo">
                                Pagado Completo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para crear tarea -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Nueva Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="proyecto_id_task" name="proyecto_id">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                            id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                            id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select @error('estado') is-invalid @enderror" 
                            id="estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="en progreso">En Progreso</option>
                            <option value="completada">Completada</option>
                        </select>
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
                            <input type="number" class="form-control" id="tiempo_estimado" 
                                   name="tiempo_estimado" min="0" required>
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

@endsection

@push('scripts')
<script>
// Variable global para el modal
let editProjectModal = null;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el modal
    editProjectModal = new bootstrap.Modal(document.getElementById('editProjectModal'));
    
    // Mostrar modal de creación si hay errores
    @if($errors->any())
        var createProjectModal = new bootstrap.Modal(document.getElementById('createProjectModal'));
        createProjectModal.show();
    @endif
});

function editProject(projectId) {
    // Obtener referencia al modal
    const modalElement = document.getElementById('editProjectModal');
    
    // Mostrar el modal
    if (editProjectModal) {
        editProjectModal.show();
    } else {
        editProjectModal = new bootstrap.Modal(modalElement);
        editProjectModal.show();
    }

    // Realizar la petición AJAX
    fetch(`/projects/${projectId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Actualizar el formulario
        const form = document.getElementById('editProjectForm');
        form.action = `/projects/${projectId}`;

        // Llenar los campos
        document.getElementById('edit_nombre').value = data.nombre || '';
        document.getElementById('edit_descripcion').value = data.descripcion || '';
        document.getElementById('edit_fecha_entrega').value = data.fecha_entrega || '';
        document.getElementById('edit_implementado_en').value = data.implementado_en || '';
        document.getElementById('edit_estado').value = data.estado || 'pendiente';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los datos del proyecto');
    });
}

function createBalance(projectId) {
    document.getElementById('proyecto_id').value = projectId;
    const modal = new bootstrap.Modal(document.getElementById('createBalanceModal'));
    modal.show();
}

function calcularMontoPendiente() {
    const montoTotal = parseFloat(document.getElementById('monto').value) || 0;
    const montoPagado = parseFloat(document.getElementById('monto_pagado').value) || 0;
    const montoPendiente = montoTotal - montoPagado;
    document.getElementById('monto_pendiente').value = montoPendiente.toFixed(2);
}

// Inicializar el cálculo del monto pendiente
document.addEventListener('DOMContentLoaded', function() {
    calcularMontoPendiente();
});

function createTask(projectId) {
    document.getElementById('proyecto_id_task').value = projectId;
    const modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
    modal.show();
}
</script>
@endpush

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .btn-group .btn {
        flex: 1;
    }
</style>
@endpush