@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Pagos</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
            <i class="fas fa-plus"></i> Nuevo Pago
        </button>
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
                    <!-- Método de Pago -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Método de Pago</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['metodo' => '']) }}" 
                               class="btn btn-sm {{ !request('metodo') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-list"></i> Todos
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['metodo' => 'efectivo']) }}" 
                               class="btn btn-sm {{ request('metodo') == 'efectivo' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-money-bill"></i> Efectivo
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['metodo' => 'transferencia']) }}" 
                               class="btn btn-sm {{ request('metodo') == 'transferencia' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-exchange-alt"></i> Transferencia
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['metodo' => 'deposito']) }}" 
                               class="btn btn-sm {{ request('metodo') == 'deposito' ? 'btn-info' : 'btn-outline-info' }}">
                                <i class="fas fa-university"></i> Depósito
                            </a>
                        </div>
                    </div>

                    <!-- Período de Pago -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Período de Pago</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => '']) }}" 
                               class="btn btn-sm {{ !request('periodo') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-calendar"></i> Todos
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => 'hoy']) }}" 
                               class="btn btn-sm {{ request('periodo') == 'hoy' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-calendar-day"></i> Hoy
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => 'semana']) }}" 
                               class="btn btn-sm {{ request('periodo') == 'semana' ? 'btn-info' : 'btn-outline-info' }}">
                                <i class="fas fa-calendar-week"></i> Esta Semana
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => 'mes']) }}" 
                               class="btn btn-sm {{ request('periodo') == 'mes' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-calendar-alt"></i> Este Mes
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => 'mes_anterior']) }}" 
                               class="btn btn-sm {{ request('periodo') == 'mes_anterior' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-calendar-minus"></i> Mes Anterior
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['periodo' => 'antiguos']) }}" 
                               class="btn btn-sm {{ request('periodo') == 'antiguos' ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="fas fa-history"></i> Más Antiguos
                            </a>
                        </div>
                    </div>

                    <!-- Monto -->
                    <div class="col-md-4 mt-3">
                        <label class="d-block mb-2">Rango de Monto</label>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ request()->fullUrlWithQuery(['monto' => '']) }}" 
                               class="btn btn-sm {{ !request('monto') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                <i class="fas fa-dollar-sign"></i> Todos
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['monto' => 'bajo']) }}" 
                               class="btn btn-sm {{ request('monto') == 'bajo' ? 'btn-success' : 'btn-outline-success' }}">
                                <i class="fas fa-angle-down"></i> 0-1000
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['monto' => 'medio']) }}" 
                               class="btn btn-sm {{ request('monto') == 'medio' ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-equals"></i> 1000-5000
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['monto' => 'alto']) }}" 
                               class="btn btn-sm {{ request('monto') == 'alto' ? 'btn-danger' : 'btn-outline-danger' }}">
                                <i class="fas fa-angle-up"></i> 5000+
                            </a>
                        </div>
                    </div>
                </div>

                @if(request()->anyFilled(['metodo', 'periodo', 'monto']))
                    <div class="mt-3 text-end">
                        <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <span>Listado de Pagos</span>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                        Nuevo Pago
                    </button>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @forelse ($payments as $payment)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-transparent">
                                        <h5 class="card-title mb-0">Pago #{{ $payment->id }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted">Balance ID:</small><br>
                                            {{ $payment->balance->id }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Monto:</small><br>
                                            ${{ number_format($payment->monto, 2) }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Fecha de Pago:</small><br>
                                            {{ $payment->fecha_pago->format('d/m/Y') }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Método de Pago:</small><br>
                                            {{ $payment->metodo_pago ?? 'N/A' }}
                                        </div>
                                        @if($payment->descripcion)
                                            <div class="mb-2">
                                                <small class="text-muted">Descripción:</small><br>
                                                {{ Str::limit($payment->descripcion, 100) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm"
                                                    onclick="editPayment({{ $payment->id }})">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <form action="{{ route('payments.destroy', $payment) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('¿Estás seguro de eliminar este pago?')">
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
                                    No hay pagos registrados.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
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
                    <div class="mb-3">
                        <label for="balance_id" class="form-label">Balance ID *</label>
                        <select class="form-select" id="balance_id" name="balance_id" required onchange="updateMonto()">
                            <option value="">Seleccionar balance</option>
                            @foreach($balances as $balance)
                                <option value="{{ $balance->id }}" data-pendiente="{{ $balance->monto_pendiente }}">{{ $balance->proyecto->nombre }} - {{ $balance->motivo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto *</label>
                        <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label">Fecha de Pago *</label>
                        <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
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

<!-- Modal para editar pago -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentModalLabel">Editar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editPaymentForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_balance_id" class="form-label">Balance ID *</label>
                        <select class="form-select" id="edit_balance_id" name="balance_id" required onchange="updateEditMonto()">
                            <option value="">Seleccionar balance</option>
                            @foreach($balances as $balance)
                                <option value="{{ $balance->id }}" data-pendiente="{{ $balance->monto_pendiente }}">{{ $balance->proyecto->nombre }} - {{ $balance->motivo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_monto" class="form-label">Monto *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_monto" name="monto" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_fecha_pago" class="form-label">Fecha de Pago *</label>
                        <input type="date" class="form-control" id="edit_fecha_pago" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_metodo_pago" class="form-label">Método de Pago</label>
                        <select class="form-select" id="edit_metodo_pago" name="metodo_pago">
                            <option value="">Seleccionar método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="deposito">Depósito</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let editPaymentModal = null;

document.addEventListener('DOMContentLoaded', function() {
    editPaymentModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
    
    @if($errors->any())
        var createPaymentModal = new bootstrap.Modal(document.getElementById('createPaymentModal'));
        createPaymentModal.show();
    @endif
});

function updateMonto() {
    const balanceSelect = document.getElementById('balance_id');
    const selectedOption = balanceSelect.options[balanceSelect.selectedIndex];
    const montoPendiente = selectedOption.getAttribute('data-pendiente');
    document.getElementById('monto').value = montoPendiente ? parseFloat(montoPendiente).toFixed(2) : '';
}

function updateEditMonto() {
    const balanceSelect = document.getElementById('edit_balance_id');
    const selectedOption = balanceSelect.options[balanceSelect.selectedIndex];
    const montoPendiente = selectedOption.getAttribute('data-pendiente');
    document.getElementById('edit_monto').value = montoPendiente;
}

function editPayment(paymentId) {
    const modalElement = document.getElementById('editPaymentModal');
    
    if (editPaymentModal) {
        editPaymentModal.show();
    } else {
        editPaymentModal = new bootstrap.Modal(modalElement);
        editPaymentModal.show();
    }

    fetch(`/payments/${paymentId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        const form = document.getElementById('editPaymentForm');
        form.action = `/payments/${paymentId}`;

        document.getElementById('edit_balance_id').value = data.balance_id;
        document.getElementById('edit_monto').value = data.monto;
        document.getElementById('edit_fecha_pago').value = data.fecha_pago;
        document.getElementById('edit_metodo_pago').value = data.metodo_pago || '';
        document.getElementById('edit_descripcion').value = data.descripcion || '';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los datos del pago');
    });
}
</script>
@endpush

@push('styles')
<style>
.table td, .table th {
    vertical-align: middle;
}
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
}
.btn-group .btn {
    flex: 1;
}

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
</style>
@endpush