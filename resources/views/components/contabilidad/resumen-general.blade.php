<!-- Resumen General de Asientos Contables -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-chart-pie"></i>
            Resumen General de Asientos Contables
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Resumen de Compras -->
            @if(isset($compras['compras']) && count($compras['compras']) > 0)
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-shopping-cart"></i>
                                Resumen Compras
                            </h6>
                        </div>
                        <div class="card-body">                            @php
                                $totalCompras = collect($compras['compras'])->sum('total_value');
                                $subtotalCompras = collect($compras['compras'])->sum('subtotal_without_taxes');
                                $ivaCompras = $totalCompras - $subtotalCompras;
                                $cantidadCompras = count($compras['compras']);
                            @endphp
                            <div class="row text-center">
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <strong>Facturas:</strong> {{ $cantidadCompras }}
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <strong>Subtotal:</strong> ${{ number_format($subtotalCompras, 2) }}
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <strong>IVA:</strong> ${{ number_format($ivaCompras, 2) }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded p-2 bg-light">
                                        <strong>Total:</strong> ${{ number_format($totalCompras, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Resumen de Ventas -->
            @if(isset($ventas['ventas']) && count($ventas['ventas']) > 0)
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-cash-register"></i>
                                Resumen Ventas
                            </h6>
                        </div>
                        <div class="card-body">                            @php
                                $totalVentas = collect($ventas['ventas'])->sum('total_value');
                                $subtotalVentas = collect($ventas['ventas'])->sum('subtotal_without_taxes');
                                $ivaVentas = $totalVentas - $subtotalVentas;
                                $cantidadVentas = count($ventas['ventas']);
                            @endphp
                            <div class="row text-center">
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <strong>Facturas:</strong> {{ $cantidadVentas }}
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <strong>Subtotal:</strong> ${{ number_format($subtotalVentas, 2) }}
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="border rounded p-2">
                                        <strong>IVA:</strong> ${{ number_format($ivaVentas, 2) }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded p-2 bg-light">
                                        <strong>Total:</strong> ${{ number_format($totalVentas, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>        <!-- Balance Tributario -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-center">
                            <i class="fas fa-file-invoice-dollar"></i>
                            Balance Tributario del Período
                        </h6>
                        @php
                            // Calcular IVA de Compras (Crédito Tributario)
                            $creditoTributario = 0;
                            if(isset($compras['compras'])) {
                                foreach($compras['compras'] as $compra) {
                                    $totalCompra = $compra['total_value'] ?? 0;
                                    $subtotalCompra = $compra['subtotal_without_taxes'] ?? 0;
                                    $creditoTributario += ($totalCompra - $subtotalCompra);
                                }
                            }
                            
                            // Calcular IVA de Ventas (Débito Tributario)
                            $debitoTributario = 0;
                            if(isset($ventas['ventas'])) {
                                foreach($ventas['ventas'] as $venta) {
                                    $totalVenta = $venta['total_value'] ?? 0;
                                    $subtotalVenta = $venta['subtotal_without_taxes'] ?? 0;
                                    $debitoTributario += ($totalVenta - $subtotalVenta);
                                }
                            }
                            
                            // Calcular IVA a Pagar (o a Favor)
                            $ivaAPagar = $debitoTributario - $creditoTributario;
                            $ivaEstado = $ivaAPagar > 0 ? 'A Pagar' : ($ivaAPagar < 0 ? 'A Favor' : 'Neutro');
                        @endphp
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-info text-white">
                                    <h6><i class="fas fa-hand-holding-usd"></i> Crédito Tributario</h6>
                                    <h4>${{ number_format($creditoTributario, 2) }}</h4>
                                    <small>IVA pagado en compras</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-warning text-dark">
                                    <h6><i class="fas fa-coins"></i> Débito Tributario</h6>
                                    <h4>${{ number_format($debitoTributario, 2) }}</h4>
                                    <small>IVA cobrado en ventas</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-{{ $ivaAPagar > 0 ? 'danger' : ($ivaAPagar < 0 ? 'success' : 'secondary') }} text-white">
                                    <h6><i class="fas fa-calculator"></i> IVA {{ $ivaEstado }}</h6>
                                    <h4>${{ number_format(abs($ivaAPagar), 2) }}</h4>
                                    <small>{{ $ivaEstado == 'A Pagar' ? 'Debe al SRI' : ($ivaEstado == 'A Favor' ? 'SRI le debe' : 'Sin diferencia') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-primary text-white">
                                    <h6><i class="fas fa-percentage"></i> Posición Fiscal</h6>
                                    <h4>{{ $debitoTributario > 0 ? number_format(($creditoTributario / $debitoTributario) * 100, 1) : 0 }}%</h4>
                                    <small>Ratio Crédito/Débito</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info mb-0">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-info-circle"></i> Resumen Período:</strong>
                                            <br>
                                            <small>
                                                Compras: {{ isset($compras['compras']) ? count($compras['compras']) : 0 }} facturas
                                                <br>
                                                Ventas: {{ isset($ventas['ventas']) ? count($ventas['ventas']) : 0 }} facturas
                                            </small>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-exclamation-triangle"></i> Estado Tributario:</strong>
                                            <br>
                                            <small>
                                                @if($ivaAPagar > 0)
                                                    <span class="text-danger">Debe pagar IVA al SRI</span>
                                                @elseif($ivaAPagar < 0)
                                                    <span class="text-success">Tiene crédito tributario a favor</span>
                                                @else
                                                    <span class="text-muted">Posición tributaria neutra</span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-calendar"></i> Próximo Vencimiento:</strong>
                                            <br>
                                            <small class="text-muted">
                                                Revisar calendario tributario SRI
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
