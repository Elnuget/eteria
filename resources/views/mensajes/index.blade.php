@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Gestión de Mensajes</span>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMensajeModal">
                        <i class="fas fa-plus"></i> Nuevo Mensaje
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Nombre</th>
                                    <th>Mensaje</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mensajes as $mensaje)
                                <tr>
                                    <td>{{ $mensaje->numero }}</td>
                                    <td>{{ $mensaje->nombre ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($mensaje->mensaje, 50) }}</td>
                                    <td>{{ ucfirst($mensaje->estado) }}</td>
                                    <td>{{ $mensaje->fecha->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#showMensajeModal" 
                                                onclick="showMensaje({{ $mensaje->id }}, '{{ $mensaje->numero }}', '{{ $mensaje->nombre }}', '{{ addslashes($mensaje->mensaje) }}', '{{ $mensaje->estado }}', '{{ $mensaje->fecha->format('d/m/Y H:i') }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form action="{{ route('mensajes.destroy', $mensaje->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este mensaje?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

<!-- Modal Crear -->
<div class="modal fade" id="createMensajeModal" tabindex="-1" aria-labelledby="createMensajeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('mensajes.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createMensajeModalLabel">Crear Nuevo Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="numero" name="numero" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre">
                    </div>
                    <div class="mb-3">
                        <label for="mensaje" class="form-label">Mensaje</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="estado" value="salida">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control" value="Salida" readonly>
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

<!-- Modal Mostrar -->
<div class="modal fade" id="showMensajeModal" tabindex="-1" aria-labelledby="showMensajeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showMensajeModalLabel">Detalles del Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold">Número:</label>
                    <p id="show_numero"></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Nombre:</label>
                    <p id="show_nombre"></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Mensaje:</label>
                    <p id="show_mensaje" style="white-space: pre-wrap;"></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Estado:</label>
                    <p id="show_estado"></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Fecha:</label>
                    <p id="show_fecha"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showMensaje(id, numero, nombre, mensaje, estado, fecha) {
        document.getElementById('show_numero').textContent = numero;
        document.getElementById('show_nombre').textContent = nombre || 'N/A';
        document.getElementById('show_mensaje').textContent = mensaje;
        document.getElementById('show_estado').textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
        document.getElementById('show_fecha').textContent = fecha;
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