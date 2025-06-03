@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pruebas SRI</h1>
    
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
</script>
@endpush
