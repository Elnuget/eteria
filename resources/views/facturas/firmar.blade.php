@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-signature"></i> Firmar Factura XML
        </h1>
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
                    <table class="table table-borderless table-sm">
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
                            <td><strong>Clave de Acceso:</strong></td>
                            <td>
                                <small class="text-muted font-monospace">
                                    {{ $factura->clave_acceso }}
                                </small>
                            </td>
                        </tr>
                    </table>

                    @if($factura->xml_ruta)
                        <div class="mt-3">
                            <a href="{{ asset($factura->xml_ruta) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i> Descargar XML Original
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Visor del XML -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-code"></i> Contenido del XML
                        @if($factura->xml_ruta)
                            <small class="text-muted">- {{ basename($factura->xml_ruta) }}</small>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($factura->xml_ruta && file_exists(public_path($factura->xml_ruta)))
                        <div class="position-relative">
                            <pre class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto; font-size: 12px; line-height: 1.4;"><code id="xmlContent">{{ file_get_contents(public_path($factura->xml_ruta)) }}</code></pre>
                            
                            <button type="button" class="btn btn-outline-secondary btn-sm position-absolute top-0 end-0 m-2" onclick="copiarXML()" title="Copiar XML">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No se pudo cargar el archivo XML o no existe.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sección de Firma -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-signature"></i> Firmar Documento
                    </h5>
                </div>
                <div class="card-body">
                    @if($factura->xml_firmado_ruta)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> 
                            Esta factura ya ha sido firmada digitalmente.
                            <br>
                            <small class="text-muted">Archivo firmado: {{ basename($factura->xml_firmado_ruta) }}</small>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-muted mb-3">
                                    <i class="fas fa-info-circle"></i> 
                                    El proceso de firma digital aplicará el certificado electrónico al documento XML.
                                </p>
                                
                                <button type="button" 
                                        class="btn btn-success btn-lg" 
                                        id="btnFirmar"
                                        onclick="firmarDocumento({{ $factura->id }})">
                                    <i class="fas fa-signature"></i> Firmar Documento
                                </button>
                                
                                <!-- Área de progreso -->
                                <div id="progresoFirma" class="mt-3" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <div class="spinner-border text-primary me-2" role="status" aria-hidden="true"></div>
                                        <span>Procesando firma digital...</span>
                                    </div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                             role="progressbar" 
                                             style="width: 100%">
                                        </div>
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

<script>
function copiarXML() {
    const xmlContent = document.getElementById('xmlContent').textContent;
    navigator.clipboard.writeText(xmlContent).then(function() {
        mostrarAlerta('success', 'XML copiado al portapapeles');
    }, function(err) {
        console.error('Error al copiar: ', err);
        mostrarAlerta('danger', 'Error al copiar el XML');
    });
}

function firmarDocumento(facturaId) {
    const btnFirmar = document.getElementById('btnFirmar');
    const progreso = document.getElementById('progresoFirma');
    
    // Deshabilitar botón y mostrar progreso
    btnFirmar.disabled = true;
    btnFirmar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Firmando...';
    progreso.style.display = 'block';
    
    // Aquí se implementará la lógica de firma
    // Por ahora solo simula el proceso
    setTimeout(() => {
        mostrarAlerta('info', 'Funcionalidad de firma en desarrollo. La implementación se completará próximamente.');
        
        // Restaurar botón
        btnFirmar.disabled = false;
        btnFirmar.innerHTML = '<i class="fas fa-signature"></i> Firmar Documento';
        progreso.style.display = 'none';
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
    
    // Auto-cerrar después de 5 segundos para alertas de éxito
    if (tipo === 'success') {
        setTimeout(() => {
            const alerta = container.querySelector('.alert');
            if (alerta) {
                alerta.remove();
            }
        }, 5000);
    }
}
</script>
@endsection
