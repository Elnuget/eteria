<!-- Sección de Compras -->
<div class="card">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-shopping-cart"></i>
            Registro de Compras
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">                <thead class="table-dark">
                    <tr>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Productos</th>
                        <th>Subtotal</th>
                        <th>IVA</th>
                        <th>Total</th>
                        <th>RIDE</th>
                    </tr>
                </thead>
                <tbody>                    @foreach($compras['compras'] as $compra)
                        @php
                            $products = $compra['products'] ?? [];
                            $subtotalSinIva = $compra['subtotal_without_taxes'] ?? 0;
                            $iva = ($compra['total_value'] ?? 0) - $subtotalSinIva;
                            
                            // Determinar tipo de tarifa basado en los subtotales
                            $tipoTarifa = 'Normal (0%)';
                            if (($compra['subtotal_15_iva'] ?? 0) > 0) {
                                $tipoTarifa = 'IVA 15%';
                            } elseif (($compra['iva_15'] ?? 0) > 0) {
                                $tipoTarifa = 'IVA 15%';
                            } elseif (($compra['subtotal_0_iva'] ?? 0) > 0) {
                                $tipoTarifa = 'IVA 0%';
                            } elseif (($compra['subtotal_exempt_iva'] ?? 0) > 0) {
                                $tipoTarifa = 'Exento';
                            }
                        @endphp
                        <tr>                            <td>{{ $compra['supplier_name'] ?? 'N/A' }}</td>
                            <td>{{ $compra['invoice_date'] ?? 'N/A' }}</td>                            <td>
                                @if(count($products) > 0)
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
                                    
                                    @foreach($products as $index => $product)
                                        <div class="mb-1">
                                            <small class="text-dark d-block">
                                                <strong>{{ $product['description'] ?? 'Producto' }}</strong>
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
                                                        str_contains($descripcion, 'conferencia') ||
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
                                    <span class="text-muted">Sin productos</span>
                                @endif                            </td>
                            <td>${{ number_format($subtotalSinIva, 2) }}</td>
                            <td>${{ number_format($iva, 2) }}</td>
                            <td>
                                <strong>${{ number_format($compra['total_value'] ?? 0, 2) }}</strong>
                            </td>                            <td>
                                @if(isset($compra['authorization_number']) && !empty($compra['authorization_number']))
                                    @php
                                        $pdfPath = public_path('compra/' . $compra['authorization_number'] . '.pdf');
                                        $pdfExists = file_exists($pdfPath);
                                    @endphp                                    @if($pdfExists)
                                        <a href="{{ asset('compra/' . $compra['authorization_number'] . '.pdf') }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-success">
                                            <i class="fas fa-file-pdf"></i>
                                            RIDE Local
                                        </a>
                                    @else
                                        <a href="https://celcer.sri.gob.ec/comprobantes-electronicos-internet/publico/comprobantes-electronicos.jspa?comprobante={{ $compra['authorization_number'] }}" 
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
                        <th colspan="3" class="text-end">Totales de Compras:</th>
                        <th>${{ number_format(collect($compras['compras'])->sum('subtotal_without_taxes'), 2) }}</th>
                        <th>${{ number_format(collect($compras['compras'])->sum(function($compra) { return ($compra['total_value'] ?? 0) - ($compra['subtotal_without_taxes'] ?? 0); }), 2) }}</th>
                        <th>${{ number_format(collect($compras['compras'])->sum('total_value'), 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
