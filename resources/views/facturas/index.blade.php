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
                                <span class="text-danger">PENDIENTE GENERAR</span><br>
                                <small class="text-muted">Clave de 49 dígitos (se calculará automáticamente)</small>
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
