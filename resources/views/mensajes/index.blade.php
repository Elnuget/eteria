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
                    <div class="accordion" id="mensajesAccordion">
                        @foreach($mensajesAgrupados as $numero => $mensajes)
                            @php
                                $primerMensaje = $mensajes->first();
                                $totalMensajes = $mensajes->count();
                                $ultimoMensaje = $mensajes->first(); // El más reciente debido al orderBy
                                $id = 'collapse' . str_replace(['+', ' '], '', $numero);
                                $contacto = $primerMensaje->contacto;
                            @endphp
                            <div class="accordion-item mb-3">
                                <div class="accordion-header w-100" role="button">
                                    <div class="accordion-button collapsed" onclick="toggleAccordion(this)">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <div>
                                                <strong>{{ $numero }}</strong>
                                                <span class="ms-3">{{ $contacto->nombre }}</span>
                                            </div>
                                            <div class="ms-auto me-3 d-flex align-items-center">
                                                <span class="badge bg-primary me-2">{{ $totalMensajes }} mensajes</span>
                                                <span class="text-muted me-3">Último: {{ $ultimoMensaje->fecha->format('d/m/Y H:i') }}</span>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        onclick="event.stopPropagation(); confirmarEliminarConversacion('{{ $contacto->id }}')"
                                                        title="Eliminar toda la conversación">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="{{ $id }}" class="accordion-collapse collapse" data-bs-parent="#mensajesAccordion">
                                    <div class="accordion-body">
                                        <form class="mb-3 mensaje-rapido-form" onsubmit="enviarMensajeRapido(event, '{{ $contacto->id }}')">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Escribir mensaje..." required>
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </form>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Mensaje</th>
                                                        <th>Estado</th>
                                                        <th>Fecha</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($mensajes as $mensaje)
                                                        <tr class="mensaje-{{ $mensaje->estado }}">
                                                            <td class="mensaje-contenido">
                                                                <div class="mensaje-burbuja {{ $mensaje->estado }}">
                                                                    {{ $mensaje->mensaje }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge {{ $mensaje->estado === 'entrada' ? 'bg-success' : 'bg-info' }}">
                                                                    {{ ucfirst($mensaje->estado) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $mensaje->fecha->format('d/m/Y H:i') }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#showMensajeModal" 
                                                                        onclick="showMensaje({{ $mensaje->id }}, '{{ $mensaje->contacto->numero }}', '{{ $mensaje->contacto->nombre }}', '{{ addslashes($mensaje->mensaje) }}', '{{ $mensaje->estado }}', '{{ $mensaje->fecha->format('d/m/Y H:i') }}')">
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
                        @endforeach
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

@push('styles')
<style>
.accordion-header {
    cursor: pointer;
    user-select: none;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #000;
}

.accordion-button:focus {
    box-shadow: none;
}

.badge {
    font-size: 0.85em;
}

.mensaje-rapido-form {
    max-width: 100%;
    margin-top: 1rem;
}

.mensaje-rapido-form .input-group {
    width: 100%;
}

.mensaje-burbuja {
    padding: 8px 12px;
    border-radius: 12px;
    max-width: 80%;
    display: inline-block;
    word-wrap: break-word;
}

.mensaje-burbuja.entrada {
    background-color: #e3f2fd;
    color: #0d47a1;
    border-top-left-radius: 2px;
}

.mensaje-burbuja.salida {
    background-color: #e8f5e9;
    color: #1b5e20;
    border-top-right-radius: 2px;
}

.mensaje-salida .mensaje-contenido {
    text-align: right;
}

.mensaje-entrada .mensaje-contenido {
    text-align: left;
}

tr.mensaje-entrada {
    background-color: rgba(227, 242, 253, 0.1);
}

tr.mensaje-salida {
    background-color: rgba(232, 245, 233, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
    function showMensaje(id, numero, nombre, mensaje, estado, fecha) {
        document.getElementById('show_numero').textContent = numero;
        document.getElementById('show_nombre').textContent = nombre;
        document.getElementById('show_mensaje').textContent = mensaje;
        document.getElementById('show_estado').textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
        document.getElementById('show_fecha').textContent = fecha;
    }

    function enviarMensajeRapido(event, contacto_id) {
        event.preventDefault();
        const form = event.target;
        const mensaje = form.querySelector('input').value;
        
        // Crear los datos para el envío
        const formData = new FormData();
        formData.append('contacto_id', contacto_id);
        formData.append('mensaje', mensaje);
        formData.append('estado', 'salida');
        formData.append('_token', '{{ csrf_token() }}');

        // Deshabilitar el formulario mientras se envía
        const submitButton = form.querySelector('button');
        const input = form.querySelector('input');
        submitButton.disabled = true;
        input.disabled = true;

        // Enviar el mensaje
        fetch('{{ route('mensajes.store') }}', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Recargar la página inmediatamente después de un envío exitoso
                window.location.href = '{{ route('mensajes.index') }}';
            } else {
                throw new Error('Error en la respuesta del servidor');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al enviar el mensaje');
            // Reactivar el formulario en caso de error
            submitButton.disabled = false;
            input.disabled = false;
        });
    }

    function toggleAccordion(header) {
        const accordionItem = header.closest('.accordion-item');
        const collapse = accordionItem.querySelector('.accordion-collapse');
        const button = header.querySelector('.accordion-button');
        
        // Toggle the collapse
        const bsCollapse = new bootstrap.Collapse(collapse, {
            toggle: true
        });
        
        // Toggle the button class
        button.classList.toggle('collapsed');
    }

    function confirmarEliminarConversacion(contactoId) {
        if (confirm('¿Está seguro de eliminar toda la conversación? Esta acción no se puede deshacer.')) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            fetch(`/mensajes/eliminar-conversacion/${contactoId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Error al eliminar la conversación');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la conversación');
            });
        }
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