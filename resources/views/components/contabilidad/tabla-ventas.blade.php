<!-- Sección de Ventas -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-cash-register"></i>
            Registro de Ventas
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">                <thead class="table-dark">
                    <tr>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Productos/Servicios</th>
                        <th>Subtotal</th>
                        <th>IVA</th>
                        <th>Total</th>
                        <th>RIDE</th>
                    </tr>
                </thead>
                <tbody>                    @foreach($ventas['ventas'] as $venta)
                        @php
                            $products = $venta['products'] ?? [];
                            $subtotalSinIva = $venta['subtotal_without_taxes'] ?? 0;
                            $iva = ($venta['total_value'] ?? 0) - $subtotalSinIva;
                            
                            // Determinar tipo de tarifa basado en los subtotales
                            $tipoTarifa = 'Normal (0%)';
                            if (($venta['subtotal_15_iva'] ?? 0) > 0) {
                                $tipoTarifa = 'IVA 15%';
                            } elseif (($venta['iva_15'] ?? 0) > 0) {
                                $tipoTarifa = 'IVA 15%';
                            } elseif (($venta['subtotal_0_iva'] ?? 0) > 0) {
                                $tipoTarifa = 'IVA 0%';
                            } elseif (($venta['subtotal_exempt_iva'] ?? 0) > 0) {
                                $tipoTarifa = 'Exento';
                            }
                            
                            $isService = ($venta['subtotal_0_iva'] ?? 0) > 0 || ($venta['subtotal_exempt_iva'] ?? 0) > 0;
                        @endphp
                        <tr>                            <td>{{ $venta['customer_name'] ?? 'Cliente General' }}</td>
                            <td>{{ $venta['invoice_date'] ?? 'N/A' }}</td>                            <td>
                                @if(count($products) > 0)
                                    @php
                                        // Obtener totales por tipo de IVA
                                        $subtotal_0 = $venta['subtotal_0_iva'] ?? 0;
                                        $subtotal_15 = $venta['subtotal_15_iva'] ?? 0;
                                        $subtotal_exempt = $venta['subtotal_exempt_iva'] ?? 0;
                                        
                                        // Calcular total de productos
                                        $totalProductos = collect($products)->sum('total_price');
                                        
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
                                    
                                    @foreach($products as $index => $product)
                                        <div class="mb-1">
                                            <small class="text-dark d-block">
                                                <strong>{{ $product['description'] ?? 'Producto/Servicio' }}</strong>
                                            </small>
                                            <small class="text-muted">
                                                Cant: {{ number_format($product['quantity'] ?? 0, 2) }} |
                                                P.Unit: ${{ number_format($product['unit_price'] ?? 0, 2) }} |
                                                Total: ${{ number_format($product['total_price'] ?? 0, 2) }}
                                                @if(isset($product['discount']) && $product['discount'] > 0)
                                                    | Desc: ${{ number_format($product['discount'], 2) }}
                                                @endif
                                            </small>
                                            <div class="mt-1">
                                                @php
                                                    $tarifaProducto = 'IVA 0%';
                                                    $tipoProducto = 'Producto';
                                                    $colorBadge = 'bg-info';
                                                    
                                                    if ($tipoUnico) {
                                                        // Si hay un solo tipo de IVA, todos los productos tienen esa tarifa
                                                        $tarifaProducto = $tipoUnico;
                                                    } else {
                                                        // Lógica para múltiples tipos de IVA
                                                        $precioProducto = $product['total_price'] ?? 0;
                                                        
                                                        // Si el precio coincide aproximadamente con algún subtotal
                                                        if (abs($precioProducto - $subtotal_15) < 0.01) {
                                                            $tarifaProducto = 'IVA 15%';
                                                        } elseif (abs($precioProducto - $subtotal_0) < 0.01) {
                                                            $tarifaProducto = 'IVA 0%';
                                                        } elseif (abs($precioProducto - $subtotal_exempt) < 0.01) {
                                                            $tarifaProducto = 'Exento';
                                                        } else {
                                                            // Para productos múltiples, determinar por proporción o posición
                                                            if ($index == 0 && $subtotal_0 > 0) {
                                                                $tarifaProducto = 'IVA 0%';
                                                            } elseif ($index == 1 && $subtotal_15 > 0) {
                                                                $tarifaProducto = 'IVA 15%';
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
                                                    
                                                    // Color del badge según tarifa
                                                    if ($tarifaProducto == 'IVA 15%') {
                                                        $colorBadge = 'bg-warning text-dark';
                                                    } elseif ($tarifaProducto == 'Exento') {
                                                        $colorBadge = 'bg-secondary';
                                                    } else {
                                                        $colorBadge = 'bg-info';
                                                    }
                                                @endphp
                                                
                                                <span class="badge {{ $colorBadge }}" style="font-size: 0.7em;">
                                                    {{ $tarifaProducto }}
                                                </span>
                                                <span class="badge @if($tipoProducto == 'Servicio') bg-success @else bg-primary @endif" style="font-size: 0.7em;">
                                                    {{ $tipoProducto }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if($isService)
                                            <span class="badge bg-info">Servicios</span>
                                        @else
                                            <span class="badge bg-primary">Productos</span>
                                        @endif
                                    </div>
                                @endif                            </td>
                            <td>${{ number_format($subtotalSinIva, 2) }}</td>
                            <td>${{ number_format($iva, 2) }}</td>
                            <td>
                                <strong>${{ number_format($venta['total_value'] ?? 0, 2) }}</strong>
                            </td>                            <td>
                                @if(isset($venta['authorization_number']) && !empty($venta['authorization_number']))
                                    @php
                                        $pdfPath = public_path('venta/' . $venta['authorization_number'] . '.pdf');
                                        $pdfExists = file_exists($pdfPath);
                                    @endphp                                    @if($pdfExists)
                                        <a href="{{ asset('venta/' . $venta['authorization_number'] . '.pdf') }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-success">
                                            <i class="fas fa-file-pdf"></i>
                                            RIDE Local
                                        </a>
                                    @else
                                        <a href="https://celcer.sri.gob.ec/comprobantes-electronicos-internet/publico/comprobantes-electronicos.jspa?comprobante={{ $venta['authorization_number'] }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="PDF no disponible localmente - Ver en SRI">
                                            <i class="fas fa-external-link-alt"></i>
                                            RIDE SRI
                                        </a>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Totales de Ventas:</th>
                        <th>${{ number_format(collect($ventas['ventas'])->sum('subtotal_without_taxes'), 2) }}</th>
                        <th>${{ number_format(collect($ventas['ventas'])->sum(function($venta) { return ($venta['total_value'] ?? 0) - ($venta['subtotal_without_taxes'] ?? 0); }), 2) }}</th>
                        <th>${{ number_format(collect($ventas['ventas'])->sum('total_value'), 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
