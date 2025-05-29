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
        </div>

        <!-- Balance General -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-center">
                            <i class="fas fa-balance-scale"></i>
                            Balance General del Período
                        </h6>
                        @php
                            $totalIngresos = isset($ventas['ventas']) ? collect($ventas['ventas'])->sum('total_value') : 0;
                            $totalGastos = isset($compras['compras']) ? collect($compras['compras'])->sum('total_value') : 0;
                            $utilidad = $totalIngresos - $totalGastos;
                        @endphp
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-success text-white">
                                    <h6>Ingresos</h6>
                                    <h4>${{ number_format($totalIngresos, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-danger text-white">
                                    <h6>Gastos</h6>
                                    <h4>${{ number_format($totalGastos, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-{{ $utilidad >= 0 ? 'primary' : 'warning' }} text-white">
                                    <h6>{{ $utilidad >= 0 ? 'Utilidad' : 'Pérdida' }}</h6>
                                    <h4>${{ number_format(abs($utilidad), 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-info text-white">
                                    <h6>Margen</h6>
                                    <h4>{{ $totalIngresos > 0 ? number_format(($utilidad / $totalIngresos) * 100, 1) : 0 }}%</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
