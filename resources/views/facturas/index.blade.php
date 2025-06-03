@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Facturas SRI</h1>
        <a href="{{ route('facturas.generarxml') }}" class="btn btn-primary">
            <i class="fas fa-file-code"></i> Crear Nueva Factura XML
        </a>
    </div>
    
    @if($facturas->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-table"></i> Listado de Facturas
                    <span class="badge bg-secondary ms-2">{{ $facturas->count() }} registros</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Número Factura</th>
                                <th>Clave de Acceso</th>
                                <th>Estado</th>
                                <th>Ambiente</th>
                                <th>Fecha Emisión</th>
                                <th>XML Ruta</th>
                                <th>XML Firmado</th>
                                <th>PDF Ruta</th>
                                <th>Certificado</th>
                                <th>Fecha Firmado</th>
                                <th>Fecha Recepción</th>
                                <th>Fecha Autorización</th>
                                <th>Número Autorización</th>
                                <th>Observaciones</th>
                                <th>Creado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facturas as $factura)
                                <tr>
                                    <td><strong>{{ $factura->id }}</strong></td>
                                    <td>
                                        <code>{{ $factura->numero_factura }}</code>
                                    </td>
                                    <td>
                                        <small class="text-muted font-monospace">
                                            {{ substr($factura->clave_acceso, 0, 20) }}...
                                        </small>
                                        <br>
                                        <small class="text-info">{{ strlen($factura->clave_acceso) }} dígitos</small>
                                    </td>
                                    <td>
                                        @php
                                            $estadoClass = match($factura->estado) {
                                                'PENDIENTE' => 'bg-warning text-dark',
                                                'FIRMADO' => 'bg-info',
                                                'RECIBIDA' => 'bg-primary',
                                                'AUTORIZADA' => 'bg-success',
                                                'NO_AUTORIZADA' => 'bg-danger',
                                                'DEVUELTA' => 'bg-secondary',
                                                default => 'bg-light text-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $estadoClass }}">
                                            {{ $factura->estado_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $factura->ambiente == '2' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $factura->ambiente_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $factura->fecha_emision ? $factura->fecha_emision->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        @if($factura->xml_ruta)
                                            <a href="{{ asset($factura->xml_ruta) }}" target="_blank" class="text-decoration-none">
                                                <small class="text-success">
                                                    <i class="fas fa-file-code"></i> 
                                                    {{ basename($factura->xml_ruta) }}
                                                </small>
                                                <br>
                                                <small class="text-muted">Click para ver XML</small>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($factura->xml_firmado_ruta)
                                            <small class="text-info">
                                                <i class="fas fa-file-signature"></i> 
                                                {{ basename($factura->xml_firmado_ruta) }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($factura->pdf_ruta)
                                            <small class="text-danger">
                                                <i class="fas fa-file-pdf"></i> 
                                                {{ basename($factura->pdf_ruta) }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($factura->certificado_propietario)
                                            <small>
                                                <strong>{{ $factura->certificado_propietario }}</strong><br>
                                                @if($factura->certificado_serial)
                                                    <span class="text-muted">Serial: {{ substr($factura->certificado_serial, 0, 10) }}...</span><br>
                                                @endif
                                                @if($factura->certificado_vigencia_hasta)
                                                    <span class="text-{{ $factura->certificado_vigencia_hasta->isPast() ? 'danger' : 'success' }}">
                                                        Vigente hasta: {{ $factura->certificado_vigencia_hasta->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $factura->fecha_firmado ? $factura->fecha_firmado->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $factura->fecha_recepcion ? $factura->fecha_recepcion->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $factura->fecha_autorizacion ? $factura->fecha_autorizacion->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        @if($factura->numero_autorizacion)
                                            <small class="font-monospace text-success">
                                                {{ substr($factura->numero_autorizacion, 0, 15) }}...
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($factura->observaciones)
                                            <small class="text-muted">
                                                {{ Str::limit($factura->observaciones, 30) }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $factura->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Resumen de Estados -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Resumen por Estados</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $estadosCount = $facturas->groupBy('estado')->map->count();
                                $ambientesCount = $facturas->groupBy('ambiente')->map->count();
                            @endphp
                            
                            <div class="col-md-6">
                                <h6>Estados:</h6>
                                @foreach($estadosCount as $estado => $count)
                                    @php
                                        $estadoClass = match($estado) {
                                            'PENDIENTE' => 'bg-warning text-dark',
                                            'FIRMADO' => 'bg-info',
                                            'RECIBIDA' => 'bg-primary',
                                            'AUTORIZADA' => 'bg-success',
                                            'NO_AUTORIZADA' => 'bg-danger',
                                            'DEVUELTA' => 'bg-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $estadoClass }} me-2 mb-1">
                                        {{ \App\Models\Factura::ESTADOS[$estado] ?? $estado }}: {{ $count }}
                                    </span>
                                @endforeach
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Ambientes:</h6>
                                @foreach($ambientesCount as $ambiente => $count)
                                    <span class="badge {{ $ambiente == '2' ? 'bg-success' : 'bg-warning text-dark' }} me-2 mb-1">
                                        {{ \App\Models\Factura::AMBIENTES[$ambiente] ?? $ambiente }}: {{ $count }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay facturas registradas</h5>
                <p class="text-muted">Crea tu primera factura haciendo clic en el botón "Crear Nueva Factura XML"</p>
                <a href="{{ route('facturas.generarxml') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primera Factura
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

