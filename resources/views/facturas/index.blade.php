@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pruebas SRI</h1>
    
    <div class="mb-4">
        <a href="{{ route('facturas.generarxml') }}" class="btn btn-primary">
            <i class="fas fa-file-code"></i> Create XML
        </a>
    </div>
    
    
</div>
@endsection

