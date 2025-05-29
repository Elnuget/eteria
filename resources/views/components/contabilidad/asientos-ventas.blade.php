<!-- Asientos Contables de Ventas -->
<div class="card mt-3">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">
            <i class="fas fa-balance-scale"></i>
            Asientos Contables - Ventas
        </h6>
    </div>
    <div class="card-body">
        @php 
            $totalDebeVentas = 0;
            $totalHaberVentas = 0;
            $ventaIndex = 0;
        @endphp
          @foreach($ventas['ventas'] as $venta)
            @php
                $totalValue = $venta['total_value'] ?? 0;
                $subtotalValue = $venta['subtotal_without_taxes'] ?? 0;
                $ivaValue = $totalValue - $subtotalValue;
                
                // Acumulamos para los totales
                $totalDebeVentas += $totalValue;
                $totalHaberVentas += $subtotalValue + $ivaValue;
                $ventaIndex++;
            @endphp
            
            <!-- Asiento Individual por Factura -->
            <div class="card border-info mb-3">
                <div class="card-header bg-light p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Factura #{{ $venta['invoice_number'] ?? $ventaIndex }}</strong>
                            <small class="text-muted">- {{ $venta['customer_name'] ?? 'Cliente General' }}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-info" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#asientoVenta{{ $ventaIndex }}" 
                                    aria-expanded="false">
                                <i class="fas fa-eye"></i> Ver Asiento
                            </button>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="asientoVenta{{ $ventaIndex }}">
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Cuenta</th>
                                        <th class="text-end">Debe</th>
                                        <th class="text-end">Haber</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cuentas por Cobrar</td>
                                        <td class="text-end">${{ number_format($totalValue, 2) }}</td>
                                        <td class="text-end">-</td>
                                    </tr>
                                    <tr>
                                        <td>Ingresos por Ventas</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">${{ number_format($subtotalValue, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>IVA Cobrado</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">${{ number_format($ivaValue, 2) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end">${{ number_format($totalValue, 2) }}</th>
                                        <th class="text-end">${{ number_format($subtotalValue + $ivaValue, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        <!-- Resumen Total de Ventas -->
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-calculator"></i>
                    Resumen Total - Asientos de Ventas
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <span><strong>Total Debe:</strong></span>
                            <span><strong>${{ number_format($totalDebeVentas, 2) }}</strong></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <span><strong>Total Haber:</strong></span>
                            <span><strong>${{ number_format($totalHaberVentas, 2) }}</strong></span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    @if($totalDebeVentas == $totalHaberVentas)
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check-circle"></i> Balance Correcto
                        </span>
                    @else
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Diferencia: ${{ number_format(abs($totalDebeVentas - $totalHaberVentas), 2) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
