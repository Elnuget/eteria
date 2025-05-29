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
                </div>                <div class="collapse" id="asientoVenta{{ $ventaIndex }}">
                    <div class="card-body p-2">
                        @php
                            // Obtener totales por tipo de IVA
                            $subtotal_0 = $venta['subtotal_0_iva'] ?? 0;
                            $subtotal_15 = $venta['subtotal_15_iva'] ?? 0;
                            $subtotal_exempt = $venta['subtotal_exempt_iva'] ?? 0;
                            $products = $venta['products'] ?? [];
                            
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
                                    <!-- Cuentas por Cobrar (total de la factura) -->
                                    <tr class="table-info">
                                        <td><strong>1201</strong> Cuentas por Cobrar</td>
                                        <td>
                                            <strong>{{ $venta['customer_name'] ?? 'Cliente General' }}</strong>
                                            <br><small class="text-muted">Factura: {{ $venta['invoice_number'] ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-end"><strong>${{ number_format($totalValue, 2) }}</strong></td>
                                        <td class="text-end">-</td>
                                    </tr>
                                    
                                    <!-- Detalle por cada producto/servicio -->
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
                                                    str_contains($descripcion, 'inyección') || 
                                                    str_contains($descripcion, 'inyeccion') || 
                                                    str_contains($descripcion, 'corte') ||
                                                    str_contains($descripcion, 'servicio')) {
                                                    $tipoProducto = 'Servicio';
                                                }
                                            @endphp
                                            
                                            <!-- Fila del ingreso por producto/servicio -->
                                            <tr>
                                                <td>
                                                    @if($tipoProducto == 'Servicio')
                                                        <strong>4102</strong> Ingresos por Servicios
                                                    @else
                                                        <strong>4101</strong> Ingresos por Ventas
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $product['description'] ?? 'Producto/Servicio' }}</strong>
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
                                                <td class="text-end">-</td>
                                                <td class="text-end">${{ number_format($precioSinIva, 2) }}</td>
                                            </tr>
                                            
                                            <!-- Fila del IVA por producto (solo si hay IVA) -->
                                            @if($ivaProducto > 0)
                                                <tr>
                                                    <td><strong>2104</strong> IVA por Pagar</td>
                                                    <td>
                                                        <small class="text-muted">
                                                            IVA {{ $tarifaProducto }} sobre {{ $product['description'] ?? 'Producto' }}
                                                        </small>
                                                    </td>
                                                    <td class="text-end">-</td>
                                                    <td class="text-end">${{ number_format($ivaProducto, 2) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <!-- Si no hay productos detallados, usar el método anterior -->
                                        <tr>
                                            <td><strong>4101</strong> Ingresos por Ventas</td>
                                            <td>Venta general</td>
                                            <td class="text-end">-</td>
                                            <td class="text-end">${{ number_format($subtotalValue, 2) }}</td>
                                        </tr>
                                        @if($ivaValue > 0)
                                            <tr>
                                                <td><strong>2104</strong> IVA por Pagar</td>
                                                <td>IVA sobre venta general</td>
                                                <td class="text-end">-</td>
                                                <td class="text-end">${{ number_format($ivaValue, 2) }}</td>
                                            </tr>
                                        @endif
                                    @endif
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
