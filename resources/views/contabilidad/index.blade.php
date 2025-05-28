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
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Descripción</th>
                                                    <th>Cuenta</th>
                                                    <th class="text-end">Debe</th>
                                                    <th class="text-end">Haber</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $totalDebeCompras = 0;
                                                    $totalHaberCompras = 0;
                                                @endphp
                                                @foreach($compras['compras'] as $compra)
                                                    @php
                                                        $totalValue = $compra['total_value'] ?? 0;
                                                        $totalDebeCompras += $totalValue;
                                                        $totalHaberCompras += $totalValue;
                                                    @endphp
                                                    <!-- Cuenta de Gasto (Debe) -->
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y', $compra['invoice_date'])->format('d/m/Y') }}</td>
                                                        <td>Compra a {{ $compra['supplier_name'] }}</td>
                                                        <td>Gastos de Operación</td>
                                                        <td class="text-end text-success">
                                                            <strong>${{ number_format($totalValue, 2) }}</strong>
                                                        </td>
                                                        <td class="text-end">-</td>
                                                    </tr>
                                                    <!-- Cuenta por Pagar (Haber) -->
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="ps-4">Cuentas por Pagar</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end text-danger">
                                                            <strong>${{ number_format($totalValue, 2) }}</strong>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="3" class="text-end">TOTALES:</th>
                                                    <th class="text-end">
                                                        <span class="badge bg-success">
                                                            ${{ number_format($totalDebeCompras, 2) }}
                                                        </span>
                                                    </th>
                                                    <th class="text-end">
                                                        <span class="badge bg-danger">
                                                            ${{ number_format($totalHaberCompras, 2) }}
                                                        </span>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="5" class="text-center">
                                                        @if($totalDebeCompras == $totalHaberCompras)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check"></i>
                                                                Asiento Balanceado
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                Asiento Desbalanceado
                                                            </span>
                                                        @endif
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Descripción</th>
                                                    <th>Cuenta</th>
                                                    <th class="text-end">Debe</th>
                                                    <th class="text-end">Haber</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $totalDebeVentas = 0;
                                                    $totalHaberVentas = 0;
                                                @endphp
                                                @foreach($ventas['ventas'] as $venta)
                                                    @php
                                                        $totalValue = $venta['total_value'] ?? 0;
                                                        $totalDebeVentas += $totalValue;
                                                        $totalHaberVentas += $totalValue;
                                                    @endphp
                                                    <!-- Cuenta por Cobrar (Debe) -->
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y', $venta['invoice_date'])->format('d/m/Y') }}</td>
                                                        <td>Venta a {{ $venta['customer_name'] ?? 'Cliente' }}</td>
                                                        <td>Cuentas por Cobrar</td>
                                                        <td class="text-end text-success">
                                                            <strong>${{ number_format($totalValue, 2) }}</strong>
                                                        </td>
                                                        <td class="text-end">-</td>
                                                    </tr>
                                                    <!-- Ingreso por Ventas (Haber) -->
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="ps-4">Ingresos por Ventas</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end text-danger">
                                                            <strong>${{ number_format($totalValue, 2) }}</strong>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="3" class="text-end">TOTALES:</th>
                                                    <th class="text-end">
                                                        <span class="badge bg-success">
                                                            ${{ number_format($totalDebeVentas, 2) }}
                                                        </span>
                                                    </th>
                                                    <th class="text-end">
                                                        <span class="badge bg-danger">
                                                            ${{ number_format($totalHaberVentas, 2) }}
                                                        </span>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="5" class="text-center">
                                                        @if($totalDebeVentas == $totalHaberVentas)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check"></i>
                                                                Asiento Balanceado
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                Asiento Desbalanceado
                                                            </span>
                                                        @endif
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
                                                        @endphp
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>Gastos de Operación (Debe):</span>
                                                            <span class="badge bg-success fs-6">${{ number_format($totalComprasGeneral, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>Cuentas por Pagar (Haber):</span>
                                                            <span class="badge bg-danger fs-6">${{ number_format($totalComprasGeneral, 2) }}</span>
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
                                                        @endphp
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>Cuentas por Cobrar (Debe):</span>
                                                            <span class="badge bg-success fs-6">${{ number_format($totalVentasGeneral, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>Ingresos por Ventas (Haber):</span>
                                                            <span class="badge bg-danger fs-6">${{ number_format($totalVentasGeneral, 2) }}</span>
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
                                                <div class="card-body text-center">
                                                    @php
                                                        $totalDebeGeneral = 0;
                                                        $totalHaberGeneral = 0;
                                                        
                                                        if(isset($compras['compras'])) {
                                                            $totalCompras = array_sum(array_column($compras['compras'], 'total_value'));
                                                            $totalDebeGeneral += $totalCompras;
                                                            $totalHaberGeneral += $totalCompras;
                                                        }
                                                        
                                                        if(isset($ventas['ventas'])) {
                                                            $totalVentas = array_sum(array_column($ventas['ventas'], 'total_value'));
                                                            $totalDebeGeneral += $totalVentas;
                                                            $totalHaberGeneral += $totalVentas;
                                                        }
                                                    @endphp
                                                    <h5 class="mb-3">Balance General de Asientos</h5>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <h6>Total Debe:</h6>
                                                            <span class="badge bg-success fs-5">
                                                                ${{ number_format($totalDebeGeneral, 2) }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <h6>Total Haber:</h6>
                                                            <span class="badge bg-danger fs-5">
                                                                ${{ number_format($totalHaberGeneral, 2) }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-4">
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
