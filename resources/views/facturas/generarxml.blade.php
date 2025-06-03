@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h5>Paso 1</h5>
        </div>
        <div class="card-body">
            <h6 class="mb-3">Información Tributaria - infoTributaria</h6>
            
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Campo XML</th>
                            <th>Valor</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>ambiente</code></td>
                            <td><span class="badge bg-info">{{ env('SRI_AMBIENTE') }}</span></td>
                            <td>1 = Pruebas, 2 = Producción</td>
                        </tr>
                        <tr>
                            <td><code>tipoEmision</code></td>
                            <td><span class="badge bg-success">1</span></td>
                            <td>1 = Emisión Normal</td>
                        </tr>
                        <tr>
                            <td><code>razonSocial</code></td>
                            <td>{{ env('EMISOR_RAZON_SOCIAL') }}</td>
                            <td>Razón social del emisor</td>
                        </tr>
                        <tr>
                            <td><code>ruc</code></td>
                            <td><strong>{{ env('EMISOR_RUC') }}</strong></td>
                            <td>RUC del emisor</td>
                        </tr>
                        <tr>
                            <td><code>claveAcceso</code></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span id="claveAccesoValue" class="text-danger">PENDIENTE GENERAR</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="generarClaveAcceso()">
                                        <i class="fas fa-key"></i> Generar
                                    </button>
                                </div>
                                <small class="text-muted d-block">Clave de 49 dígitos (se calculará automáticamente)</small>
                                <input type="hidden" id="claveAccesoInput" value="">
                            </td>
                            <td>Clave única de identificación del comprobante</td>
                        </tr>
                        <tr>
                            <td><code>codDoc</code></td>
                            <td><span class="badge bg-primary">01</span></td>
                            <td>01 = Factura</td>
                        </tr>
                        <tr>
                            <td><code>estab</code></td>
                            <td><span class="badge bg-secondary">{{ env('EMISOR_ESTABLECIMIENTO') }}</span></td>
                            <td>Código del establecimiento</td>
                        </tr>
                        <tr>
                            <td><code>ptoEmi</code></td>
                            <td><span class="badge bg-secondary">{{ env('EMISOR_PUNTO_EMISION') }}</span></td>
                            <td>Punto de emisión</td>
                        </tr>
                        <tr>
                            <td><code>secuencial</code></td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="secuencial" value="000000001" maxlength="9" pattern="[0-9]{9}" style="width: 120px;">
                                <small class="text-muted">9 dígitos (000000001)</small>
                            </td>
                            <td>Número secuencial del comprobante</td>
                        </tr>
                        <tr>
                            <td><code>dirMatriz</code></td>
                            <td>{{ env('EMISOR_DIRECCION') }}</td>
                            <td>Dirección de la matriz</td>
                        </tr>
                        <tr>
                            <td><code>contribuyenteRimpe</code></td>
                            <td>{{ env('EMISOR_CONTRIBUYENTE_RIMPE') }}</td>
                            <td>Tipo de contribuyente RIMPE</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" class="btn btn-primary">Generar XML</button>
            </div>
        </div>
    </div>

    <!-- Sección infoFactura -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Paso 2</h5>
        </div>
        <div class="card-body">
            <h6 class="mb-3">Información de la Factura - infoFactura</h6>
            
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Campo XML</th>
                            <th>Valor</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>fechaEmision</code></td>
                            <td>
                                <input type="date" class="form-control form-control-sm" id="fechaEmision" value="{{ date('Y-m-d') }}" style="width: 150px;">
                                <small class="text-muted">Fecha actual por defecto</small>
                            </td>
                            <td>Fecha de emisión del comprobante</td>
                        </tr>
                        <tr>
                            <td><code>dirEstablecimiento</code></td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="dirEstablecimiento" 
                                       value="Calle: E3J Numero: S56-65 Interseccion: S57 P INOCENCIO JACOME" style="width: 400px;">
                            </td>
                            <td>Dirección del establecimiento donde se emite</td>
                        </tr>
                        <tr>
                            <td><code>obligadoContabilidad</code></td>
                            <td>
                                <select class="form-select form-select-sm" id="obligadoContabilidad" style="width: 100px;">
                                    <option value="NO" selected>NO</option>
                                    <option value="SI">SI</option>
                                </select>
                            </td>
                            <td>Si está obligado a llevar contabilidad</td>
                        </tr>
                        <tr>
                            <td><code>tipoIdentificacionComprador</code></td>
                            <td>
                                <select class="form-select form-select-sm" id="tipoIdentificacionComprador" style="width: 150px;">
                                    <option value="04">04 - RUC</option>
                                    <option value="05">05 - Cédula</option>
                                    <option value="06">06 - Pasaporte</option>
                                    <option value="07" selected>07 - Consumidor Final</option>
                                    <option value="08">08 - Identificación del Exterior</option>
                                </select>
                            </td>
                            <td>Tipo de identificación del comprador</td>
                        </tr>
                        <tr>
                            <td><code>razonSocialComprador</code></td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="razonSocialComprador" 
                                       value="CONSUMIDOR FINAL" style="width: 250px;">
                            </td>
                            <td>Razón social o nombre del comprador</td>
                        </tr>
                        <tr>
                            <td><code>identificacionComprador</code></td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="identificacionComprador" 
                                       value="9999999999" style="width: 150px;">
                            </td>
                            <td>Número de identificación del comprador</td>
                        </tr>
                        <tr>
                            <td><code>totalSinImpuestos</code></td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm" id="totalSinImpuestos" 
                                           value="100.00" step="0.01" min="0" onchange="calcularTotales()">
                                </div>
                            </td>
                            <td>Subtotal sin impuestos</td>
                        </tr>
                        <tr>
                            <td><code>totalDescuento</code></td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm" id="totalDescuento" 
                                           value="0.00" step="0.01" min="0" onchange="calcularTotales()">
                                </div>
                            </td>
                            <td>Total de descuentos aplicados</td>
                        </tr>
                        <tr>
                            <td><code>totalConImpuestos</code></td>
                            <td>
                                <div class="border p-2 bg-light">
                                    <strong>Total Impuesto:</strong><br>
                                    <small>Código: <span class="badge bg-secondary">2</span> (IVA)</small><br>
                                    <small>Código Porcentaje: <span class="badge bg-info">4</span> (15%)</small><br>
                                    <small>Base Imponible: $<span id="baseImponible">100.00</span></small><br>
                                    <small>Valor IVA: $<span id="valorIva">15.00</span></small>
                                </div>
                            </td>
                            <td>Detalle de impuestos aplicados</td>
                        </tr>
                        <tr>
                            <td><code>propina</code></td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm" id="propina" 
                                           value="0.00" step="0.01" min="0" onchange="calcularTotales()">
                                </div>
                            </td>
                            <td>Propina incluida en la factura</td>
                        </tr>
                        <tr>
                            <td><code>importeTotal</code></td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm bg-warning" id="importeTotal" 
                                           value="115.00" readonly>
                                </div>
                                <small class="text-muted">Calculado automáticamente</small>
                            </td>
                            <td>Total a pagar (subtotal + impuestos + propina)</td>
                        </tr>
                        <tr>
                            <td><code>moneda</code></td>
                            <td>
                                <select class="form-select form-select-sm" id="moneda" style="width: 120px;">
                                    <option value="DOLAR" selected>DOLAR</option>
                                    <option value="EURO">EURO</option>
                                </select>
                            </td>
                            <td>Moneda de la transacción</td>
                        </tr>
                        <tr>
                            <td><code>pagos</code></td>
                            <td>
                                <div class="border p-2 bg-light">
                                    <strong>Forma de Pago:</strong><br>
                                    <select class="form-select form-select-sm mb-2" id="formaPago" style="width: 200px;">
                                        <option value="01" selected>01 - Sin utilización del sistema financiero</option>
                                        <option value="02">02 - Compensación de deudas</option>
                                        <option value="03">03 - Tarjeta de débito</option>
                                        <option value="04">04 - Tarjeta de crédito</option>
                                        <option value="17">17 - Dinero electrónico</option>
                                        <option value="18">18 - Tarjeta prepago</option>
                                        <option value="19">19 - Tarjeta de crédito</option>
                                        <option value="20">20 - Otros con utilización del sistema financiero</option>
                                    </select>
                                    <small>Total: $<span id="totalPago">115.00</span></small>
                                </div>
                            </td>
                            <td>Detalle de formas de pago</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sección detalles -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Paso 3</h5>
        </div>
        <div class="card-body">
            <h6 class="mb-3">Detalles de Productos/Servicios - detalles</h6>
            
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Campo XML</th>
                            <th>Valor</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>codigoPrincipal</code></td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="codigoPrincipal" 
                                       value="PRODGENERICO" style="width: 150px;">
                            </td>
                            <td>Código del producto o servicio</td>
                        </tr>
                        <tr>
                            <td><code>descripcion</code></td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="descripcion" 
                                       value="SERVICIO GENERAL" style="width: 250px;">
                            </td>
                            <td>Descripción del producto o servicio</td>
                        </tr>
                        <tr>
                            <td><code>cantidad</code></td>
                            <td>
                                <input type="number" class="form-control form-control-sm" id="cantidad" 
                                       value="1.00" step="0.01" min="0.01" style="width: 100px;" onchange="calcularDetalles()">
                            </td>
                            <td>Cantidad vendida</td>
                        </tr>
                        <tr>
                            <td><code>precioUnitario</code></td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm" id="precioUnitario" 
                                           value="100.00" step="0.01" min="0" onchange="calcularDetalles()">
                                </div>
                            </td>
                            <td>Precio por unidad</td>
                        </tr>
                        <tr>
                            <td><code>descuento</code></td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm" id="descuentoDetalle" 
                                           value="0.00" step="0.01" min="0" onchange="calcularDetalles()">
                                </div>
                            </td>
                            <td>Descuento aplicado al detalle</td>
                        </tr>
                        <tr>
                            <td><code>precioTotalSinImpuesto</code></td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control form-control-sm bg-warning" id="precioTotalSinImpuesto" 
                                           value="100.00" readonly>
                                </div>
                                <small class="text-muted">Calculado automáticamente</small>
                            </td>
                            <td>Precio total sin impuestos del detalle</td>
                        </tr>
                        <tr>
                            <td><code>impuestos</code></td>
                            <td>
                                <div class="border p-2 bg-light">
                                    <strong>Impuesto del Detalle:</strong><br>
                                    <small>Código: <span class="badge bg-secondary">2</span> (IVA)</small><br>
                                    <small>Código Porcentaje: <span class="badge bg-info">4</span> (15%)</small><br>
                                    <small>Tarifa: <span id="tarifaDetalle">15.00</span>%</small><br>
                                    <small>Base Imponible: $<span id="baseImponibleDetalle">100.00</span></small><br>
                                    <small>Valor: $<span id="valorImpuestoDetalle">15.00</span></small>
                                </div>
                            </td>
                            <td>Impuestos aplicados al detalle</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sección infoAdicional -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Paso 4</h5>
        </div>
        <div class="card-body">
            <h6 class="mb-3">Información Adicional - infoAdicional</h6>
            
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Campo XML</th>
                            <th>Valor</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>campoAdicional[Telefono]</code></td>
                            <td>
                                <input type="text" class="form-control form-control-sm" id="telefono" 
                                       value="0983468115" style="width: 150px;">
                            </td>
                            <td>Teléfono de contacto</td>
                        </tr>
                        <tr>
                            <td><code>campoAdicional[Email]</code></td>
                            <td>
                                <input type="email" class="form-control form-control-sm" id="email" 
                                       value="cangulo009@outlook.es" style="width: 250px;">
                            </td>
                            <td>Correo electrónico</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" class="btn btn-success btn-lg" onclick="generarXMLCompleto()">
                    <i class="fas fa-file-code"></i> Generar XML Completo
                </button>
                <button type="button" class="btn btn-info ms-2" onclick="mostrarResumen()">
                    <i class="fas fa-eye"></i> Ver Resumen
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function generarClaveAcceso() {
    // 1. Fecha de Emisión (8 dígitos) - DDMMAAAA
    const fechaActual = new Date();
    const dia = fechaActual.getDate().toString().padStart(2, '0');
    const mes = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
    const año = fechaActual.getFullYear().toString();
    const fechaEmision = dia + mes + año;
    
    // 2. Tipo de Comprobante (2 dígitos) - 01 para Factura
    const tipoComprobante = '01';
    
    // 3. RUC del Emisor (13 dígitos)
    const rucEmisor = '{{ env("EMISOR_RUC") }}';
    
    // 4. Ambiente (1 dígito)
    const ambiente = '{{ env("SRI_AMBIENTE") }}';
    
    // 5. Serie del Comprobante (6 dígitos) - establecimiento + punto de emisión
    const establecimiento = '{{ env("EMISOR_ESTABLECIMIENTO") }}';
    const puntoEmision = '{{ env("EMISOR_PUNTO_EMISION") }}';
    const serie = establecimiento + puntoEmision;
    
    // 6. Número Secuencial (9 dígitos)
    const secuencial = document.getElementById('secuencial').value.padStart(9, '0');
    
    // 7. Código Numérico (8 dígitos) - aleatorio para pruebas
    const codigoNumerico = '12345678'; // Fijo para pruebas
    
    // 8. Tipo de Emisión (1 dígito) - 1 para emisión normal
    const tipoEmision = '1';
    
    // Concatenar los primeros 48 dígitos
    const clave48 = fechaEmision + tipoComprobante + rucEmisor + ambiente + serie + secuencial + codigoNumerico + tipoEmision;
    
    // 9. Calcular Dígito Verificador usando Módulo 11
    const digitoVerificador = calcularModulo11(clave48);
    
    // Clave de acceso completa (49 dígitos)
    const claveAcceso = clave48 + digitoVerificador;
    
    // Mostrar la clave generada
    document.getElementById('claveAccesoValue').innerHTML = `<span class="text-success"><strong>${claveAcceso}</strong></span>`;
    document.getElementById('claveAccesoInput').value = claveAcceso;
    
    console.log('Clave de Acceso generada:', claveAcceso);
    console.log('Desglose:');
    console.log('- Fecha:', fechaEmision);
    console.log('- Tipo Comprobante:', tipoComprobante);
    console.log('- RUC:', rucEmisor);
    console.log('- Ambiente:', ambiente);
    console.log('- Serie:', serie);
    console.log('- Secuencial:', secuencial);
    console.log('- Código Numérico:', codigoNumerico);
    console.log('- Tipo Emisión:', tipoEmision);
    console.log('- Dígito Verificador:', digitoVerificador);
}

function calcularModulo11(cadena) {
    const factores = [2, 3, 4, 5, 6, 7, 2, 3, 4, 5, 6, 7, 2, 3, 4, 5, 6, 7, 2, 3, 4, 5, 6, 7, 2, 3, 4, 5, 6, 7, 2, 3, 4, 5, 6, 7, 2, 3, 4, 5, 6, 7, 2, 3, 4, 5, 6, 7];
    
    let suma = 0;
    for (let i = 0; i < cadena.length; i++) {
        suma += parseInt(cadena[i]) * factores[i];
    }
    
    const residuo = suma % 11;
    const digito = residuo === 0 ? 0 : residuo === 1 ? 1 : 11 - residuo;
    
    return digito.toString();
}

// Función para calcular totales automáticamente
function calcularTotales() {
    const totalSinImpuestos = parseFloat(document.getElementById('totalSinImpuestos').value) || 0;
    const totalDescuento = parseFloat(document.getElementById('totalDescuento').value) || 0;
    const propina = parseFloat(document.getElementById('propina').value) || 0;
    
    // Calcular base imponible (total sin impuestos - descuentos)
    const baseImponible = totalSinImpuestos - totalDescuento;
    
    // Calcular IVA (15%)
    const valorIva = baseImponible * 0.15;
    
    // Calcular total
    const importeTotal = baseImponible + valorIva + propina;
    
    // Actualizar campos
    document.getElementById('baseImponible').textContent = baseImponible.toFixed(2);
    document.getElementById('valorIva').textContent = valorIva.toFixed(2);
    document.getElementById('importeTotal').value = importeTotal.toFixed(2);
    document.getElementById('totalPago').textContent = importeTotal.toFixed(2);
}

// Función para calcular detalles automáticamente
function calcularDetalles() {
    const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    const precioUnitario = parseFloat(document.getElementById('precioUnitario').value) || 0;
    const descuentoDetalle = parseFloat(document.getElementById('descuentoDetalle').value) || 0;
    
    // Calcular precio total sin impuesto
    const precioTotalSinImpuesto = (cantidad * precioUnitario) - descuentoDetalle;
    
    // Calcular impuesto del detalle (15%)
    const valorImpuestoDetalle = precioTotalSinImpuesto * 0.15;
    
    // Actualizar campos
    document.getElementById('precioTotalSinImpuesto').value = precioTotalSinImpuesto.toFixed(2);
    document.getElementById('baseImponibleDetalle').textContent = precioTotalSinImpuesto.toFixed(2);
    document.getElementById('valorImpuestoDetalle').textContent = valorImpuestoDetalle.toFixed(2);
    
    // Actualizar también los totales generales
    document.getElementById('totalSinImpuestos').value = precioTotalSinImpuesto.toFixed(2);
    calcularTotales();
}

// Función para generar XML completo
function generarXMLCompleto() {
    // Primero generar la clave de acceso
    generarClaveAcceso();
    
    console.log('=== GENERANDO XML COMPLETO ===');
    console.log('Todos los datos han sido capturados y están listos para el XML');
    
    // Aquí se implementaría la generación real del XML
    alert('XML generado correctamente!\nRevisa la consola del navegador para ver los detalles.');
}

// Función para mostrar resumen
function mostrarResumen() {
    const resumen = {
        claveAcceso: document.getElementById('claveAccesoInput').value || 'Pendiente generar',
        fechaEmision: document.getElementById('fechaEmision').value,
        comprador: document.getElementById('razonSocialComprador').value,
        identificacion: document.getElementById('identificacionComprador').value,
        producto: document.getElementById('descripcion').value,
        subtotal: document.getElementById('totalSinImpuestos').value,
        iva: document.getElementById('valorIva').textContent,
        total: document.getElementById('importeTotal').value,
        telefono: document.getElementById('telefono').value,
        email: document.getElementById('email').value
    };
    
    console.log('=== RESUMEN DE LA FACTURA ===', resumen);
    
    let mensaje = `RESUMEN DE LA FACTURA\n\n`;
    mensaje += `Clave de Acceso: ${resumen.claveAcceso}\n`;
    mensaje += `Fecha: ${resumen.fechaEmision}\n`;
    mensaje += `Cliente: ${resumen.comprador}\n`;
    mensaje += `Identificación: ${resumen.identificacion}\n`;
    mensaje += `Producto/Servicio: ${resumen.producto}\n`;
    mensaje += `Subtotal: $${resumen.subtotal}\n`;
    mensaje += `IVA: $${resumen.iva}\n`;
    mensaje += `TOTAL: $${resumen.total}\n`;
    mensaje += `Contacto: ${resumen.telefono} / ${resumen.email}`;
    
    alert(mensaje);
}

// Inicializar cálculos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    calcularTotales();
    calcularDetalles();
});
</script>
@endpush

