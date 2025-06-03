@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
                                <th>Acciones</th>
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
                                            <a href="{{ asset($factura->xml_firmado_ruta) }}" target="_blank" class="text-decoration-none">
                                                <small class="text-info">
                                                    <i class="fas fa-file-signature"></i> 
                                                    {{ basename($factura->xml_firmado_ruta) }}
                                                </small>
                                                <br>
                                                <small class="text-muted">Click para ver XML firmado</small>
                                            </a>
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
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($factura->xml_ruta && !$factura->xml_firmado_ruta)
                                                <a href="{{ route('facturas.firmar', $factura->id) }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Firmar XML">
                                                    <i class="fas fa-signature"></i> Firmar
                                                </a>
                                            @endif
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm" 
                                                    onclick="confirmarBorrar({{ $factura->id }}, '{{ $factura->numero_factura }}')"
                                                    title="Eliminar factura">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmarBorrar" tabindex="-1" aria-labelledby="modalConfirmarBorrarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarBorrarLabel">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar la factura <strong id="numeroFacturaModal"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> Esta acción no se puede deshacer. Se eliminarán todos los datos de la factura y los archivos asociados.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarBorrar">
                    <i class="fas fa-trash"></i> Eliminar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let facturaAEliminar = null;

function confirmarBorrar(facturaId, numeroFactura) {
    facturaAEliminar = facturaId;
    document.getElementById('numeroFacturaModal').textContent = numeroFactura;
    
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarBorrar'));
    modal.show();
}

document.getElementById('btnConfirmarBorrar').addEventListener('click', function() {
    if (facturaAEliminar) {
        eliminarFactura(facturaAEliminar);
    }
});

function eliminarFactura(facturaId) {
    const btnConfirmar = document.getElementById('btnConfirmarBorrar');
    const textOriginal = btnConfirmar.innerHTML;
    
    // Mostrar loading
    btnConfirmar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...';
    btnConfirmar.disabled = true;
    
    fetch(`/facturas/${facturaId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalConfirmarBorrar')).hide();
            
            // Mostrar mensaje de éxito
            mostrarAlerta('success', data.message);
            
            // Recargar la página después de un breve delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            mostrarAlerta('danger', data.message || 'Error al eliminar la factura');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('danger', 'Error de conexión al eliminar la factura');
    })
    .finally(() => {
        // Restaurar botón
        btnConfirmar.innerHTML = textOriginal;
        btnConfirmar.disabled = false;
        facturaAEliminar = null;
    });
}

function mostrarAlerta(tipo, mensaje) {
    const alertaHtml = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insertar la alerta al inicio del contenedor principal
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertaHtml);
}
</script>
@endsection

