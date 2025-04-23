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
                    <div class="row">
                        <!-- Tabla de turnos (izquierda) -->
                        <div class="col-md-6">
                            @php
                                // Obtener la fecha de hoy en formato Y-m-d
                                $hoy = \Carbon\Carbon::today()->format('Y-m-d');
                                
                                // Ordenar las fechas en orden descendente (de mayor a menor)
                                $fechasOrdenadas = $turnos->keys()->sort(function($a, $b) {
                                    return strtotime($b) <=> strtotime($a);
                                });
                                
                                // Filtrar para excluir la fecha de hoy (evitar duplicados)
                                $fechasOrdenadasSinHoy = $fechasOrdenadas->filter(function($fecha) use ($hoy) {
                                    return $fecha != $hoy;
                                });
                            @endphp
                            
                            <!-- Turnos de hoy -->
                            @if($turnos->has($hoy))
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2 bg-success text-white p-2 rounded">
                                        <i class="fas fa-calendar-check"></i>
                                        TURNOS PARA HOY ({{ \Carbon\Carbon::parse($hoy)->format('d/m/Y') }})
                                        <span class="badge bg-light text-dark ms-2">{{ $turnos[$hoy]->count() }} turnos</span>
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Hora</th>
                                                    <th>Número</th>
                                                    <th>Nombre</th>
                                                    <th>Motivo</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($turnos[$hoy] as $turno)
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
                            @else
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle"></i> No hay turnos programados para hoy.
                                </div>
                            @endif
                            
                            <!-- Resto de turnos (excluyendo hoy) -->
                            @foreach($fechasOrdenadasSinHoy as $fecha)
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2">
                                        <i class="fas fa-calendar-day"></i>
                                        {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                                        <span class="badge bg-primary ms-2">{{ $turnos[$fecha]->count() }} turnos</span>
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
                                                @foreach($turnos[$fecha] as $turno)
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
                        
                        <!-- Calendario (derecha) -->
                        <div class="col-md-6">
                            <div class="calendario-container">
                                <div id="calendario"></div>
                            </div>
                        </div>
                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendario');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                @foreach($turnos as $fecha => $turnosDia)
                    @foreach($turnosDia as $turno)
                    {
                        title: '{{ $turno->contacto->nombre }} - {{ $turno->motivo }}',
                        start: '{{ $turno->fecha_turno->format('Y-m-d\TH:i:s') }}',
                        url: '#',
                        extendedProps: {
                            turnoId: {{ $turno->id }}
                        }
                    },
                    @endforeach
                @endforeach
            ],
            eventClick: function(info) {
                // Prevenir navegación por defecto
                info.jsEvent.preventDefault();
                
                // Obtener el ID del turno
                const turnoId = info.event.extendedProps.turnoId;
                
                // Buscar el botón de editar correspondiente y hacer clic
                document.querySelector(`button[onclick*="${turnoId}"]`).click();
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }
        });
        calendar.render();
        
        // Ajustar altura en cambio de tamaño de ventana
        window.addEventListener('resize', function() {
            calendar.updateSize();
        });
    });

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
<style>
    .calendario-container {
        padding: 10px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    #calendario {
        width: 100%;
        height: 100%;
    }
    
    /* Estilos responsivos */
    @media (max-width: 768px) {
        .row > .col-md-6 {
            margin-bottom: 20px;
        }
    }
    
    .fc-event {
        cursor: pointer;
    }
</style>
@endpush 