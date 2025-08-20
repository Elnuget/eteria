@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>
                        Contactos del Chat Web
                    </h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($contactosWeb->isEmpty())
                        <div class="alert alert-light text-center">
                            No hay contactos web registrados.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Celular</th>
                                        <th>Email</th>
                                        <th>Fecha de Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contactosWeb as $contacto)
                                        <tr>
                                            <td>{{ $contacto->id }}</td>
                                            <td>{{ $contacto->nombre }}</td>
                                            <td>{{ $contacto->celular }}</td>
                                            <td>
                                                @if($contacto->email)
                                                    {{ $contacto->email }}
                                                @else
                                                    <span class="text-muted">Sin email</span>
                                                @endif
                                            </td>
                                            <td>{{ $contacto->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <form action="{{ route('contacto-webs.destroy', $contacto->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este contacto? Se eliminarán también todos sus mensajes de chat.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $contactosWeb->links() }} 
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endpush 