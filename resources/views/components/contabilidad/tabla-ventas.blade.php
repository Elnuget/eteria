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
                            $isService = ($venta['subtotal_0_iva'] ?? 0) > 0 || ($venta['subtotal_exempt_iva'] ?? 0) > 0;
                        @endphp
                        <tr>                            <td>{{ $venta['customer_name'] ?? 'Cliente General' }}</td>
                            <td>{{ $venta['invoice_date'] ?? 'N/A' }}</td>
                            <td>
                                @if(count($products) > 0)
                                    @foreach($products as $product)
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-dark">{{ $product['description'] ?? 'Producto' }}</small>
                                            <span class="badge bg-success ms-2" style="font-size: 0.7em;">
                                                {{ $venta['emission_type'] ?? 'Normal' }}
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if($isService)
                                            <span class="badge bg-info">Servicios</span>
                                        @else
                                            <span class="badge bg-primary">Productos</span>
                                        @endif
                                        <span class="badge bg-success ms-2" style="font-size: 0.7em;">
                                            {{ $venta['emission_type'] ?? 'Normal' }}
                                        </span>
                                    </div>
                                @endif
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
