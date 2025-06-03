@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pruebas SRI</h1>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>Paso 1</h5>
        </div>
        <div class="card-body">
            <h6 class="mb-3">Configuración SRI</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>SRI_AMBIENTE:</strong> {{ env('SRI_AMBIENTE') }}
                </div>
                <div class="col-md-6">
                    <strong>SRI_RECEPCION_URL:</strong><br>
                    <small class="text-muted">{{ env('SRI_RECEPCION_URL') }}</small>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <strong>SRI_AUTORIZACION_URL:</strong><br>
                    <small class="text-muted">{{ env('SRI_AUTORIZACION_URL') }}</small>
                </div>
            </div>

            <h6 class="mb-3">Datos del Emisor</h6>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>EMISOR_RUC:</strong> {{ env('EMISOR_RUC') }}
                </div>
                <div class="col-md-6">
                    <strong>EMISOR_RAZON_SOCIAL:</strong> {{ env('EMISOR_RAZON_SOCIAL') }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <strong>EMISOR_DIRECCION:</strong> {{ env('EMISOR_DIRECCION') }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>EMISOR_TELEFONO:</strong> {{ env('EMISOR_TELEFONO') }}
                </div>
                <div class="col-md-6">
                    <strong>EMISOR_EMAIL:</strong> {{ env('EMISOR_EMAIL') }}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>EMISOR_OBLIGADO_CONTABILIDAD:</strong> {{ env('EMISOR_OBLIGADO_CONTABILIDAD') }}
                </div>
                <div class="col-md-6">
                    <strong>EMISOR_CONTRIBUYENTE_RIMPE:</strong><br>
                    <small class="text-muted">{{ env('EMISOR_CONTRIBUYENTE_RIMPE') }}</small>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <strong>EMISOR_ESTABLECIMIENTO:</strong> {{ env('EMISOR_ESTABLECIMIENTO') }}
                </div>
                <div class="col-md-6">
                    <strong>EMISOR_PUNTO_EMISION:</strong> {{ env('EMISOR_PUNTO_EMISION') }}
                </div>
            </div>

            <button type="button" class="btn btn-primary">Generar XML</button>
        </div>
    </div>
</div>
@endsection
