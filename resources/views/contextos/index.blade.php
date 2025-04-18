@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Gestión de Contextos</span>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createContextoModal">
                        <i class="fas fa-plus"></i> Nuevo Contexto
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Contexto</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contextos as $contexto)
                                <tr>
                                    <td>{{ $contexto->id }}</td>
                                    <td>{{ Str::limit($contexto->contexto, 100) }}</td>
                                    <td>{{ $contexto->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editContextoModal" 
                                                onclick="editContexto({{ $contexto->id }}, '{{ addslashes($contexto->contexto) }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteContextoModal" 
                                                onclick="setDeleteId({{ $contexto->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createContextoModal" tabindex="-1" aria-labelledby="createContextoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createContextoForm" action="{{ route('contextos.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createContextoModalLabel">Crear Nuevo Contexto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="contexto" class="form-label">Contexto</label>
                        <textarea class="form-control" id="contexto" name="contexto" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editContextoModal" tabindex="-1" aria-labelledby="editContextoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editContextoForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editContextoModalLabel">Editar Contexto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_contexto" class="form-label">Contexto</label>
                        <textarea class="form-control" id="edit_contexto" name="contexto" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="deleteContextoModal" tabindex="-1" aria-labelledby="deleteContextoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteContextoForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteContextoModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Está seguro que desea eliminar este contexto?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function editContexto(id, contexto) {
        const form = document.getElementById('editContextoForm');
        form.action = `/contextos/${id}`;
        document.getElementById('edit_contexto').value = contexto;
    }

    function setDeleteId(id) {
        const form = document.getElementById('deleteContextoForm');
        form.action = `/contextos/${id}`;
    }

    // Mostrar mensajes de error de validación
    @if($errors->any())
        let errorMessages = '';
        @foreach($errors->all() as $error)
            errorMessages += '{{ $error }}\n';
        @endforeach
        alert(errorMessages);
    @endif

    // Mostrar mensajes de éxito
    @if(session('success'))
        alert('{{ session('success') }}');
    @endif
</script>
@endpush 