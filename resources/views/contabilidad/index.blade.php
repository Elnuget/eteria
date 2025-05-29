@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Sección de Compras y Ventas -->
            @if((isset($compras['compras']) && count($compras['compras']) > 0) || (isset($ventas['ventas']) && count($ventas['ventas']) > 0))
                <div class="row mb-4">
                    <!-- Sección de Compras -->
                    @if(isset($compras['compras']) && count($compras['compras']) > 0)
                        <div class="col-md-6">
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
                                                    <th>Total</th>
                                                    <th>RIDE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($compras['compras'] as $compra)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $compra['supplier_name'] }}</strong>
                                                            @if(isset($compra['business_name']))
                                                                <br><small class="text-muted">{{ $compra['business_name'] }}</small>
                                                            @endif
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y', $compra['invoice_date'])->format('d/m/Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-danger">
                                                                ${{ number_format($compra['total_value'] ?? 0, 2) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if(isset($compra['authorization_number']))
                                                                <a href="{{ asset('compra/' . $compra['authorization_number'] . '.pdf') }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-outline-danger" 
                                                                   title="Ver RIDE PDF">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                    PDF
                                                                </a>
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="2" class="text-end">Total de Compras:</th>
                                                    <th>
                                                        <span class="badge bg-danger">
                                                            @php
                                                                $totalComprasValue = array_sum(array_column($compras['compras'], 'total_value'));
                                                            @endphp
                                                            ${{ number_format($totalComprasValue, 2) }}
                                                        </span>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

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
                                            $subtotalValue = $compra['subtotal_value'] ?? ($totalValue / 1.15); // Calculamos subtotal
                                            $ivaValue = $totalValue - $subtotalValue; // Calculamos IVA
                                            
                                            // Acumulamos para los totales
                                            $totalDebeCompras += $subtotalValue + $ivaValue; // Gastos + IVA
                                            $totalHaberCompras += $totalValue; // Cuentas por Pagar
                                            $compraIndex++;
                                        @endphp
                                        
                                        <!-- Asiento Individual por Factura -->
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-light p-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <button class="btn btn-sm btn-outline-warning" 
                                                                    type="button" 
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#asientoCompra{{ $compraIndex }}" 
                                                                    aria-expanded="false">
                                                                <i class="fas fa-chevron-down"></i>
                                                            </button>
                                                            Asiento #{{ $compraIndex }} - {{ $compra['supplier_name'] }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            Fecha: {{ \Carbon\Carbon::createFromFormat('d/m/Y', $compra['invoice_date'])->format('d/m/Y') }} 
                                                            | Total: ${{ number_format($totalValue, 2) }}
                                                        </small>
                                                    </div>
                                                    <div>
                                                        @if(($subtotalValue + $ivaValue) == $totalValue)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check"></i> Balanceado
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle"></i> Desbalanceado
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapse" id="asientoCompra{{ $compraIndex }}">
                                                <div class="card-body p-2">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-striped mb-0">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th width="20%">Cuenta</th>
                                                                    <th width="40%">Descripción</th>
                                                                    <th width="20%" class="text-end">Debe</th>
                                                                    <th width="20%" class="text-end">Haber</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- Gastos de Operación (Debe) -->
                                                                <tr>
                                                                    <td><strong>5101</strong></td>
                                                                    <td>Gastos de Operación - Compra a {{ $compra['supplier_name'] }}</td>
                                                                    <td class="text-end text-success">
                                                                        <strong>${{ number_format($subtotalValue, 2) }}</strong>
                                                                    </td>
                                                                    <td class="text-end">-</td>
                                                                </tr>
                                                                <!-- IVA por Pagar (Debe) -->
                                                                <tr>
                                                                    <td><strong>1105</strong></td>
                                                                    <td>IVA Pagado - Crédito Tributario</td>
                                                                    <td class="text-end text-success">
                                                                        <strong>${{ number_format($ivaValue, 2) }}</strong>
                                                                    </td>
                                                                    <td class="text-end">-</td>
                                                                </tr>
                                                                <!-- Cuentas por Pagar (Haber) -->
                                                                <tr>
                                                                    <td><strong>2101</strong></td>
                                                                    <td>Cuentas por Pagar - {{ $compra['supplier_name'] }}</td>
                                                                    <td class="text-end">-</td>
                                                                    <td class="text-end text-danger">
                                                                        <strong>${{ number_format($totalValue, 2) }}</strong>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot class="table-light">
                                                                <tr>
                                                                    <th colspan="2" class="text-end">SUBTOTALES:</th>
                                                                    <th class="text-end">
                                                                        <span class="badge bg-success">
                                                                            ${{ number_format($subtotalValue + $ivaValue, 2) }}
                                                                        </span>
                                                                    </th>
                                                                    <th class="text-end">
                                                                        <span class="badge bg-danger">
                                                                            ${{ number_format($totalValue, 2) }}
                                                                        </span>
                                                                    </th>
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
                                                        <span>Total Debe:</span>
                                                        <span class="badge bg-success fs-6">
                                                            ${{ number_format($totalDebeCompras, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex justify-content-between">
                                                        <span>Total Haber:</span>
                                                        <span class="badge bg-danger fs-6">
                                                            ${{ number_format($totalHaberCompras, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="text-center">
                                                @if($totalDebeCompras == $totalHaberCompras)
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-check"></i>
                                                        TODAS LAS COMPRAS BALANCEADAS
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        DIFERENCIA: ${{ number_format(abs($totalDebeCompras - $totalHaberCompras), 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Sección de Ventas -->
                    @if(isset($ventas['ventas']) && count($ventas['ventas']) > 0)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-cash-register"></i>
                                        Registro de Ventas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Empresa</th>
                                                    <th>Fecha</th>
                                                    <th>Total</th>
                                                    <th>RIDE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ventas['ventas'] as $venta)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $venta['supplier_name'] }}</strong>
                                                            @if(isset($venta['business_name']))
                                                                <br><small class="text-muted">{{ $venta['business_name'] }}</small>
                                                            @endif
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y', $venta['invoice_date'])->format('d/m/Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                ${{ number_format($venta['total_value'] ?? 0, 2) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if(isset($venta['authorization_number']))
                                                                <a href="{{ asset('venta/' . $venta['authorization_number'] . '.pdf') }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-outline-success" 
                                                                   title="Ver RIDE PDF">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                    PDF
                                                                </a>
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="2" class="text-end">Total de Ventas:</th>
                                                    <th>
                                                        <span class="badge bg-success">
                                                            @php
                                                                $totalVentasValue = array_sum(array_column($ventas['ventas'], 'total_value'));
                                                            @endphp
                                                            ${{ number_format($totalVentasValue, 2) }}
                                                        </span>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

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
                                            $subtotalValue = $venta['subtotal_value'] ?? ($totalValue / 1.15); // Calculamos subtotal
                                            $ivaValue = $totalValue - $subtotalValue; // Calculamos IVA
                                            
                                            // Acumulamos para los totales
                                            $totalDebeVentas += $totalValue; // Cuentas por Cobrar
                                            $totalHaberVentas += $subtotalValue + $ivaValue; // Ingresos + IVA
                                            $ventaIndex++;
                                        @endphp
                                        
                                        <!-- Asiento Individual por Factura -->
                                        <div class="card border-info mb-3">
                                            <div class="card-header bg-light p-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <button class="btn btn-sm btn-outline-info" 
                                                                    type="button" 
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#asientoVenta{{ $ventaIndex }}" 
                                                                    aria-expanded="false">
                                                                <i class="fas fa-chevron-down"></i>
                                                            </button>
                                                            Asiento #{{ $ventaIndex }} - {{ $venta['customer_name'] ?? 'Cliente' }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            Fecha: {{ \Carbon\Carbon::createFromFormat('d/m/Y', $venta['invoice_date'])->format('d/m/Y') }} 
                                                            | Total: ${{ number_format($totalValue, 2) }}
                                                        </small>
                                                    </div>
                                                    <div>
                                                        @if($totalValue == ($subtotalValue + $ivaValue))
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check"></i> Balanceado
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle"></i> Desbalanceado
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapse" id="asientoVenta{{ $ventaIndex }}">
                                                <div class="card-body p-2">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-striped mb-0">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th width="20%">Cuenta</th>
                                                                    <th width="40%">Descripción</th>
                                                                    <th width="20%" class="text-end">Debe</th>
                                                                    <th width="20%" class="text-end">Haber</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- Cuentas por Cobrar (Debe) -->
                                                                <tr>
                                                                    <td><strong>1201</strong></td>
                                                                    <td>Cuentas por Cobrar - {{ $venta['customer_name'] ?? 'Cliente' }}</td>
                                                                    <td class="text-end text-success">
                                                                        <strong>${{ number_format($totalValue, 2) }}</strong>
                                                                    </td>
                                                                    <td class="text-end">-</td>
                                                                </tr>
                                                                <!-- Ingresos por Ventas (Haber) -->
                                                                <tr>
                                                                    <td><strong>4101</strong></td>
                                                                    <td>Ingresos por Ventas - Servicios</td>
                                                                    <td class="text-end">-</td>
                                                                    <td class="text-end text-danger">
                                                                        <strong>${{ number_format($subtotalValue, 2) }}</strong>
                                                                    </td>
                                                                </tr>
                                                                <!-- IVA por Pagar (Haber) -->
                                                                <tr>
                                                                    <td><strong>2104</strong></td>
                                                                    <td>IVA por Pagar - Débito Fiscal</td>
                                                                    <td class="text-end">-</td>
                                                                    <td class="text-end text-danger">
                                                                        <strong>${{ number_format($ivaValue, 2) }}</strong>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot class="table-light">
                                                                <tr>
                                                                    <th colspan="2" class="text-end">SUBTOTALES:</th>
                                                                    <th class="text-end">
                                                                        <span class="badge bg-success">
                                                                            ${{ number_format($totalValue, 2) }}
                                                                        </span>
                                                                    </th>
                                                                    <th class="text-end">
                                                                        <span class="badge bg-danger">
                                                                            ${{ number_format($subtotalValue + $ivaValue, 2) }}
                                                                        </span>
                                                                    </th>
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
                                                        <span>Total Debe:</span>
                                                        <span class="badge bg-success fs-6">
                                                            ${{ number_format($totalDebeVentas, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex justify-content-between">
                                                        <span>Total Haber:</span>
                                                        <span class="badge bg-danger fs-6">
                                                            ${{ number_format($totalHaberVentas, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="text-center">
                                                @if($totalDebeVentas == $totalHaberVentas)
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-check"></i>
                                                        TODAS LAS VENTAS BALANCEADAS
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        DIFERENCIA: ${{ number_format(abs($totalDebeVentas - $totalHaberVentas), 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Resumen General de Asientos Contables -->
                @if((isset($compras['compras']) && count($compras['compras']) > 0) || (isset($ventas['ventas']) && count($ventas['ventas']) > 0))
                    <div class="row mt-4">
                        <div class="col-md-12">
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
                                                        <h6 class="mb-0">Movimientos por Compras</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @php
                                                            $totalComprasGeneral = array_sum(array_column($compras['compras'], 'total_value'));
                                                            $totalSubtotalCompras = 0;
                                                            $totalIvaCompras = 0;
                                                            
                                                            foreach($compras['compras'] as $compra) {
                                                                $total = $compra['total_value'] ?? 0;
                                                                $subtotal = $compra['subtotal_value'] ?? ($total / 1.15);
                                                                $iva = $total - $subtotal;
                                                                $totalSubtotalCompras += $subtotal;
                                                                $totalIvaCompras += $iva;
                                                            }
                                                        @endphp
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>Gastos de Operación (Debe):</span>
                                                            <span class="badge bg-success fs-6">${{ number_format($totalSubtotalCompras, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>IVA Crédito Tributario (Debe):</span>
                                                            <span class="badge bg-success fs-6">${{ number_format($totalIvaCompras, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>Cuentas por Pagar (Haber):</span>
                                                            <span class="badge bg-danger fs-6">${{ number_format($totalComprasGeneral, 2) }}</span>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <strong>Balance:</strong>
                                                            @if(($totalSubtotalCompras + $totalIvaCompras) == $totalComprasGeneral)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check"></i> Cuadrado
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> 
                                                                    Dif: ${{ number_format(abs(($totalSubtotalCompras + $totalIvaCompras) - $totalComprasGeneral), 2) }}
                                                                </span>
                                                            @endif
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
                                                        <h6 class="mb-0">Movimientos por Ventas</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @php
                                                            $totalVentasGeneral = array_sum(array_column($ventas['ventas'], 'total_value'));
                                                            $totalSubtotalVentas = 0;
                                                            $totalIvaVentas = 0;
                                                            
                                                            foreach($ventas['ventas'] as $venta) {
                                                                $total = $venta['total_value'] ?? 0;
                                                                $subtotal = $venta['subtotal_value'] ?? ($total / 1.15);
                                                                $iva = $total - $subtotal;
                                                                $totalSubtotalVentas += $subtotal;
                                                                $totalIvaVentas += $iva;
                                                            }
                                                        @endphp
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>Cuentas por Cobrar (Debe):</span>
                                                            <span class="badge bg-success fs-6">${{ number_format($totalVentasGeneral, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>Ingresos por Ventas (Haber):</span>
                                                            <span class="badge bg-danger fs-6">${{ number_format($totalSubtotalVentas, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>IVA por Pagar (Haber):</span>
                                                            <span class="badge bg-danger fs-6">${{ number_format($totalIvaVentas, 2) }}</span>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <strong>Balance:</strong>
                                                            @if($totalVentasGeneral == ($totalSubtotalVentas + $totalIvaVentas))
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check"></i> Cuadrado
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> 
                                                                    Dif: ${{ number_format(abs($totalVentasGeneral - ($totalSubtotalVentas + $totalIvaVentas)), 2) }}
                                                                </span>
                                                            @endif
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
                                                    @php
                                                        $totalDebeGeneral = 0;
                                                        $totalHaberGeneral = 0;
                                                        $ivaCredito = 0; // IVA pagado en compras
                                                        $ivaDebito = 0;  // IVA cobrado en ventas
                                                        
                                                        // Cálculos de Compras
                                                        if(isset($compras['compras'])) {
                                                            foreach($compras['compras'] as $compra) {
                                                                $total = $compra['total_value'] ?? 0;
                                                                $subtotal = $compra['subtotal_value'] ?? ($total / 1.15);
                                                                $iva = $total - $subtotal;
                                                                
                                                                $totalDebeGeneral += $subtotal; // Gastos
                                                                $totalDebeGeneral += $iva;      // IVA Crédito
                                                                $totalHaberGeneral += $total;   // Cuentas por Pagar
                                                                $ivaCredito += $iva;
                                                            }
                                                        }
                                                        
                                                        // Cálculos de Ventas
                                                        if(isset($ventas['ventas'])) {
                                                            foreach($ventas['ventas'] as $venta) {
                                                                $total = $venta['total_value'] ?? 0;
                                                                $subtotal = $venta['subtotal_value'] ?? ($total / 1.15);
                                                                $iva = $total - $subtotal;
                                                                
                                                                $totalDebeGeneral += $total;    // Cuentas por Cobrar
                                                                $totalHaberGeneral += $subtotal; // Ingresos
                                                                $totalHaberGeneral += $iva;      // IVA por Pagar
                                                                $ivaDebito += $iva;
                                                            }
                                                        }
                                                        
                                                        $ivaResultante = $ivaDebito - $ivaCredito; // IVA neto a pagar/favor
                                                    @endphp
                                                    
                                                    <h5 class="mb-3 text-center">Balance General de Asientos Contables</h5>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <h6>Total Debe:</h6>
                                                                <span class="badge bg-success fs-5">
                                                                    ${{ number_format($totalDebeGeneral, 2) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <h6>Total Haber:</h6>
                                                                <span class="badge bg-danger fs-5">
                                                                    ${{ number_format($totalHaberGeneral, 2) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <h6>Estado:</h6>
                                                                @if($totalDebeGeneral == $totalHaberGeneral)
                                                                    <span class="badge bg-success fs-5">
                                                                        <i class="fas fa-check"></i>
                                                                        BALANCEADO
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-danger fs-5">
                                                                        <i class="fas fa-exclamation-triangle"></i>
                                                                        DESBALANCEADO
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <h6>IVA Resultante:</h6>
                                                                @if($ivaResultante > 0)
                                                                    <span class="badge bg-warning fs-5">
                                                                        A Pagar: ${{ number_format($ivaResultante, 2) }}
                                                                    </span>
                                                                @elseif($ivaResultante < 0)
                                                                    <span class="badge bg-info fs-5">
                                                                        A Favor: ${{ number_format(abs($ivaResultante), 2) }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-secondary fs-5">
                                                                        Neutro: $0.00
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Detalle del IVA -->
                                                    @if($ivaCredito > 0 || $ivaDebito > 0)
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h6 class="text-center mb-3">Detalle de IVA</h6>
                                                                <div class="row">
                                                                    <div class="col-md-4 text-center">
                                                                        <small class="text-muted">IVA Crédito (Compras)</small><br>
                                                                        <span class="badge bg-primary">
                                                                            ${{ number_format($ivaCredito, 2) }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-md-4 text-center">
                                                                        <small class="text-muted">IVA Débito (Ventas)</small><br>
                                                                        <span class="badge bg-primary">
                                                                            ${{ number_format($ivaDebito, 2) }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-md-4 text-center">
                                                                        <small class="text-muted">Diferencia</small><br>
                                                                        <span class="badge bg-{{ $ivaResultante >= 0 ? 'warning' : 'info' }}">
                                                                            ${{ number_format($ivaResultante, 2) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-calculator"></i>
                        Contabilidad
                    </h3>
                    <a href="{{ route('contabilidad.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nuevo Registro
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($contabilidad->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Motivo</th>
                                        <th>Valor</th>
                                        <th>Usuario</th>
                                        <th>Registrado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contabilidad as $registro)
                                        <tr>
                                            <td>
                                                <i class="fas fa-calendar text-primary"></i>
                                                {{ $registro->fecha->format('d/m/Y') }}
                                            </td>
                                            <td>{{ $registro->motivo }}</td>
                                            <td>
                                                <span class="badge bg-{{ $registro->valor >= 0 ? 'success' : 'danger' }}">
                                                    ${{ number_format($registro->valor, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-user text-secondary"></i>
                                                {{ $registro->usuario->name ?? 'Usuario no encontrado' }}
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $registro->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('contabilidad.show', $registro) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('contabilidad.edit', $registro) }}" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('contabilidad.destroy', $registro) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar este registro?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $contabilidad->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay registros de contabilidad</h4>
                            <p class="text-muted">Comienza agregando tu primer registro contable.</p>
                            <a href="{{ route('contabilidad.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Crear Primer Registro
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
