@extends('layouts.app')

@section('content')
<div class="container">
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

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Balance</th>
                                    <th>Monto</th>
                                    <th>Fecha de Pago</th>
                                    <th>Método de Pago</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ $payment->balance->id }}</td>
                                        <td>${{ number_format($payment->monto, 2) }}</td>
                                        <td>{{ $payment->fecha_pago->format('d/m/Y') }}</td>
                                        <td>{{ $payment->metodo_pago ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($payment->descripcion, 30) ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm"
                                                        onclick="editPayment({{ $payment->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('payments.destroy', $payment) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('¿Estás seguro de eliminar este pago?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay pagos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $payments->links() }}
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
                        <input type="number" class="form-control" id="balance_id" name="balance_id" required>
                    </div>

                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto *</label>
                        <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label">Fecha de Pago *</label>
                        <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" required>
                    </div>

                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago">
                            <option value="">Seleccionar método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
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
                        <input type="number" class="form-control" id="edit_balance_id" name="balance_id" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_monto" class="form-label">Monto *</label>
                        <input type="number" step="0.01" class="form-control" id="edit_monto" name="monto" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_fecha_pago" class="form-label">Fecha de Pago *</label>
                        <input type="date" class="form-control" id="edit_fecha_pago" name="fecha_pago" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_metodo_pago" class="form-label">Método de Pago</label>
                        <select class="form-select" id="edit_metodo_pago" name="metodo_pago">
                            <option value="">Seleccionar método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
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
</style>
@endpush 