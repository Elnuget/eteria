<!-- Asientos Contables de Compras -->
<div class="card mt-3">
    <div class="card-header bg-warning text-dark">
        <h6 class="mb-0">
            <i class="fas fa-balance-scale"></i>
            Asientos Contables - Compras
        </h6>
    </div>
    <div class="card-body">
        @php 
            $totalDebeCompras = 0;
            $totalHaberCompras = 0;
            $compraIndex = 0;
        @endphp
          @foreach($compras['compras'] as $compra)
            @php
                $totalValue = $compra['total_value'] ?? 0;
                $subtotalValue = $compra['subtotal_without_taxes'] ?? 0;
                $ivaValue = $totalValue - $subtotalValue;
                
                // Acumulamos para los totales
                $totalDebeCompras += $subtotalValue + $ivaValue;
                $totalHaberCompras += $totalValue;
                $compraIndex++;
                $products = $compra['products'] ?? [];
            @endphp
            
            <!-- Asiento Individual por Factura -->
            <div class="card border-warning mb-3">
                <div class="card-header bg-light p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Factura #{{ $compra['invoice_number'] ?? $compraIndex }}</strong>
                            <small class="text-muted">- {{ $compra['supplier_name'] ?? 'Proveedor' }}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-warning" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#asientoCompra{{ $compraIndex }}" 
                                    aria-expanded="false">
                                <i class="fas fa-eye"></i> Ver Asiento
                            </button>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="asientoCompra{{ $compraIndex }}">
                    <div class="card-body p-2">
                        <!-- Información de Productos si existe -->                        @if(count($products) > 0)
                            <div class="mb-2">
                                <strong>Productos:</strong>
                                @foreach($products as $product)
                                    <span class="badge bg-secondary me-1">{{ $product['description'] ?? 'Producto' }}</span>
                                @endforeach
                            </div>
                        @endif
                        
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
                                        <td>Gastos / Inventario</td>
                                        <td class="text-end">${{ number_format($subtotalValue, 2) }}</td>
                                        <td class="text-end">-</td>
                                    </tr>
                                    <tr>
                                        <td>IVA por Pagar</td>
                                        <td class="text-end">${{ number_format($ivaValue, 2) }}</td>
                                        <td class="text-end">-</td>
                                    </tr>
                                    <tr>
                                        <td>Cuentas por Pagar</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">${{ number_format($totalValue, 2) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end">${{ number_format($subtotalValue + $ivaValue, 2) }}</th>
                                        <th class="text-end">${{ number_format($totalValue, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        <!-- Resumen Total de Compras -->
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-calculator"></i>
                    Resumen Total - Asientos de Compras
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <span><strong>Total Debe:</strong></span>
                            <span><strong>${{ number_format($totalDebeCompras, 2) }}</strong></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <span><strong>Total Haber:</strong></span>
                            <span><strong>${{ number_format($totalHaberCompras, 2) }}</strong></span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    @if($totalDebeCompras == $totalHaberCompras)
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check-circle"></i> Balance Correcto
                        </span>
                    @else
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Diferencia: ${{ number_format(abs($totalDebeCompras - $totalHaberCompras), 2) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
