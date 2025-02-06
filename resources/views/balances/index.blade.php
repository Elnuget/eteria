@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Listado de Balances</span>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createBalanceModal">
                        Nuevo Balance
                    </button>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @forelse ($balances as $balance)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-transparent">
                                        <h5 class="card-title mb-0">{{ $balance->proyecto->nombre }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted">Fecha de Generación:</small><br>
                                            {{ $balance->fecha_generacion->format('d/m/Y') }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Tipo de Saldo:</small><br>
                                            <span class="badge bg-{{ $balance->tipo_saldo === 'anual' ? 'primary' : 
                                                ($balance->tipo_saldo === 'mensual' ? 'info' : 'warning') }}">
                                                {{ ucfirst($balance->tipo_saldo) }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Estado:</small><br>
                                            <span class="badge bg-{{ $balance->pagado_completo ? 'success' : 'danger' }}">
                                                {{ $balance->pagado_completo ? 'Pagado' : 'Pendiente' }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Monto Total:</small><br>
                                            ${{ number_format($balance->monto, 2) }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Pagado:</small><br>
                                            ${{ number_format($balance->monto_pagado, 2) }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Pendiente:</small><br>
                                            ${{ number_format($balance->monto_pendiente, 2) }}
                                        </div>
                                        @if($balance->motivo)
                                            <div class="mb-2">
                                                <small class="text-muted">Motivo:</small><br>
                                                {{ Str::limit($balance->motivo, 100) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm"
                                                    onclick="editBalance({{ $balance->id }})">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <form action="{{ route('balances.destroy', $balance) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('¿Estás seguro de eliminar este balance?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    No hay balances registrados.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $balances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear balance -->
<div class="modal fade" id="createBalanceModal" tabindex="-1" aria-labelledby="createBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBalanceModalLabel">Crear Nuevo Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('balances.store') }}" method="POST" id="createBalanceForm">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="proyecto_id" class="form-label">Proyecto *</label>
                        <select class="form-select @error('proyecto_id') is-invalid @enderror" 
                            id="proyecto_id" name="proyecto_id" required>
                            <option value="">Seleccionar proyecto</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_generacion" class="form-label">Fecha de Generación *</label>
                        <input type="date" class="form-control" id="fecha_generacion" name="fecha_generacion" required 
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="tipo_saldo" class="form-label">Tipo de Saldo *</label>
                        <select class="form-select" id="tipo_saldo" name="tipo_saldo" required>
                            <option value="anual">Anual</option>
                            <option value="mensual">Mensual</option>
                            <option value="unico">Único</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto Total *</label>
                        <input type="number" step="0.01" class="form-control" id="monto" name="monto" required 
                            onchange="actualizarMontoPendiente()" value="0">
                    </div>

                    <div class="mb-3">
                        <label for="monto_pagado" class="form-label">Monto Pagado *</label>
                        <input type="number" step="0.01" class="form-control" id="monto_pagado" name="monto_pagado" required 
                            onchange="actualizarMontoPendiente()" value="0">
                    </div>

                    <div class="mb-3">
                        <label for="monto_pendiente" class="form-label">Monto Pendiente *</label>
                        <input type="number" step="0.01" class="form-control" id="monto_pendiente" name="monto_pendiente" required readonly>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pagado_completo" name="pagado_completo">
                            <label class="form-check-label" for="pagado_completo">
                                Pagado Completo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar balance -->
<div class="modal fade" id="editBalanceModal" tabindex="-1" aria-labelledby="editBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBalanceModalLabel">Editar Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editBalanceForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_proyecto_id" class="form-label">Proyecto *</label>
                        <select class="form-select" id="edit_proyecto_id" name="proyecto_id" required>
                            <option value="">Seleccionar proyecto</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_fecha_generacion" class="form-label">Fecha de Generación *</label>
                        <input type="date" class="form-control" id="edit_fecha_generacion" name="fecha_generacion" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tipo_saldo" class="form-label">Tipo de Saldo *</label>
                        <select class="form-select" id="edit_tipo_saldo" name="tipo_saldo" required>
                            <option value="anual">Anual</option>
                            <option value="mensual">Mensual</option>
                            <option value="unico">Único</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_monto" class="form-label">Monto Total *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_monto" name="monto" required onchange="actualizarMontoPendienteEdit()">
                    </div>
                    <div class="mb-3">
                        <label for="edit_monto_pagado" class="form-label">Monto Pagado *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_monto_pagado" name="monto_pagado" required onchange="actualizarMontoPendienteEdit()">
                    </div>
                    <div class="mb-3">
                        <label for="edit_monto_pendiente" class="form-label">Monto Pendiente *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_monto_pendiente" name="monto_pendiente" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_motivo" class="form-label">Motivo</label>
                        <textarea class="form-control" id="edit_motivo" name="motivo" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_pagado_completo" name="pagado_completo">
                        <label class="form-check-label" for="edit_pagado_completo">Pagado Completo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let editBalanceModal = null;

document.addEventListener('DOMContentLoaded', function() {
    editBalanceModal = new bootstrap.Modal(document.getElementById('editBalanceModal'));
    
    @if($errors->any())
        var createBalanceModal = new bootstrap.Modal(document.getElementById('createBalanceModal'));
        createBalanceModal.show();
    @endif
});

function editBalance(balanceId) {
    const modalElement = document.getElementById('editBalanceModal');
    
    if (editBalanceModal) {
        editBalanceModal.show();
    } else {
        editBalanceModal = new bootstrap.Modal(modalElement);
        editBalanceModal.show();
    }

    fetch(`/balances/${balanceId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        const form = document.getElementById('editBalanceForm');
        form.action = `/balances/${balanceId}`;

        // Formatear la fecha de generación
        const fechaGeneracion = new Date(data.fecha_generacion);
        const formattedFechaGeneracion = fechaGeneracion.toISOString().split('T')[0];

        // Llenar los campos con los datos del balance
        document.getElementById('edit_proyecto_id').value = data.proyecto_id;
        document.getElementById('edit_monto').value = data.monto;
        document.getElementById('edit_monto_pagado').value = data.monto_pagado;
        document.getElementById('edit_monto_pendiente').value = data.monto_pendiente;
        document.getElementById('edit_fecha_generacion').value = formattedFechaGeneracion;
        document.getElementById('edit_tipo_saldo').value = data.tipo_saldo;
        document.getElementById('edit_motivo').value = data.motivo;
        document.getElementById('edit_pagado_completo').checked = data.pagado_completo;

        actualizarMontoPendienteEdit();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los datos del balance');
    });
}

function actualizarMontoPendiente() {
    const montoTotal = parseFloat(document.getElementById('monto').value) || 0;
    const montoPagado = parseFloat(document.getElementById('monto_pagado').value) || 0;
    const montoPendiente = montoTotal - montoPagado;
    document.getElementById('monto_pendiente').value = montoPendiente.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar monto pendiente al cargar la página
    actualizarMontoPendiente();
});

// Función para actualizar monto pendiente en el modal de edición
function actualizarMontoPendienteEdit() {
    const montoTotal = parseFloat(document.getElementById('edit_monto').value) || 0;
    const montoPagado = parseFloat(document.getElementById('edit_monto_pagado').value) || 0;
    const montoPendiente = montoTotal - montoPagado;
    document.getElementById('edit_monto_pendiente').value = montoPendiente.toFixed(2);
}
</script>
@endpush

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .btn-group .btn {
        flex: 1;
    }
</style>
@endpush