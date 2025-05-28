@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-eye"></i>
                        Detalle del Registro de Contabilidad
                    </h4>
                    <div>
                        <a href="{{ route('contabilidad.edit', $contabilidad) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i>
                            Editar
                        </a>
                        <a href="{{ route('contabilidad.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-calendar text-primary"></i>
                                        Fecha
                                    </h6>
                                    <p class="card-text h5">{{ $contabilidad->fecha->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-user text-secondary"></i>
                                        Usuario
                                    </h6>
                                    <p class="card-text h5">{{ $contabilidad->usuario->name ?? 'Usuario no encontrado' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-comment text-info"></i>
                                        Motivo
                                    </h6>
                                    <p class="card-text h5">{{ $contabilidad->motivo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-{{ $contabilidad->valor >= 0 ? 'success' : 'danger' }} text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">
                                        <i class="fas fa-dollar-sign"></i>
                                        Valor
                                    </h6>
                                    <h2 class="card-text">
                                        ${{ number_format($contabilidad->valor, 2) }}
                                    </h2>
                                    <small>
                                        {{ $contabilidad->valor >= 0 ? 'Ingreso' : 'Gasto' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-clock text-warning"></i>
                                        Fecha de Registro
                                    </h6>
                                    <p class="card-text">{{ $contabilidad->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-sync text-success"></i>
                                        Última Actualización
                                    </h6>
                                    <p class="card-text">{{ $contabilidad->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <form action="{{ route('contabilidad.destroy', $contabilidad) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    Eliminar Registro
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
