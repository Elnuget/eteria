@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Filtros de Período -->
            @include('components.contabilidad.filtros-periodo')

            <!-- Sección de Compras y Ventas -->
            @if((isset($compras['compras']) && count($compras['compras']) > 0) || (isset($ventas['ventas']) && count($ventas['ventas']) > 0))
                <div class="row mb-4">
                    <!-- Sección de Compras -->
                    @if(isset($compras['compras']) && count($compras['compras']) > 0)
                        <div class="col-md-6">
                            @include('components.contabilidad.tabla-compras')
                            @include('components.contabilidad.asientos-compras')
                        </div>
                    @endif

                    <!-- Sección de Ventas -->
                    @if(isset($ventas['ventas']) && count($ventas['ventas']) > 0)
                        <div class="col-md-6">
                            @include('components.contabilidad.tabla-ventas')
                            @include('components.contabilidad.asientos-ventas')
                        </div>
                    @endif
                </div>

                <!-- Resumen General de Asientos Contables -->
                @if((isset($compras['compras']) && count($compras['compras']) > 0) || (isset($ventas['ventas']) && count($ventas['ventas']) > 0))
                    <div class="row mt-4">
                        <div class="col-md-12">
                            @include('components.contabilidad.resumen-general')
                        </div>
                    </div>
                @endif
            @endif

            <!-- Tabla de Registros Contables -->
            @include('components.contabilidad.tabla-registros')
        </div>
    </div>
</div>
@endsection
