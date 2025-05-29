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
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Productos</th>
                        <th>Tipo Tarifa</th>
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
                        <tr>
                            <td>{{ $compra['supplier_name'] ?? 'N/A' }}</td>
                            <td>{{ $compra['invoice_date'] ?? 'N/A' }}</td>
                            <td>
                                @if(count($products) > 0)
                                    @foreach($products as $product)
                                        <small class="d-block">{{ $product['description'] ?? 'Producto' }}</small>
                                    @endforeach
                                @else
                                    <span class="text-muted">Sin productos</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $compra['emission_type'] ?? 'Normal' }}
                                </span>
                            </td>
                            <td>${{ number_format($subtotalSinIva, 2) }}</td>
                            <td>${{ number_format($iva, 2) }}</td>
                            <td>
                                <strong>${{ number_format($compra['total_value'] ?? 0, 2) }}</strong>
                            </td>
                            <td>
                                @if(isset($compra['ride_url']))
                                    <a href="{{ $compra['ride_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf"></i>
                                        Ver RIDE
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Totales de Compras:</th>
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
