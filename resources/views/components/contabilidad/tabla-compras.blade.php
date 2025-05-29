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
                        @endphp
                        <tr>                            <td>{{ $compra['supplier_name'] ?? 'N/A' }}</td>
                            <td>{{ $compra['invoice_date'] ?? 'N/A' }}</td>
                            <td>
                                @if(count($products) > 0)
                                    @foreach($products as $product)
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-dark">{{ $product['description'] ?? 'Producto' }}</small>
                                            <span class="badge bg-info ms-2" style="font-size: 0.7em;">
                                                {{ $compra['emission_type'] ?? 'Normal' }}
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Sin productos</span>
                                        <span class="badge bg-info ms-2" style="font-size: 0.7em;">
                                            {{ $compra['emission_type'] ?? 'Normal' }}
                                        </span>
                                    </div>
                                @endif
                            </td>
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
