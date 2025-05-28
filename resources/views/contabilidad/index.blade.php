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
                                                    <th>RUC</th>
                                                    <th>Factura</th>
                                                    <th>Fecha</th>
                                                    <th>Total</th>
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
                                                        <td>{{ $compra['ruc'] }}</td>
                                                        <td>{{ $compra['invoice_number'] }}</td>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y', $compra['invoice_date'])->format('d/m/Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-danger">
                                                                ${{ number_format($compra['total_value'] ?? 0, 2) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="4" class="text-end">Total de Compras:</th>
                                                    <th>
                                                        <span class="badge bg-danger">
                                                            @php
                                                                $totalComprasValue = array_sum(array_column($compras['compras'], 'total_value'));
                                                            @endphp
                                                            ${{ number_format($totalComprasValue, 2) }}
                                                        </span>
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
                                                    <th>RUC</th>
                                                    <th>Factura</th>
                                                    <th>Fecha</th>
                                                    <th>Total</th>
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
                                                        <td>{{ $venta['ruc'] }}</td>
                                                        <td>{{ $venta['invoice_number'] }}</td>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y', $venta['invoice_date'])->format('d/m/Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                ${{ number_format($venta['total_value'] ?? 0, 2) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="4" class="text-end">Total de Ventas:</th>
                                                    <th>
                                                        <span class="badge bg-success">
                                                            @php
                                                                $totalVentasValue = array_sum(array_column($ventas['ventas'], 'total_value'));
                                                            @endphp
                                                            ${{ number_format($totalVentasValue, 2) }}
                                                        </span>
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
