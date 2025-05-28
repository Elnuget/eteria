@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i>
                        Editar Registro de Contabilidad
                    </h4>
                    <a href="{{ route('contabilidad.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('contabilidad.update', $contabilidad) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha" class="form-label">
                                        <i class="fas fa-calendar"></i>
                                        Fecha <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('fecha') is-invalid @enderror" 
                                           id="fecha" 
                                           name="fecha" 
                                           value="{{ old('fecha', $contabilidad->fecha->format('Y-m-d')) }}" 
                                           required>
                                    @error('fecha')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="usuario_id" class="form-label">
                                        <i class="fas fa-user"></i>
                                        Usuario <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('usuario_id') is-invalid @enderror" 
                                            id="usuario_id" 
                                            name="usuario_id" 
                                            required>
                                        <option value="">Seleccionar usuario...</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" 
                                                    {{ (old('usuario_id', $contabilidad->usuario_id) == $usuario->id) ? 'selected' : '' }}>
                                                {{ $usuario->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('usuario_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="motivo" class="form-label">
                                <i class="fas fa-comment"></i>
                                Motivo <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('motivo') is-invalid @enderror" 
                                   id="motivo" 
                                   name="motivo" 
                                   value="{{ old('motivo', $contabilidad->motivo) }}" 
                                   placeholder="Ingrese el motivo del registro contable..."
                                   required>
                            @error('motivo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="valor" class="form-label">
                                <i class="fas fa-dollar-sign"></i>
                                Valor <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control @error('valor') is-invalid @enderror" 
                                       id="valor" 
                                       name="valor" 
                                       value="{{ old('valor', $contabilidad->valor) }}" 
                                       step="0.01" 
                                       placeholder="0.00"
                                       required>
                                @error('valor')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Ingrese valores positivos para ingresos y negativos para gastos.
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('contabilidad.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Actualizar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
