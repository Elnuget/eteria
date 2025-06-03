@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Enviar Factura XML Firmado</h1>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
    
    <div class="row">
        <!-- Información de la Factura -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Información de la Factura
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>{{ $factura->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Número:</strong></td>
                            <td><code>{{ $factura->numero_factura }}</code></td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
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
                        </tr>
                        <tr>
                            <td><strong>Ambiente:</strong></td>
                            <td>
                                <span class="badge {{ $factura->ambiente == '2' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $factura->ambiente_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Fecha Emisión:</strong></td>
                            <td>{{ $factura->fecha_emision ? $factura->fecha_emision->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Fecha Firmado:</strong></td>
                            <td>{{ $factura->fecha_firmado ? $factura->fecha_firmado->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Clave de Acceso:</strong></td>
                            <td>
                                <small class="font-monospace">{{ $factura->clave_acceso }}</small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Botón de Envío -->
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane"></i> Enviar al SRI
                    </h5>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted mb-3">
                        El XML está firmado y listo para ser enviado al SRI para su autorización.
                    </p>
                    <button type="button" 
                            class="btn btn-success btn-lg" 
                            id="btnEnviarFactura"
                            onclick="enviarFactura({{ $factura->id }})">
                        <i class="fas fa-paper-plane"></i> Enviar Factura
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Visualización del XML -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-code"></i> XML Firmado
                        <span class="badge bg-secondary ms-2">{{ basename($factura->xml_firmado_ruta) }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($factura->xml_firmado_ruta && file_exists(public_path($factura->xml_firmado_ruta)))
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Contenido del XML:</h6>
                                <a href="{{ asset($factura->xml_firmado_ruta) }}" 
                                   target="_blank" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> Abrir en Nueva Ventana
                                </a>
                            </div>
                            <div class="bg-light p-3 rounded" style="max-height: 600px; overflow-y: auto;">
                                <pre class="mb-0"><code>{{ file_get_contents(public_path($factura->xml_firmado_ruta)) }}</code></pre>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning m-3">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No se pudo cargar el archivo XML firmado.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function enviarFactura(facturaId) {
    const btn = document.getElementById('btnEnviarFactura');
    const textOriginal = btn.innerHTML;
    
    // Mostrar loading
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
    btn.disabled = true;
    
    // Aquí puedes implementar la lógica de envío cuando esté lista
    // Por ahora solo simula el envío
    setTimeout(() => {
        mostrarAlerta('info', 'Funcionalidad de envío será implementada próximamente');
        
        // Restaurar botón
        btn.innerHTML = textOriginal;
        btn.disabled = false;
    }, 2000);
}

function mostrarAlerta(tipo, mensaje) {
    const alertaHtml = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'info' ? 'info-circle' : 'exclamation-circle'}"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insertar la alerta al inicio del contenedor principal
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertaHtml);
}
</script>
@endsection
