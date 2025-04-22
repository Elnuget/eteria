@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Gestión de Turnos</span>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTurnoModal">
                        <i class="fas fa-plus"></i> Nuevo Turno
                    </button>
                </div>

                <div class="card-body">
                    @foreach($turnos as $fecha => $turnosDia)
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">
                                <i class="fas fa-calendar-day"></i>
                                {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                                <span class="badge bg-primary ms-2">{{ $turnosDia->count() }} turnos</span>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Hora</th>
                                            <th>Número</th>
                                            <th>Nombre</th>
                                            <th>Motivo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($turnosDia as $turno)
                                            <tr>
                                                <td>{{ $turno->fecha_turno->format('H:i') }}</td>
                                                <td>{{ $turno->contacto->numero }}</td>
                                                <td>{{ $turno->contacto->nombre }}</td>
                                                <td>{{ $turno->motivo }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editTurnoModal" 
                                                            onclick="editTurno({{ $turno->id }}, '{{ $turno->fecha_turno }}', '{{ $turno->motivo }}')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('turnos.destroy', $turno->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este turno?')">
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createTurnoModal" tabindex="-1" aria-labelledby="createTurnoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('turnos.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createTurnoModalLabel">Crear Nuevo Turno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="contacto_id" class="form-label">Contacto</label>
                        <select class="form-select" id="contacto_id" name="contacto_id" required>
                            <option value="">Seleccione un contacto</option>
                            @foreach($contactos as $contacto)
                                <option value="{{ $contacto->id }}">
                                    {{ $contacto->nombre }} ({{ $contacto->numero }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_turno" class="form-label">Fecha y Hora</label>
                        <input type="datetime-local" class="form-control" id="fecha_turno" name="fecha_turno" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo</label>
                        <input type="text" class="form-control" id="motivo" name="motivo" required>
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
<div class="modal fade" id="editTurnoModal" tabindex="-1" aria-labelledby="editTurnoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTurnoForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editTurnoModalLabel">Editar Turno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_fecha_turno" class="form-label">Fecha y Hora</label>
                        <input type="datetime-local" class="form-control" id="edit_fecha_turno" name="fecha_turno" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_motivo" class="form-label">Motivo</label>
                        <input type="text" class="form-control" id="edit_motivo" name="motivo" required>
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

@endsection

@push('scripts')
<script>
    function editTurno(id, fecha_turno, motivo) {
        const form = document.getElementById('editTurnoForm');
        form.action = `/turnos/${id}`;
        document.getElementById('edit_fecha_turno').value = fecha_turno;
        document.getElementById('edit_motivo').value = motivo;
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