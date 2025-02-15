@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Saldos</h2>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBalanceModal">
                <i class="fas fa-plus"></i> Nuevo Saldo
            </button>
            <button type="button" class="btn btn-secondary" onclick="actualizarBalances()">
                <i class="fas fa-sync"></i> Actualizar Balances
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-white" role="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter me-2"></i> Filtros
                <i class="fas fa-chevron-down ms-2"></i>
            </div>
        </div>

        <div class="collapse show" id="filtrosCollapse">
            <div class="card-body pt-0">
                <div class="row">
                    <!-- Estado de Pago -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Estado de Pago</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['estado' => '']) }}" 
                               class="btn btn-sm {{ !request('estado') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-list"></i> Todos
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['estado' => 'pendiente']) }}" 
                               class="btn btn-sm {{ request('estado') == 'pendiente' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-clock"></i> Pendientes
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['estado' => 'pagado']) }}" 
                               class="btn btn-sm {{ request('estado') == 'pagado' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-check-circle"></i> Pagados
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['estado' => 'vencido']) }}" 
                               class="btn btn-sm {{ request('estado') == 'vencido' ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="fas fa-exclamation-circle"></i> Vencidos
                            </a>
                        </div>
                    </div>

                    <!-- Tipo de Saldo -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Tipo de Saldo</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => '']) }}" 
                               class="btn btn-sm {{ !request('tipo') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-layer-group"></i> Todos
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => 'mensual']) }}" 
                               class="btn btn-sm {{ request('tipo') == 'mensual' ? 'btn-info' : 'btn-outline-info' }}">
                                <i class="fas fa-calendar-alt"></i> Mensual
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => 'anual']) }}" 
                               class="btn btn-sm {{ request('tipo') == 'anual' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-calendar"></i> Anual
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => 'unico']) }}" 
                               class="btn btn-sm {{ request('tipo') == 'unico' ? 'btn-dark' : 'btn-outline-dark' }}">
                                <i class="fas fa-dot-circle"></i> Único
                            </a>
                        </div>
                    </div>

                    <!-- Período -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Período</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => '']) }}" 
                               class="btn btn-sm {{ !request('periodo') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-calendar"></i> Todos
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => 'mes_actual']) }}" 
                               class="btn btn-sm {{ request('periodo') == 'mes_actual' ? 'btn-info' : 'btn-outline-info' }}">
                                <i class="fas fa-calendar-week"></i> Mes Actual
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => 'mes_anterior']) }}" 
                               class="btn btn-sm {{ request('periodo') == 'mes_anterior' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-calendar-minus"></i> Mes Anterior
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => 'proximo_mes']) }}" 
                               class="btn btn-sm {{ request('periodo') == 'proximo_mes' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-calendar-plus"></i> Próximo Mes
                            </a>
                        </div>
                    </div>
                </div>

                @if(request()->anyFilled(['estado', 'tipo', 'periodo']))
                    <div class="mt-3 text-end">
                        <a href="{{ route('balances.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar filtros
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Listado de Saldos</span>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createBalanceModal">
                            Nuevo Saldo
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="actualizarBalances()">
                            Actualizar Balances
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Proyecto</th>
                                    <th>Cliente</th>
                                    <th>Fecha Generación</th>
                                    <th>Tipo Saldo</th>
                                    <th>Estado</th>
                                    <th>Monto Total</th>
                                    <th>Pagado</th>
                                    <th>Pendiente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($balances as $balance)
                                    <tr>
                                        <td>{{ $balance->id }}</td>
                                        <td>{{ $balance->proyecto ? $balance->proyecto->nombre : 'Sin proyecto' }}</td>
                                        <td>{{ $balance->cliente ? $balance->cliente->nombre : 'Sin cliente' }}</td>
                                        <td>{{ $balance->fecha_generacion->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $balance->tipo_saldo === 'anual' ? 'primary' : 
                                                ($balance->tipo_saldo === 'mensual' ? 'info' : 'warning') }}">
                                                {{ ucfirst($balance->tipo_saldo) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $balance->pagado_completo ? 'success' : 'danger' }}">
                                                {{ $balance->pagado_completo ? 'Pagado' : 'Pendiente' }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($balance->monto, 2) }}</td>
                                        <td>${{ number_format($balance->monto_pagado, 2) }}</td>
                                        <td>${{ number_format($balance->monto_pendiente, 2) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm"
                                                        onclick="editBalance({{ $balance->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-outline-success btn-sm"
                                                        onclick="createPayment({{ $balance->id }}, {{ $balance->monto_pendiente }})">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </button>
                                                <form action="{{ route('balances.destroy', $balance) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('¿Estás seguro de eliminar este balance?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No hay balances registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                        <label for="proyecto_id" class="form-label">Proyecto</label>
                        <select class="form-select @error('proyecto_id') is-invalid @enderror" 
                            id="proyecto_id" name="proyecto_id">
                            <option value="">Sin proyecto</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select @error('cliente_id') is-invalid @enderror" 
                            id="cliente_id" name="cliente_id">
                            <option value="">Sin cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
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
                        <label for="edit_proyecto_id" class="form-label">Proyecto</label>
                        <select class="form-select" id="edit_proyecto_id" name="proyecto_id">
                            <option value="">Sin proyecto</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="edit_cliente_id" name="cliente_id">
                            <option value="">Sin cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
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

<!-- Modal para crear pago -->
<div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPaymentModalLabel">Crear Nuevo Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="balance_id" name="balance_id">

                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto *</label>
                        <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label">Fecha de Pago *</label>
                        <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago">
                            <option value="">Seleccionar método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="deposito">Depósito</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Pago</button>
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
        document.getElementById('edit_cliente_id').value = data.cliente_id;
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

function createPayment(balanceId, montoPendiente) {
    // Establecer el ID del balance en el campo oculto
    document.getElementById('balance_id').value = balanceId;
    
    // Establecer el monto pendiente como valor por defecto en el campo de monto
    const montoInput = document.getElementById('createPaymentModal').querySelector('[name="monto"]');
    montoInput.value = montoPendiente.toFixed(2);
    montoInput.max = montoPendiente.toFixed(2);
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('createPaymentModal'));
    modal.show();
}

function actualizarBalances() {
    fetch('{{ route('balances.updateBalances') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar los balances');
    });
}
</script>
@endpush

@push('styles')
<style>
    /* Estilos para los botones */
    .btn-sm {
        padding: 0.35rem 0.8rem;   /* Padding más pequeño */
        font-size: 0.88rem;        /* Fuente más pequeña */
        border-radius: 18px;       /* Radio de borde más pequeño */
        white-space: nowrap;
        font-weight: 500;
        transition: all 0.2s ease;
        height: 32px;              /* Altura más pequeña */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 85px;           /* Ancho mínimo más pequeño */
    }

    .btn-sm i {
        font-size: 0.82rem;        /* Iconos más pequeños */
        margin-right: 0.35rem;     /* Menos espacio entre icono y texto */
    }

    /* Espaciado entre botones */
    .gap-1 {
        gap: 0.35rem !important;   /* Gap más pequeño */
    }

    /* Etiquetas de filtros */
    label {
        font-size: 0.95rem;        /* Etiquetas más pequeñas */
        font-weight: 600;
        color: #444;
        margin-bottom: 0.5rem;     /* Margen inferior más pequeño */
    }

    /* Contenedor de filtros */
    .card-body {
        padding: 1rem;             /* Padding más pequeño */
    }

    /* Ajustes de espaciado vertical */
    .col-md-4 {
        margin-top: 0.8rem;        /* Márgenes más pequeños */
        margin-bottom: 0.5rem;
    }

    /* Efectos hover mejorados */
    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Estilos para botones activos */
    .btn-secondary:not(.btn-outline-secondary) {
        background-color: #6c757d;
        color: white;
        font-weight: 600;
    }

    .btn-warning:not(.btn-outline-warning) {
        background-color: #ffc107;
        color: #000;
        font-weight: 600;
    }

    .btn-success:not(.btn-outline-success) {
        background-color: #198754;
        color: white;
        font-weight: 600;
    }

    .btn-danger:not(.btn-outline-danger) {
        background-color: #dc3545;
        color: white;
        font-weight: 600;
    }

    .btn-info:not(.btn-outline-info) {
        background-color: #0dcaf0;
        color: white;
        font-weight: 600;
    }

    .btn-primary:not(.btn-outline-primary) {
        background-color: #0d6efd;
        color: white;
        font-weight: 600;
    }

    .btn-dark:not(.btn-outline-dark) {
        background-color: #212529;
        color: white;
        font-weight: 600;
    }

    /* Contenedor de grupos de botones */
    .d-flex.flex-wrap {
        margin: -0.25rem;
    }

    .d-flex.flex-wrap > * {
        margin: 0.25rem;
    }

    /* Botón de limpiar filtros */
    .btn-outline-secondary {
        border-width: 2px;
    }

    /* Cabecera de filtros */
    .card-header {
        padding: 1rem 1.5rem;
        font-size: 1.1rem;
    }

    .card-header i {
        font-size: 1rem;
    }
</style>
@endpush