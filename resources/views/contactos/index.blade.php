@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {!! session('warning') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Gestión de Contactos</span>
                    <div>
                        <button type="button" class="btn btn-primary me-2" id="enviarSaludoBtn" disabled>
                            <i class="fas fa-paper-plane"></i> Enviar Saludo
                        </button>
                        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                            <i class="fas fa-file-excel"></i> Importar Excel
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createContactoModal">
                            <i class="fas fa-plus"></i> Nuevo Contacto
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Número</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Fecha de Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contactos as $contacto)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input contacto-checkbox" type="checkbox" 
                                                value="{{ $contacto->numero }}"
                                                {{ $contacto->estado === 'iniciado' ? 'disabled' : '' }}
                                                data-estado="{{ $contacto->estado }}">
                                        </div>
                                    </td>
                                    <td>{{ $contacto->numero }}</td>
                                    <td>{{ $contacto->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $contacto->estado === 'iniciado' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($contacto->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ $contacto->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editContactoModal" 
                                                onclick="editContacto({{ $contacto->id }}, '{{ $contacto->numero }}', '{{ $contacto->nombre }}', '{{ $contacto->estado }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('contactos.destroy', $contacto->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este contacto?')">
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
<div class="modal fade" id="createContactoModal" tabindex="-1" aria-labelledby="createContactoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('contactos.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createContactoModalLabel">Crear Nuevo Contacto</h5>
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
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="por iniciar">Por Iniciar</option>
                            <option value="iniciado">Iniciado</option>
                        </select>
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
<div class="modal fade" id="editContactoModal" tabindex="-1" aria-labelledby="editContactoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editContactoForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editContactoModalLabel">Editar Contacto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="edit_numero" name="numero" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre">
                    </div>
                    <div class="mb-3">
                        <label for="edit_estado" class="form-label">Estado</label>
                        <select class="form-select" id="edit_estado" name="estado" required>
                            <option value="por iniciar">Por Iniciar</option>
                            <option value="iniciado">Iniciado</option>
                        </select>
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

<!-- Modal Importar Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('contactos.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">Importar Contactos desde Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Archivo Excel</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                    </div>
                    <div class="alert alert-info">
                        <small>El archivo Excel debe contener las columnas: "numero" y "nombre"</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function editContacto(id, numero, nombre, estado) {
        const form = document.getElementById('editContactoForm');
        form.action = `/contactos/${id}`;
        document.getElementById('edit_numero').value = numero;
        document.getElementById('edit_nombre').value = nombre || '';
        document.getElementById('edit_estado').value = estado;
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

    // Manejar selección de contactos
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const contactoCheckboxes = document.querySelectorAll('.contacto-checkbox:not([disabled])');
        const enviarSaludoBtn = document.getElementById('enviarSaludoBtn');

        function updateEnviarSaludoBtn() {
            const checkedBoxes = document.querySelectorAll('.contacto-checkbox:checked').length;
            enviarSaludoBtn.disabled = checkedBoxes === 0;
        }

        selectAllCheckbox.addEventListener('change', function() {
            contactoCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateEnviarSaludoBtn();
        });

        contactoCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(contactoCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                updateEnviarSaludoBtn();
            });
        });

        enviarSaludoBtn.addEventListener('click', async function() {
            const selectedNumbers = Array.from(document.querySelectorAll('.contacto-checkbox:checked'))
                .map(cb => cb.value);

            if (!selectedNumbers.length) return;

            if (!confirm('¿Está seguro de enviar el saludo a los contactos seleccionados?')) return;

            try {
                const response = await fetch('/whatsapp/send-bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ numbers: selectedNumbers })
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    alert('Error al enviar los saludos: ' + result.message);
                }
            } catch (error) {
                alert('Error al enviar los saludos: ' + error.message);
            }
        });
    });
</script>
@endpush 