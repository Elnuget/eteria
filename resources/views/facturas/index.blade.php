@extends('layouts.app')

@section('title', 'Gestión de Facturas')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestión de Facturas Electrónicas</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#facturaModal" onclick="abrirModalCrear()">
                        <i class="fas fa-plus"></i> Nueva Factura
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="filtroEstado">
                                <option value="">Todos los estados</option>
                                <option value="PENDIENTE">Pendiente</option>
                                <option value="FIRMADO">Firmado</option>
                                <option value="RECIBIDA">Recibida</option>
                                <option value="DEVUELTA">Devuelta</option>
                                <option value="AUTORIZADA">Autorizada</option>
                                <option value="NO_AUTORIZADA">No Autorizada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filtroAmbiente">
                                <option value="">Todos los ambientes</option>
                                <option value="1">Pruebas</option>
                                <option value="2">Producción</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="busqueda" placeholder="Buscar por número de factura o clave de acceso...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="aplicarFiltros()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de facturas -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Clave de Acceso</th>
                                    <th>Estado</th>
                                    <th>Ambiente</th>
                                    <th>Fecha Emisión</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facturas as $factura)
                                <tr>
                                    <td>{{ $factura->numero_factura }}</td>
                                    <td class="font-monospace">{{ $factura->clave_acceso }}</td>
                                    <td>
                                        <span class="badge bg-{{ getEstadoBadgeColor($factura->estado) }}">
                                            {{ $factura->estado_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $factura->ambiente == '2' ? 'success' : 'warning' }}">
                                            {{ $factura->ambiente_label }}
                                        </span>
                                    </td>
                                    <td>{{ $factura->fecha_emision->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-info" onclick="verFactura({{ $factura->id }})" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" onclick="editarFactura({{ $factura->id }})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="eliminarFactura({{ $factura->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay facturas registradas</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $facturas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Factura -->
<div class="modal fade" id="facturaModal" tabindex="-1" aria-labelledby="facturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="facturaModalLabel">Nueva Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="facturaForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-file-invoice"></i> Información Básica</h6>
                            
                            <div class="mb-3">
                                <label for="numero_factura" class="form-label">Número de Factura *</label>
                                <input type="text" class="form-control" id="numero_factura" name="numero_factura" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="clave_acceso" class="form-label">Clave de Acceso *</label>
                                <input type="text" class="form-control font-monospace" id="clave_acceso" name="clave_acceso" maxlength="49" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estado" class="form-label">Estado *</label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="PENDIENTE">Pendiente</option>
                                            <option value="FIRMADO">Firmado</option>
                                            <option value="RECIBIDA">Recibida</option>
                                            <option value="DEVUELTA">Devuelta</option>
                                            <option value="AUTORIZADA">Autorizada</option>
                                            <option value="NO_AUTORIZADA">No Autorizada</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ambiente" class="form-label">Ambiente *</label>
                                        <select class="form-select" id="ambiente" name="ambiente" required>
                                            <option value="1">Pruebas</option>
                                            <option value="2">Producción</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="fecha_emision" class="form-label">Fecha de Emisión *</label>
                                <input type="datetime-local" class="form-control" id="fecha_emision" name="fecha_emision" required>
                            </div>
                        </div>

                        <!-- Archivos -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-folder"></i> Archivos</h6>
                            
                            <div class="mb-3">
                                <label for="xml_ruta" class="form-label">Ruta XML</label>
                                <input type="text" class="form-control" id="xml_ruta" name="xml_ruta">
                            </div>
                            
                            <div class="mb-3">
                                <label for="xml_firmado_ruta" class="form-label">Ruta XML Firmado</label>
                                <input type="text" class="form-control" id="xml_firmado_ruta" name="xml_firmado_ruta">
                            </div>
                            
                            <div class="mb-3">
                                <label for="pdf_ruta" class="form-label">Ruta PDF</label>
                                <input type="text" class="form-control" id="pdf_ruta" name="pdf_ruta">
                            </div>
                        </div>
                    </div>

                    <!-- Información de certificado -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="fas fa-certificate"></i> Certificado Digital</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="certificado_ruta" class="form-label">Ruta Certificado (.p12)</label>
                                <input type="text" class="form-control" id="certificado_ruta" name="certificado_ruta">
                            </div>
                            
                            <div class="mb-3">
                                <label for="certificado_password" class="form-label">Password Certificado</label>
                                <input type="password" class="form-control" id="certificado_password" name="certificado_password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="certificado_serial" class="form-label">Serial Certificado</label>
                                <input type="text" class="form-control" id="certificado_serial" name="certificado_serial">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="certificado_propietario" class="form-label">Propietario Certificado</label>
                                <input type="text" class="form-control" id="certificado_propietario" name="certificado_propietario">
                            </div>
                            
                            <div class="mb-3">
                                <label for="certificado_vigencia_hasta" class="form-label">Vigencia Hasta</label>
                                <input type="date" class="form-control" id="certificado_vigencia_hasta" name="certificado_vigencia_hasta">
                            </div>
                            
                            <div class="mb-3">
                                <label for="fecha_firmado" class="form-label">Fecha Firmado</label>
                                <input type="datetime-local" class="form-control" id="fecha_firmado" name="fecha_firmado">
                            </div>
                        </div>
                    </div>

                    <!-- Fechas adicionales -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="fas fa-calendar"></i> Fechas Adicionales</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="fecha_recepcion" class="form-label">Fecha Recepción</label>
                                <input type="datetime-local" class="form-control" id="fecha_recepcion" name="fecha_recepcion">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="fecha_autorizacion" class="form-label">Fecha Autorización</label>
                                <input type="datetime-local" class="form-control" id="fecha_autorizacion" name="fecha_autorizacion">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="numero_autorizacion" class="form-label">Número Autorización</label>
                                <input type="text" class="form-control" id="numero_autorizacion" name="numero_autorizacion">
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Factura</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Factura -->
<div class="modal fade" id="verFacturaModal" tabindex="-1" aria-labelledby="verFacturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verFacturaModalLabel">Detalles de la Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detallesFactura">
                <!-- Los detalles se cargarán aquí dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let facturaEditando = null;

// Abrir modal para crear nueva factura
function abrirModalCrear() {
    facturaEditando = null;
    document.getElementById('facturaModalLabel').textContent = 'Nueva Factura';
    document.getElementById('facturaForm').reset();
    // Establecer fecha actual
    const ahora = new Date();
    const fechaLocal = new Date(ahora.getTime() - ahora.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    document.getElementById('fecha_emision').value = fechaLocal;
}

// Ver detalles de una factura
async function verFactura(id) {
    try {
        const response = await fetch(`/facturas/${id}`);
        const factura = await response.json();
        
        const detallesHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Información Básica</h6>
                    <p><strong>Número:</strong> ${factura.numero_factura}</p>
                    <p><strong>Clave de Acceso:</strong> <span class="font-monospace">${factura.clave_acceso}</span></p>
                    <p><strong>Estado:</strong> <span class="badge bg-primary">${factura.estado}</span></p>
                    <p><strong>Ambiente:</strong> <span class="badge bg-${factura.ambiente == '2' ? 'success' : 'warning'}">${factura.ambiente == '1' ? 'Pruebas' : 'Producción'}</span></p>
                    <p><strong>Fecha Emisión:</strong> ${new Date(factura.fecha_emision).toLocaleString()}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Archivos</h6>
                    <p><strong>XML:</strong> ${factura.xml_ruta || 'No especificado'}</p>
                    <p><strong>XML Firmado:</strong> ${factura.xml_firmado_ruta || 'No especificado'}</p>
                    <p><strong>PDF:</strong> ${factura.pdf_ruta || 'No especificado'}</p>
                </div>
            </div>
            ${factura.observaciones ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="text-primary">Observaciones</h6>
                    <p>${factura.observaciones}</p>
                </div>
            </div>
            ` : ''}
        `;
        
        document.getElementById('detallesFactura').innerHTML = detallesHtml;
        new bootstrap.Modal(document.getElementById('verFacturaModal')).show();
    } catch (error) {
        console.error('Error al cargar la factura:', error);
        alert('Error al cargar los detalles de la factura');
    }
}

// Editar factura
async function editarFactura(id) {
    try {
        const response = await fetch(`/facturas/${id}`);
        const factura = await response.json();
        
        facturaEditando = id;
        document.getElementById('facturaModalLabel').textContent = 'Editar Factura';
        
        // Llenar el formulario con los datos de la factura
        Object.keys(factura).forEach(key => {
            const input = document.getElementById(key);
            if (input) {
                if (input.type === 'datetime-local' && factura[key]) {
                    // Convertir fecha para datetime-local
                    const fecha = new Date(factura[key]);
                    input.value = new Date(fecha.getTime() - fecha.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
                } else if (input.type === 'date' && factura[key]) {
                    input.value = factura[key];
                } else {
                    input.value = factura[key] || '';
                }
            }
        });
        
        new bootstrap.Modal(document.getElementById('facturaModal')).show();
    } catch (error) {
        console.error('Error al cargar la factura:', error);
        alert('Error al cargar los datos de la factura');
    }
}

// Eliminar factura
async function eliminarFactura(id) {
    if (!confirm('¿Está seguro de que desea eliminar esta factura?')) {
        return;
    }
    
    try {
        const response = await fetch(`/facturas/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload(); // Recargar la página para actualizar la lista
        } else {
            alert('Error al eliminar la factura');
        }
    } catch (error) {
        console.error('Error al eliminar la factura:', error);
        alert('Error al eliminar la factura');
    }
}

// Manejar envío del formulario
document.getElementById('facturaForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const url = facturaEditando ? `/facturas/${facturaEditando}` : '/facturas';
        const method = facturaEditando ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            bootstrap.Modal.getInstance(document.getElementById('facturaModal')).hide();
            location.reload(); // Recargar la página para actualizar la lista
        } else {
            alert('Error al guardar la factura');
        }
    } catch (error) {
        console.error('Error al guardar la factura:', error);
        alert('Error al guardar la factura');
    }
});

// Aplicar filtros (función básica, se puede mejorar)
function aplicarFiltros() {
    const estado = document.getElementById('filtroEstado').value;
    const ambiente = document.getElementById('filtroAmbiente').value;
    const busqueda = document.getElementById('busqueda').value;
    
    // Construir URL con parámetros de filtro
    const params = new URLSearchParams();
    if (estado) params.append('estado', estado);
    if (ambiente) params.append('ambiente', ambiente);
    if (busqueda) params.append('busqueda', busqueda);
    
    const url = params.toString() ? `?${params.toString()}` : '';
    window.location.href = url;
}
</script>
@endsection

@php
function getEstadoBadgeColor($estado) {
    return match($estado) {
        'PENDIENTE' => 'warning',
        'FIRMADO' => 'info',
        'RECIBIDA' => 'primary',
        'DEVUELTA' => 'danger',
        'AUTORIZADA' => 'success',
        'NO_AUTORIZADA' => 'danger',
        default => 'secondary'
    };
}
@endphp
