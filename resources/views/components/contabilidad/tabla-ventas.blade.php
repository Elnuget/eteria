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
                        <th>Tipo Tarifa</th>
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
                                    @foreach($products as $product)
                                        <div class="mb-1">
                                            <small class="text-dark d-block">
                                                <strong>{{ $product['description'] ?? 'Producto/Servicio' }}</strong>
                                            </small>
                                            <small class="text-muted">
                                                Cant: {{ number_format($product['quantity'] ?? 0, 2) }} |
                                                P.Unit: ${{ number_format($product['unit_price'] ?? 0, 2) }} |
                                                Total: ${{ number_format($product['total_price'] ?? 0, 2) }}
                                            </small>
                                            <div class="mt-1">
                                                @php
                                                    $tarifaProducto = 'IVA 0%';
                                                    if (($venta['subtotal_15_iva'] ?? 0) > 0 || ($venta['iva_15'] ?? 0) > 0) {
                                                        $tarifaProducto = 'IVA 15%';
                                                    }
                                                @endphp
                                                <span class="badge @if($tarifaProducto == 'IVA 15%') bg-warning text-dark @else bg-info @endif" style="font-size: 0.7em;">
                                                    {{ $tarifaProducto }}
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
                                @endif
                            </td>
                            <td>
                                <span class="badge @if($tipoTarifa == 'IVA 15%') bg-warning @elseif($tipoTarifa == 'IVA 0%') bg-info @else bg-secondary @endif">
                                    {{ $tipoTarifa }}
                                </span>
                            </td>
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
                        <th colspan="4" class="text-end">Totales de Ventas:</th>
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
