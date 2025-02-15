@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestión de Clientes</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClienteModal">
                        <i class="fas fa-plus"></i> Nuevo Cliente
                    </button>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Dirección</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientes as $cliente)
                                    <tr>
                                        <td>{{ $cliente->nombre }}</td>
                                        <td>{{ $cliente->correo }}</td>
                                        <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                                        <td>{{ $cliente->direccion ?? 'N/A' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#showClienteModal{{ $cliente->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editClienteModal{{ $cliente->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteClienteModal{{ $cliente->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal Ver Cliente -->
                                    <div class="modal fade" id="showClienteModal{{ $cliente->id }}" tabindex="-1" aria-labelledby="showClienteModalLabel{{ $cliente->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="showClienteModalLabel{{ $cliente->id }}">Detalles del Cliente</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <dl class="row">
                                                        <dt class="col-sm-3">Nombre:</dt>
                                                        <dd class="col-sm-9">{{ $cliente->nombre }}</dd>
                                                        
                                                        <dt class="col-sm-3">Correo:</dt>
                                                        <dd class="col-sm-9">{{ $cliente->correo }}</dd>
                                                        
                                                        <dt class="col-sm-3">Teléfono:</dt>
                                                        <dd class="col-sm-9">{{ $cliente->telefono ?? 'No especificado' }}</dd>
                                                        
                                                        <dt class="col-sm-3">Dirección:</dt>
                                                        <dd class="col-sm-9">{{ $cliente->direccion ?? 'No especificada' }}</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Editar Cliente -->
                                    <div class="modal fade" id="editClienteModal{{ $cliente->id }}" tabindex="-1" aria-labelledby="editClienteModalLabel{{ $cliente->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editClienteModalLabel{{ $cliente->id }}">Editar Cliente</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="nombre" class="form-label">Nombre</label>
                                                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $cliente->nombre }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="correo" class="form-label">Correo</label>
                                                            <input type="email" class="form-control" id="correo" name="correo" value="{{ $cliente->correo }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="telefono" class="form-label">Teléfono</label>
                                                            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $cliente->telefono }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="direccion" class="form-label">Dirección</label>
                                                            <textarea class="form-control" id="direccion" name="direccion" rows="3">{{ $cliente->direccion }}</textarea>
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

                                    <!-- Modal Eliminar Cliente -->
                                    <div class="modal fade" id="deleteClienteModal{{ $cliente->id }}" tabindex="-1" aria-labelledby="deleteClienteModalLabel{{ $cliente->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteClienteModalLabel{{ $cliente->id }}">Confirmar Eliminación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Está seguro que desea eliminar al cliente <strong>{{ $cliente->nombre }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No hay clientes registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $clientes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Cliente -->
<div class="modal fade" id="createClienteModal" tabindex="-1" aria-labelledby="createClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createClienteModalLabel">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="3"></textarea>
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

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('{{ old('modal_id', 'createClienteModal') }}'));
            modal.show();
        });
    </script>
@endif
@endsection 