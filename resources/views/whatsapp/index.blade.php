@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fab fa-whatsapp"></i> Enviar Mensaje de WhatsApp</h4>
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
                            <label class="form-label">Número de Teléfono</label>
                            <input type="text" class="form-control" value="{{ $toNumber }}" readonly>
                            <small class="text-muted">Este número está configurado como destino fijo</small>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fab fa-whatsapp"></i> Enviar Mensaje
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 