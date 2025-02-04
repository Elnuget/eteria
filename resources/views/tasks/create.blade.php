@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nueva Tarea</h5>
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

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
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en progreso" {{ old('estado') == 'en progreso' ? 'selected' : '' }}>En Progreso</option>
                                <option value="completada" {{ old('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="desarrollado_por" class="form-label">Desarrollador</label>
                            <select class="form-select @error('desarrollado_por') is-invalid @enderror" 
                                id="desarrollado_por" name="desarrollado_por">
                                <option value="">Seleccionar desarrollador</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('desarrollado_por') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('desarrollado_por')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tiempo_estimado" class="form-label">Tiempo Estimado (minutos)</label>
                                    <input type="number" class="form-control @error('tiempo_estimado') is-invalid @enderror" 
                                        id="tiempo_estimado" name="tiempo_estimado" value="{{ old('tiempo_estimado') }}" min="0">
                                    @error('tiempo_estimado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tiempo_real" class="form-label">Tiempo Real (minutos)</label>
                                    <input type="number" class="form-control @error('tiempo_real') is-invalid @enderror" 
                                        id="tiempo_real" name="tiempo_real" value="{{ old('tiempo_real') }}" min="0">
                                    @error('tiempo_real')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Tarea
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 