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
                </div>                <div class="collapse" id="asientoCompra{{ $compraIndex }}">
                    <div class="card-body p-2">
                        @php
                            // Obtener totales por tipo de IVA
                            $subtotal_0 = $compra['subtotal_0_iva'] ?? 0;
                            $subtotal_15 = $compra['subtotal_15_iva'] ?? 0;
                            $subtotal_exempt = $compra['subtotal_exempt_iva'] ?? 0;
                            
                            // Si solo hay un tipo de IVA, todos los productos tienen esa tarifa
                            $tipoUnico = null;
                            if ($subtotal_15 > 0 && $subtotal_0 == 0 && $subtotal_exempt == 0) {
                                $tipoUnico = 'IVA 15%';
                            } elseif ($subtotal_0 > 0 && $subtotal_15 == 0 && $subtotal_exempt == 0) {
                                $tipoUnico = 'IVA 0%';
                            } elseif ($subtotal_exempt > 0 && $subtotal_15 == 0 && $subtotal_0 == 0) {
                                $tipoUnico = 'Exento';
                            }
                        @endphp
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Cuenta</th>
                                        <th>Detalle</th>
                                        <th class="text-end">Debe</th>
                                        <th class="text-end">Haber</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Detalle por cada producto -->
                                    @if(count($products) > 0)
                                        @foreach($products as $index => $product)
                                            @php
                                                $tarifaProducto = 'IVA 0%';
                                                $tipoProducto = 'Producto';
                                                $precioProducto = $product['total_price'] ?? 0;
                                                $ivaProducto = 0;
                                                
                                                if ($tipoUnico) {
                                                    // Si hay un solo tipo de IVA, todos los productos tienen esa tarifa
                                                    $tarifaProducto = $tipoUnico;
                                                    if ($tarifaProducto == 'IVA 15%') {
                                                        $ivaProducto = $precioProducto * 0.15 / 1.15;
                                                        $precioSinIva = $precioProducto - $ivaProducto;
                                                    } else {
                                                        $precioSinIva = $precioProducto;
                                                    }
                                                } else {
                                                    // Lógica para múltiples tipos de IVA
                                                    if (abs($precioProducto - $subtotal_15) < 0.01) {
                                                        $tarifaProducto = 'IVA 15%';
                                                        $ivaProducto = $precioProducto * 0.15 / 1.15;
                                                        $precioSinIva = $precioProducto - $ivaProducto;
                                                    } elseif (abs($precioProducto - $subtotal_0) < 0.01) {
                                                        $tarifaProducto = 'IVA 0%';
                                                        $precioSinIva = $precioProducto;
                                                    } elseif (abs($precioProducto - $subtotal_exempt) < 0.01) {
                                                        $tarifaProducto = 'Exento';
                                                        $precioSinIva = $precioProducto;
                                                    } else {
                                                        // Para productos múltiples, determinar por proporción o posición
                                                        if ($index == 0 && $subtotal_0 > 0) {
                                                            $tarifaProducto = 'IVA 0%';
                                                            $precioSinIva = $precioProducto;
                                                        } elseif ($index == 1 && $subtotal_15 > 0) {
                                                            $tarifaProducto = 'IVA 15%';
                                                            $ivaProducto = $precioProducto * 0.15 / 1.15;
                                                            $precioSinIva = $precioProducto - $ivaProducto;
                                                        } else {
                                                            $precioSinIva = $precioProducto;
                                                        }
                                                    }
                                                }
                                                
                                                // Determinar si es producto o servicio
                                                $descripcion = strtolower($product['description'] ?? '');
                                                if (str_contains($descripcion, 'consulta') || 
                                                    str_contains($descripcion, 'terapia') || 
                                                    str_contains($descripcion, 'conferencia') ||
                                                    str_contains($descripcion, 'servicio')) {
                                                    $tipoProducto = 'Servicio';
                                                }
                                            @endphp
                                            
                                            <!-- Fila del producto/servicio -->
                                            <tr>
                                                <td>
                                                    @if($tipoProducto == 'Servicio')
                                                        <strong>6201</strong> Gastos de Servicios
                                                    @else
                                                        <strong>5101</strong> Inventario/Compras
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $product['description'] ?? 'Producto' }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            Cant: {{ number_format($product['quantity'] ?? 0, 2) }} |
                                                            P.Unit: ${{ number_format($product['unit_price'] ?? 0, 2) }} |
                                                            Total: ${{ number_format($precioProducto, 2) }}
                                                        </small>
                                                        <br>
                                                        <span class="badge @if($tarifaProducto == 'IVA 15%') bg-warning text-dark @elseif($tarifaProducto == 'Exento') bg-secondary @else bg-info @endif" style="font-size: 0.65em;">
                                                            {{ $tarifaProducto }}
                                                        </span>
                                                        <span class="badge @if($tipoProducto == 'Servicio') bg-success @else bg-primary @endif" style="font-size: 0.65em;">
                                                            {{ $tipoProducto }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-end">${{ number_format($precioSinIva, 2) }}</td>
                                                <td class="text-end">-</td>
                                            </tr>
                                            
                                            <!-- Fila del IVA por producto (solo si hay IVA) -->
                                            @if($ivaProducto > 0)
                                                <tr>
                                                    <td><strong>1103</strong> IVA Crédito Tributario</td>
                                                    <td>
                                                        <small class="text-muted">
                                                            IVA {{ $tarifaProducto }} sobre {{ $product['description'] ?? 'Producto' }}
                                                        </small>
                                                    </td>
                                                    <td class="text-end">${{ number_format($ivaProducto, 2) }}</td>
                                                    <td class="text-end">-</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <!-- Si no hay productos detallados, usar el método anterior -->
                                        <tr>
                                            <td><strong>5101</strong> Gastos / Inventario</td>
                                            <td>Compra general</td>
                                            <td class="text-end">${{ number_format($subtotalValue, 2) }}</td>
                                            <td class="text-end">-</td>
                                        </tr>
                                        @if($ivaValue > 0)
                                            <tr>
                                                <td><strong>1103</strong> IVA Crédito Tributario</td>
                                                <td>IVA sobre compra general</td>
                                                <td class="text-end">${{ number_format($ivaValue, 2) }}</td>
                                                <td class="text-end">-</td>
                                            </tr>
                                        @endif
                                    @endif
                                    
                                    <!-- Cuentas por Pagar (total de la factura) -->
                                    <tr class="table-warning">
                                        <td><strong>2101</strong> Cuentas por Pagar</td>
                                        <td>
                                            <strong>{{ $compra['supplier_name'] ?? 'Proveedor' }}</strong>
                                            <br><small class="text-muted">Factura: {{ $compra['invoice_number'] ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-end">-</td>
                                        <td class="text-end"><strong>${{ number_format($totalValue, 2) }}</strong></td>
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
