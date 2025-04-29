@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comment-dots me-2"></i>
                        Conversaciones de Chat Web
                    </h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($chats->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay conversaciones registradas.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Chat</th>
                                        <th>Último Mensaje</th>
                                        <th>Fecha</th>
                                        <th>Mensajes</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chats as $chat_id => $messages)
                                        <tr>
                                            <td>{{ $chat_id }}</td>
                                            <td>
                                                {{ Str::limit($messages->last()->mensaje, 50) }}
                                            </td>
                                            <td>
                                                {{ $messages->last()->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                {{ $messages->count() }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('chat-web.show', $chat_id) }}" 
                                                       class="btn btn-sm btn-info text-white">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('chat-web.destroy', $chat_id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar esta conversación?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table td, .table th {
        vertical-align: middle;
    }
    
    .btn-group .btn {
        margin-right: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endsection 