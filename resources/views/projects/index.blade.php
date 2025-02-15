@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Listado de Proyectos</span>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        <i class="fas fa-plus"></i> Nuevo Proyecto
                    </button>
                </div>

                <!-- Sección de Filtros -->
                <div class="card mb-0 border-0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-filter"></i> Filtros
                        </h5>
                        @if(request()->anyFilled(['estado', 'implementado', 'periodo']))
                            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpiar filtros
                            </a>
                        @endif
                    </div>
                    <div class="card-body bg-light pt-0">
                        <form method="GET" action="{{ route('projects.index') }}" id="filterForm" class="mt-3">
                            <div class="row g-3">
                                <!-- Estado -->
                                <div class="col-md-4">
                                    <label class="filter-label mb-2">Estado del Proyecto</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <input type="radio" class="btn-check" name="estado" id="todos" value="" 
                                            {{ !request('estado') ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-secondary" for="todos">
                                            <i class="fas fa-tasks"></i> Todos
                                        </label>

                                        <input type="radio" class="btn-check" name="estado" id="pendiente" 
                                            value="pendiente" {{ request('estado') == 'pendiente' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-warning" for="pendiente">
                                            <i class="fas fa-clock"></i> Pendientes
                                        </label>

                                        <input type="radio" class="btn-check" name="estado" id="en_progreso" 
                                            value="en_progreso" {{ request('estado') == 'en_progreso' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary" for="en_progreso">
                                            <i class="fas fa-spinner fa-spin"></i> En Progreso
                                        </label>

                                        <input type="radio" class="btn-check" name="estado" id="completado" 
                                            value="completado" {{ request('estado') == 'completado' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-success" for="completado">
                                            <i class="fas fa-check-circle"></i> Completados
                                        </label>

                                        <input type="radio" class="btn-check" name="estado" id="cancelado" 
                                            value="cancelado" {{ request('estado') == 'cancelado' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-danger" for="cancelado">
                                            <i class="fas fa-times-circle"></i> Cancelados
                                        </label>
                                    </div>
                                </div>

                                <!-- Implementación -->
                                <div class="col-md-4">
                                    <label class="filter-label mb-2">Implementación</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <input type="radio" class="btn-check" name="implementado" id="todos_imp" 
                                            value="" {{ !request('implementado') ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-secondary" for="todos_imp">
                                            <i class="fas fa-globe"></i> Todos
                                        </label>

                                        <input type="radio" class="btn-check" name="implementado" id="esta_semana_imp" 
                                            value="esta_semana" {{ request('implementado') == 'esta_semana' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-info" for="esta_semana_imp">
                                            <i class="fas fa-calendar-week"></i> Esta Semana
                                        </label>

                                        <input type="radio" class="btn-check" name="implementado" id="este_mes_imp" 
                                            value="este_mes" {{ request('implementado') == 'este_mes' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary" for="este_mes_imp">
                                            <i class="fas fa-calendar-alt"></i> Este Mes
                                        </label>

                                        <input type="radio" class="btn-check" name="implementado" id="implementado" 
                                            value="implementado" {{ request('implementado') == 'implementado' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-success" for="implementado">
                                            <i class="fas fa-check-circle"></i> Implementados
                                        </label>

                                        <input type="radio" class="btn-check" name="implementado" id="sin_implementar" 
                                            value="sin_implementar" {{ request('implementado') == 'sin_implementar' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-warning" for="sin_implementar">
                                            <i class="fas fa-hourglass-half"></i> Sin Implementar
                                        </label>
                                    </div>
                                </div>

                                <!-- Periodo -->
                                <div class="col-md-4">
                                    <label class="filter-label mb-2">Fecha de Entrega</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <input type="radio" class="btn-check" name="periodo" id="todos_fecha" 
                                            value="" {{ !request('periodo') ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-secondary" for="todos_fecha">
                                            <i class="fas fa-calendar"></i> Todas
                                        </label>

                                        <input type="radio" class="btn-check" name="periodo" id="esta_semana" 
                                            value="semana" {{ request('periodo') == 'semana' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-info" for="esta_semana">
                                            <i class="fas fa-calendar-week"></i> Esta Semana
                                        </label>

                                        <input type="radio" class="btn-check" name="periodo" id="este_mes" 
                                            value="mes" {{ request('periodo') == 'mes' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-primary" for="este_mes">
                                            <i class="fas fa-calendar-alt"></i> Este Mes
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
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
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm"
                                                    onclick="manageClients({{ $project->id }})">
                                                <i class="fas fa-users"></i> Clientes
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
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id">
                            <option value="">Seleccionar cliente</option>
                            @foreach($project->clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

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

<!-- Modal para gestionar clientes -->
<div class="modal fade" id="manageClientsModal" tabindex="-1" aria-labelledby="manageClientsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageClientsModalLabel">Gestionar Clientes del Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <h6>Clientes Asociados</h6>
                        <div class="list-group" id="clientesAsociados">
                            <!-- Lista de clientes asociados -->
                        </div>
                    </div>
                    <div class="col">
                        <h6>Clientes Disponibles</h6>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchClientes" placeholder="Buscar clientes...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="list-group" id="clientesDisponibles">
                            <!-- Lista de clientes disponibles -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeClientsModal()">Cerrar</button>
            </div>
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

    const filterForm = document.getElementById('filterForm');
    const radioButtons = filterForm.querySelectorAll('input[type="radio"]');
    
    radioButtons.forEach(radio => {
        radio.addEventListener('change', () => {
            filterForm.submit();
        });
    });
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
    
    // Cargar los clientes del proyecto
    fetch(`/projects/${projectId}/clients`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        const clienteSelect = document.getElementById('cliente_id');
        clienteSelect.innerHTML = '<option value="">Seleccionar cliente</option>';
        
        data.associated.forEach(cliente => {
            clienteSelect.innerHTML += `
                <option value="${cliente.id}">${cliente.nombre}</option>
            `;
        });
        
        const modal = new bootstrap.Modal(document.getElementById('createBalanceModal'));
        modal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los clientes del proyecto');
    });
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

function manageClients(projectId) {
    // Remover cualquier backdrop existente
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';

    const modal = new bootstrap.Modal(document.getElementById('manageClientsModal'));
    
    // Cargar clientes asociados y disponibles
    fetch(`/projects/${projectId}/clients`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        const clientesAsociados = document.getElementById('clientesAsociados');
        const clientesDisponibles = document.getElementById('clientesDisponibles');
        
        // Limpiar listas
        clientesAsociados.innerHTML = '';
        clientesDisponibles.innerHTML = '';
        
        // Mostrar clientes asociados
        data.associated.forEach(cliente => {
            clientesAsociados.innerHTML += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    ${cliente.nombre}
                    <button class="btn btn-sm btn-danger" onclick="removeClient(${projectId}, ${cliente.id})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        
        // Mostrar clientes disponibles
        data.available.forEach(cliente => {
            clientesDisponibles.innerHTML += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    ${cliente.nombre}
                    <button class="btn btn-sm btn-success" onclick="addClient(${projectId}, ${cliente.id})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            `;
        });

        // Agregar evento para limpiar el modal al cerrarse
        const modalElement = document.getElementById('manageClientsModal');
        modalElement.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });

        modal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los clientes');
    });
}

// Agregar función para cerrar el modal correctamente
function closeClientsModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('manageClientsModal'));
    if (modal) {
        modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
}

// Modificar las funciones de addClient y removeClient para usar el nuevo cierre
function addClient(projectId, clienteId) {
    fetch(`/projects/${projectId}/clients/${clienteId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            closeClientsModal(); // Cerrar el modal actual
            manageClients(projectId); // Recargar las listas
        } else {
            throw new Error('Error al agregar el cliente');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar el cliente');
    });
}

function removeClient(projectId, clienteId) {
    if (!confirm('¿Estás seguro de querer eliminar este cliente del proyecto?')) {
        return;
    }
    
    fetch(`/projects/${projectId}/clients/${clienteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            closeClientsModal(); // Cerrar el modal actual
            manageClients(projectId); // Recargar las listas
        } else {
            throw new Error('Error al eliminar el cliente');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el cliente');
    });
}

// Búsqueda de clientes
document.getElementById('searchClientes').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const clientesItems = document.querySelectorAll('#clientesDisponibles .list-group-item');
    
    clientesItems.forEach(item => {
        const clienteName = item.textContent.toLowerCase();
        item.style.display = clienteName.includes(searchTerm) ? '' : 'none';
    });
});
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
    
    /* Estilos actualizados para los botones */
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .btn-group .btn {
        flex: 1 1 auto;
        min-width: calc(33.333% - 0.25rem);
        white-space: nowrap;
        padding: 0.25rem;
        font-size: 0.75rem;
    }
    
    .btn-group form {
        flex: 1 1 auto;
        min-width: calc(33.333% - 0.25rem);
    }
    
    .btn-group form .btn {
        width: 100%;
    }

    .filter-label {
        font-weight: 600;
        color: #444;
        font-size: 0.9rem;
    }

    .btn-check + .btn {
        transition: all 0.2s ease;
    }

    .btn-check:checked + .btn {
        transform: scale(1.05);
    }

    .btn-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }

    .btn i {
        font-size: 0.8rem;
    }

    /* Estilos para estados activos */
    .btn-check:checked + .btn-outline-secondary {
        background-color: #6c757d;
        color: white;
    }
    .btn-check:checked + .btn-outline-warning {
        background-color: #ffc107;
        color: black;
    }
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
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
    .btn-check:checked + .btn-outline-info {
        background-color: #0dcaf0;
        color: white;
    }

    /* Estilos para la gestión de clientes */
    .list-group {
        max-height: 300px;
        overflow-y: auto;
    }

    .list-group-item {
        transition: background-color 0.2s;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .list-group-item button {
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .list-group-item:hover button {
        opacity: 1;
    }

    /* Ajustes responsivos para las tarjetas */
    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
        }
        
        .btn-group .btn,
        .btn-group form {
            min-width: 100%;
        }
    }
</style>
@endpush