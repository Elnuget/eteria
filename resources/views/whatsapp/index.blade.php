@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fab fa-whatsapp"></i> Enviar Saludo de Bienvenida</h4>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('whatsapp.send') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Número de WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text">+593</span>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" 
                                       placeholder="98 316 3609" required
                                       pattern="[0-9 ]{9,}"
                                       title="Ingrese un número válido sin el código de país">
                            </div>
                            <small class="text-muted">Ingrese el número sin el código de país (+593)</small>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fab fa-whatsapp"></i> Enviar Saludo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 