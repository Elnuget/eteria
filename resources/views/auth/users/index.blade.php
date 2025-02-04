@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Lista de Usuarios</span>
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Crear Usuario</a>
                </div>

                <div class="card-body">
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive" id="usersTableContainer">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Admin</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_admin ? 'success' : 'secondary' }}">
                                            {{ $user->is_admin ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal-{{ $user->id }}">
                                            Editar
                                        </button>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                        </form>
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

<!-- Modales para Editar Usuario -->
@foreach($users as $user)
<div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel-{{ $user->id }}">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name{{ $user->id }}" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email{{ $user->id }}" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email{{ $user->id }}" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="password{{ $user->id }}" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password{{ $user->id }}" name="password">
                        <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number{{ $user->id }}" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="phone_number{{ $user->id }}" name="phone_number" value="{{ $user->phone_number }}">
                    </div>
                    <div class="mb-3">
                        <label for="is_admin{{ $user->id }}" class="form-label">Es Admin</label>
                        <select class="form-control" id="is_admin{{ $user->id }}" name="is_admin">
                            <option value="0" {{ !$user->is_admin ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $user->is_admin ? 'selected' : '' }}>Sí</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection 