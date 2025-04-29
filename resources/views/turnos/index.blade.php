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
                                                        <td>{{ $turno->contacto ? $turno->contacto->numero : ($turno->contactoWeb ? $turno->contactoWeb->email : 'N/A') }}</td>
                                                        <td>{{ $turno->contacto ? $turno->contacto->nombre : ($turno->contactoWeb ? $turno->contactoWeb->nombre : 'Desconocido') }}</td>
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
                                                        <td>{{ $turno->contacto ? $turno->contacto->numero : ($turno->contactoWeb ? $turno->contactoWeb->email : 'N/A') }}</td>
                                                        <td>{{ $turno->contacto ? $turno->contacto->nombre : ($turno->contactoWeb ? $turno->contactoWeb->nombre : 'Desconocido') }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/es.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendario');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: 'auto',
            headerToolbar: {
                left: 'prev',
                center: 'title',
                right: 'next today'
            },
            buttonText: {
                today: 'Hoy'
            },
            dayMaxEvents: true,
            events: [
                @foreach($turnos as $fecha => $turnosDia)
                    @foreach($turnosDia as $turno)
                    @php
                        // Determinar el nombre a mostrar
                        $nombreMostrar = $turno->contacto ? $turno->contacto->nombre : ($turno->contactoWeb ? $turno->contactoWeb->nombre : 'Desconocido');
                    @endphp
                    {
                        title: '{{ addslashes($nombreMostrar) }} - {{ addslashes($turno->motivo) }}', // Usar nombre determinado y escapar caracteres
                        start: '{{ $turno->fecha_turno->format('Y-m-d\TH:i:s') }}',
                        url: '#',
                        extendedProps: {
                            turnoId: {{ $turno->id }}
                        },
                        backgroundColor: '{{ $turno->contactoWeb ? "#28a745" : "#4f6df5" }}', // Verde para web, Azul para WhatsApp
                        borderColor: '{{ $turno->contactoWeb ? "#28a745" : "#4f6df5" }}' // Verde para web, Azul para WhatsApp
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
            },
            // Personalizar las celdas de día
            dayCellDidMount: function(info) {
                // Si es hoy, añadir clase especial
                if (info.isToday) {
                    info.el.classList.add('fc-day-today-custom');
                }
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
        padding: 15px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    #calendario {
        width: 100%;
        height: 100%;
        font-family: inherit;
    }
    
    /* Mejoras visuales para el calendario */
    .fc-theme-standard .fc-scrollgrid {
        border: none !important;
    }
    
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #eaeaea;
    }
    
    .fc-header-toolbar {
        margin-bottom: 1.5em !important;
    }
    
    .fc-col-header-cell {
        background-color: #f8f9fa;
        padding: 10px 0 !important;
    }
    
    .fc-daygrid-day-top {
        justify-content: center;
        margin-top: 5px;
    }
    
    .fc-daygrid-day-number {
        font-size: 0.9em;
        color: #555;
        text-decoration: none !important;
    }
    
    .fc-day-today {
        background-color: rgba(79, 109, 245, 0.08) !important;
    }
    
    .fc-day-today-custom .fc-daygrid-day-number {
        background-color: #4f6df5;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .fc-event {
        border-radius: 4px !important;
        padding: 3px 4px !important;
        font-size: 0.85em !important;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border: none !important;
        margin-bottom: 2px;
    }
    
    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .fc-button-primary {
        background-color: #4f6df5 !important;
        border-color: #4f6df5 !important;
        border-radius: 30px !important;
        padding: 8px 16px !important;
        font-weight: 500 !important;
        transition: all 0.2s !important;
    }
    
    .fc-button-primary:hover {
        background-color: #3a57d7 !important;
        box-shadow: 0 4px 8px rgba(79, 109, 245, 0.3) !important;
    }
    
    .fc-button-primary:focus {
        box-shadow: 0 0 0 0.2rem rgba(79, 109, 245, 0.4) !important;
    }
    
    .fc-toolbar-title {
        font-size: 1.5em !important;
        font-weight: 600 !important;
        color: #333;
    }
    
    /* Estilos responsivos mejorados */
    @media (max-width: 768px) {
        .row > .col-md-6 {
            margin-bottom: 20px;
        }
        
        .fc-header-toolbar {
            flex-direction: row !important;
            margin-bottom: 1em !important;
        }
        
        .fc-toolbar-title {
            font-size: 1.2em !important;
        }
        
        .fc-col-header-cell {
            padding: 5px 0 !important;
        }
        
        .fc-daygrid-day-events {
            min-height: auto !important;
        }
        
        .fc-event {
            margin-bottom: 1px;
            padding: 2px 3px !important;
            font-size: 0.8em !important;
        }
    }
    
    @media (max-width: 480px) {
        .fc-header-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .fc-toolbar-chunk {
            margin-bottom: 5px;
        }
        
        .fc-button {
            padding: 5px 10px !important;
            font-size: 0.9em !important;
        }
    }
</style>
@endpush 