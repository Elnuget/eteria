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
                        <div class="accordion" id="chatsAccordion">
                            @foreach($chats as $chat_id => $messages)
                                @php
                                    // Obtener el primer mensaje para acceder a la relación contactoWeb
                                    $firstMessage = $messages->first();
                                    $contactoWeb = $firstMessage->contactoWeb; // Acceder al contacto relacionado
                                    $lastMessage = $messages->last(); // Último mensaje para la fecha

                                    // Usar los datos del contacto relacionado
                                    $userName = $contactoWeb ? $contactoWeb->nombre : 'Usuario Desconocido';
                                    $userEmail = $contactoWeb ? $contactoWeb->email : 'Email Desconocido';
                                @endphp
                                <div class="accordion-item mb-3">
                                    <div class="accordion-header">
                                        <div class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#chat{{ str_replace(['@', '.', '-'], '', $chat_id) }}">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div>
                                                    <strong>{{ $userName }}</strong>
                                                    <small class="text-muted ms-2">{{ $userEmail }}</small>
                                                </div>
                                                <div class="ms-auto me-3 d-flex align-items-center">
                                                    <span class="badge bg-primary me-2">{{ $messages->count() }} mensajes</span>
                                                    <span class="text-muted">Último: {{ $lastMessage->created_at->format('d/m/Y H:i') }}</span>
                                                    <form action="{{ route('chat-web.destroy', $chat_id) }}" 
                                                          method="POST" 
                                                          class="d-inline ms-2"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar esta conversación?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="chat{{ str_replace(['@', '.', '-'], '', $chat_id) }}" class="accordion-collapse collapse" data-bs-parent="#chatsAccordion">
                                        <div class="accordion-body">
                                            <div class="chat-messages mb-3">
                                                @foreach($messages->sortByDesc('created_at') as $message)
                                                    <div class="message-container {{ $message->tipo === 'usuario' ? 'user-message' : 'other-message' }}">
                                                        <div class="message-bubble">
                                                            <div class="message-header">
                                                                <strong>
                                                                    @if($message->tipo === 'usuario')
                                                                        {{-- Acceder al nombre desde la relación --}}
                                                                        {{ $message->contactoWeb ? $message->contactoWeb->nombre : 'Usuario' }}
                                                                    @elseif($message->tipo === 'admin')
                                                                        <i class="fas fa-user-shield"></i> Administrador
                                                                    @else
                                                                        <i class="fas fa-robot"></i> Bot
                                                                    @endif
                                                                </strong>
                                                                <small class="text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</small>
                                                            </div>
                                                            <div class="message-content">
                                                                {{ $message->mensaje }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            {{-- Asegurarse que el contactoWeb existe antes de pasar sus datos al JS --}}
                                            @if($contactoWeb)
                                            <form class="chat-form" onsubmit="sendAdminMessage(event, '{{ $chat_id }}')">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Responder a {{ $contactoWeb->nombre }}..." required>
                                                    <button class="btn btn-success" type="submit">
                                                        <i class="fas fa-reply"></i> Responder
                                                    </button>
                                                </div>
                                            </form>
                                            @else
                                            <p class="text-danger">Error: No se pudo identificar al contacto para responder.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.chat-messages {
    max-height: 400px;
    overflow-y: auto;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
    display: flex;
    flex-direction: column-reverse;
}

.message-container {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
}

.message-container:last-child {
    margin-top: 0;
}

.user-message {
    align-items: flex-start;
}

.other-message {
    align-items: flex-end;
}

.user-message .message-bubble {
    background: #e3f2fd;
    border-top-left-radius: 0.25rem;
    color: #0d47a1;
}

.other-message .message-bubble {
    background: #f5f5f5;
    border-top-right-radius: 0.25rem;
    color: #424242;
}

.other-message .message-bubble.admin-message {
    background: #e8f5e9;
    color: #1b5e20;
}

.admin-message {
    /* Remove specific admin alignment */
}

.admin-message .message-bubble {
    /* Remove specific admin styles, handled by .other-message.admin-message */
}

.bot-message .message-bubble {
    /* Remove specific bot styles, handled by .other-message */
}

.message-bubble {
    max-width: 80%;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    background: white;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.message-content {
    word-break: break-word;
}

.chat-form {
    margin-top: 1rem;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #000;
}

.accordion-button:focus {
    box-shadow: none;
}
</style>
@endpush

@push('scripts')
<script>
async function sendAdminMessage(event, chatId) {
    event.preventDefault();
    const form = event.target;
    const input = form.querySelector('input');
    const button = form.querySelector('button');
    const message = input.value.trim();

    if (!message) return;

    // Deshabilitar el formulario mientras se envía
    input.disabled = true;
    button.disabled = true;

    try {
        const response = await fetch('/api/chat/admin-reply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: message,
                chat_id: chatId
            })
        });

        if (!response.ok) {
            throw new Error('Error al enviar el mensaje');
        }

        // Recargar la página para mostrar el nuevo mensaje
        window.location.reload();
    } catch (error) {
        console.error('Error:', error);
        alert('Error al enviar el mensaje');
    } finally {
        input.disabled = false;
        button.disabled = false;
        input.value = '';
    }
}

// Mantener el scroll en la parte superior para cada chat
document.querySelectorAll('.chat-messages').forEach(container => {
    container.scrollTop = 0;
});

// Scroll automático cuando se abre un acordeón
document.querySelectorAll('.accordion-button').forEach(button => {
    button.addEventListener('click', function() {
        const chatMessages = this.closest('.accordion-item').querySelector('.chat-messages');
        if (chatMessages) {
            setTimeout(() => {
                chatMessages.scrollTop = 0;
            }, 300);
        }
    });
});
</script>
@endpush 