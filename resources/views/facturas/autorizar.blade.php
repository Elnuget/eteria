@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Autorizar Factura XML</h1>
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
                            <td><strong>Fecha Recepción:</strong></td>
                            <td>{{ $factura->fecha_recepcion ? $factura->fecha_recepcion->format('d/m/Y H:i') : '-' }}</td>
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
            
            <!-- Botón de Autorización -->
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle"></i> Autorizar Factura
                    </h5>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted mb-3">
                        La factura ha sido recibida por el SRI. Proceda con la autorización final.
                    </p>
                    <button type="button" 
                            class="btn btn-info btn-lg" 
                            id="btnAutorizarFactura"
                            onclick="autorizarFactura({{ $factura->id }})">
                        <i class="fas fa-check-circle"></i> Autorizar Factura
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Visualización del XML Firmado -->
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
                                <h6 class="mb-0">Contenido del XML Firmado:</h6>
                                <div>
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-sm me-2" 
                                            onclick="copiarXML()">
                                        <i class="fas fa-copy"></i> Copiar
                                    </button>
                                    <a href="{{ asset($factura->xml_firmado_ruta) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt"></i> Abrir en Nueva Ventana
                                    </a>
                                </div>
                            </div>
                            <div class="bg-light p-3 rounded" style="max-height: 600px; overflow-y: auto;">
                                <pre class="mb-0" id="xmlContent"><code>{{ file_get_contents(public_path($factura->xml_firmado_ruta)) }}</code></pre>
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

<!-- Modal de Éxito (AUTORIZADA) -->
<div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalExitoLabel">
                    <i class="fas fa-check-circle"></i> Factura Autorizada por el SRI
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <h6><i class="fas fa-info-circle"></i> Estado: <span id="estadoExito"></span></h6>
                    <p><strong>Mensaje:</strong> <span id="mensajeExito"></span></p>
                    <div id="infoAdicionalExito" style="display: none;">
                        <p><strong>Información Adicional:</strong> <span id="infoAdicionalTexto"></span></p>
                    </div>
                    <p><strong>Número de Autorización:</strong> <span id="numeroAutorizacion"></span></p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información de la Factura:</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Número:</strong> {{ $factura->numero_factura }}</li>
                            <li class="list-group-item"><strong>Clave de Acceso:</strong> <small class="font-monospace">{{ $factura->clave_acceso }}</small></li>
                            <li class="list-group-item"><strong>Fecha de Autorización:</strong> <span id="fechaAutorizacion"></span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Estado Final:</h6>
                        <div class="alert alert-success">
                            <p class="mb-1"><i class="fas fa-check-circle"></i> La factura ha sido autorizada correctamente por el SRI.</p>
                            <p class="mb-0"><i class="fas fa-info-circle"></i> El proceso de facturación electrónica está completo.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="fas fa-check"></i> Entendido
                </button>
                <a href="{{ route('facturas.index') }}" class="btn btn-outline-success">
                    <i class="fas fa-list"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Error (NO_AUTORIZADA) -->
<div class="modal fade" id="modalError" tabindex="-1" aria-labelledby="modalErrorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalErrorLabel">
                    <i class="fas fa-exclamation-triangle"></i> Factura No Autorizada por el SRI
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6><i class="fas fa-times-circle"></i> Estado: <span id="estadoError"></span></h6>
                    <p><strong>Mensaje de Error:</strong> <span id="mensajeError"></span></p>
                    <div id="infoAdicionalError" style="display: none;">
                        <p><strong>Información Adicional:</strong> <span id="infoAdicionalErrorTexto"></span></p>
                    </div>
                    <p><strong>Tipo:</strong> <span id="tipoError"></span></p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información de la Factura:</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Número:</strong> {{ $factura->numero_factura }}</li>
                            <li class="list-group-item"><strong>Clave de Acceso:</strong> <small class="font-monospace">{{ $factura->clave_acceso }}</small></li>
                            <li class="list-group-item"><strong>Fecha de Intento:</strong> <span id="fechaIntento"></span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Qué Hacer Ahora:</h6>
                        <div class="alert alert-warning">
                            <p class="mb-1"><i class="fas fa-tools"></i> La factura no fue autorizada por errores.</p>
                            <p class="mb-1"><i class="fas fa-edit"></i> Revise y corrija los errores indicados.</p>
                            <p class="mb-0"><i class="fas fa-redo"></i> Genere una nueva factura con los datos corregidos.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <a href="{{ route('facturas.generarxml') }}" class="btn btn-outline-warning">
                    <i class="fas fa-plus"></i> Generar Nueva Factura
                </a>
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

function autorizarFactura(facturaId) {
    const btn = document.getElementById('btnAutorizarFactura');
    const textOriginal = btn.innerHTML;
    
    // Mostrar loading
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Autorizando...';
    btn.disabled = true;
    
    // Obtener el token CSRF
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // TODO: Implementar envío al SRI para autorización
    // Por ahora simular respuesta
    setTimeout(() => {
        // Simular respuesta exitosa o con error
        const esExitosa = Math.random() > 0.3; // 70% de éxito
        
        if (esExitosa) {
            const data = {
                success: true,
                estado: 'AUTORIZADA',
                mensaje: 'COMPROBANTE AUTORIZADO',
                numero_autorizacion: '2406202501' + Math.random().toString().substr(2, 39),
                fecha_autorizacion: new Date().toISOString()
            };
            mostrarModalExito(data);
        } else {
            const data = {
                success: false,
                estado: 'NO_AUTORIZADA',
                mensaje: 'ERROR EN VALIDACIÓN DE COMPROBANTE',
                informacion_adicional: 'El certificado digital ha expirado',
                tipo: 'ERROR'
            };
            mostrarModalError(data);
        }
        
        // Restaurar botón
        btn.innerHTML = textOriginal;
        btn.disabled = false;
    }, 3000);
}

function mostrarModalExito(data) {
    document.getElementById('estadoExito').textContent = data.estado;
    document.getElementById('mensajeExito').textContent = data.mensaje;
    document.getElementById('numeroAutorizacion').textContent = data.numero_autorizacion || 'N/A';
    document.getElementById('fechaAutorizacion').textContent = new Date().toLocaleString('es-EC');
    
    if (data.informacion_adicional) {
        document.getElementById('infoAdicionalTexto').textContent = data.informacion_adicional;
        document.getElementById('infoAdicionalExito').style.display = 'block';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalExito'));
    modal.show();
}

function mostrarModalError(data) {
    document.getElementById('estadoError').textContent = data.estado;
    document.getElementById('mensajeError').textContent = data.mensaje;
    document.getElementById('tipoError').textContent = data.tipo || 'ERROR';
    document.getElementById('fechaIntento').textContent = new Date().toLocaleString('es-EC');
    
    if (data.informacion_adicional) {
        document.getElementById('infoAdicionalErrorTexto').textContent = data.informacion_adicional;
        document.getElementById('infoAdicionalError').style.display = 'block';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalError'));
    modal.show();
}

function mostrarAlerta(tipo, mensaje) {
    const alertaHtml = `
        <div class="alert alert-${tipo} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;" 
             role="alert">
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertaHtml);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        const alerta = document.querySelector('.alert:last-of-type');
        if (alerta) {
            alerta.remove();
        }
    }, 5000);
}
</script>
@endsection
