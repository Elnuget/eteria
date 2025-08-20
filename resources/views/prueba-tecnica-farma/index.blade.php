<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prueba Técnica Farma - Eteria</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container-main {
            width: 100%;
            max-width: 1200px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 90vh;
            min-height: 600px;
        }

        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .header .date {
            font-size: 1rem;
            opacity: 0.8;
        }

        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
            position: relative;
        }

        .chat-messages {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message {
            max-width: 70%;
            animation: fadeIn 0.3s ease;
        }

        .message-content {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .message-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .bot-message .message-avatar {
            background: #007bff;
            color: white;
        }

        .user-message .message-avatar {
            background: #6c757d;
            color: white;
        }

        .message-bubble {
            padding: 15px 20px;
            border-radius: 20px;
            font-size: 16px;
            line-height: 1.5;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .bot-message {
            align-self: flex-start;
        }

        .bot-message .message-bubble {
            background: #007bff;
            color: white;
            border-bottom-left-radius: 8px;
        }

        .user-message {
            align-self: flex-end;
        }

        .user-message .message-content {
            flex-direction: row-reverse;
        }

        .user-message .message-bubble {
            background: #e9ecef;
            color: #333;
            border-bottom-right-radius: 8px;
        }

        .message-time {
            font-size: 12px;
            color: #6c757d;
            margin-top: 8px;
            margin-left: 60px;
        }

        .user-message .message-time {
            margin-left: 0;
            margin-right: 60px;
            text-align: right;
        }

        .chat-input-container {
            padding: 25px 30px;
            background: white;
            border-top: 2px solid #eee;
        }

        .chat-user-info {
            padding: 30px;
            background: white;
            display: none;
        }

        .user-info-form {
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
        }

        .user-info-form h3 {
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #007bff;
        }

        .btn-start-chat {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-start-chat:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8f9fa;
            padding: 12px 20px;
            border-radius: 50px;
            border: 2px solid #eee;
            transition: border-color 0.3s ease;
        }

        .input-wrapper:focus-within {
            border-color: #007bff;
        }

        .chat-input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 8px 0;
            font-size: 16px;
            outline: none;
        }

        .send-button {
            background: #007bff;
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .send-button:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        .send-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scrollbar personalizado */
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Indicador de carga */
        .loading-indicator {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container-main {
                height: 95vh;
                border-radius: 15px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .chat-messages {
                padding: 20px;
            }

            .message {
                max-width: 85%;
            }

            .chat-input-container {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.5rem;
            }

            .header .subtitle {
                font-size: 1rem;
            }

            .message-avatar {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .message-time {
                margin-left: 50px;
            }

            .user-message .message-time {
                margin-right: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="container-main">
        <!-- Header -->
        <div class="header">
            <h1>Prueba Técnica Farmacéutica</h1>
            <div class="subtitle">Evaluación para Carlos Angulo</div>
            <div class="date">{{ date('d \d\e F \d\e Y') }}</div>
        </div>

        <!-- Chat Container -->
        <div class="chat-container">
            <!-- Formulario de información del usuario -->
            <div class="chat-user-info" id="chat-user-info">
                <div class="user-info-form">
                    <h3>Para comenzar, ingresa tus datos:</h3>
                    <div class="form-group">
                        <label for="user-name">Nombre</label>
                        <input type="text" id="user-name" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="user-email">Correo electrónico</label>
                        <input type="email" id="user-email" placeholder="Tu correo electrónico" required>
                    </div>
                    <button id="start-chat" class="btn-start-chat">
                        Comenzar
                    </button>
                </div>
            </div>

            <!-- Mensajes del chat -->
            <div class="chat-messages" id="chat-messages" style="display: none;">
                <!-- Los mensajes se cargarán dinámicamente -->
            </div>

            <!-- Input del chat -->
            <div class="chat-input-container" style="display: none;">
                <div class="input-wrapper">
                    <input type="text" class="chat-input" id="user-input" placeholder="Escribe tu respuesta...">
                    <button class="send-button" id="send-message">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chat-messages');
            const userInput = document.getElementById('user-input');
            const sendButton = document.getElementById('send-message');
            const userInfoForm = document.getElementById('chat-user-info');
            const chatInputContainer = document.querySelector('.chat-input-container');
            const startChatButton = document.getElementById('start-chat');
            const userNameInput = document.getElementById('user-name');
            const userEmailInput = document.getElementById('user-email');

            let chatId = localStorage.getItem('pruebaTecnicaChatId');
            let contactoWebId = localStorage.getItem('pruebaTecnicaContactoWebId');
            let userInfo = JSON.parse(localStorage.getItem('pruebaTecnicaChatUser'));

            // Funciones de LocalStorage
            function saveChatData(id, contactoId, name, email) {
                localStorage.setItem('pruebaTecnicaChatId', id);
                localStorage.setItem('pruebaTecnicaContactoWebId', contactoId);
                localStorage.setItem('pruebaTecnicaChatUser', JSON.stringify({ nombre: name, email: email }));
                chatId = id;
                contactoWebId = contactoId;
                userInfo = { nombre: name, email: email };
            }

            function clearChatData() {
                localStorage.removeItem('pruebaTecnicaChatId');
                localStorage.removeItem('pruebaTecnicaContactoWebId');
                localStorage.removeItem('pruebaTecnicaChatUser');
                chatId = null;
                contactoWebId = null;
                userInfo = null;
            }

            // Actualizar UI del chat
            function updateChatUI(showChat = false) {
                if (showChat && chatId && userInfo && contactoWebId) {
                    userInfoForm.style.display = 'none';
                    chatMessages.style.display = 'flex';
                    chatInputContainer.style.display = 'block';
                } else {
                    userInfoForm.style.display = 'block';
                    chatMessages.style.display = 'none';
                    chatInputContainer.style.display = 'none';
                }
            }

            // Agregar mensaje al chat
            function addMessage(message, isUser = false) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
                
                const messageContent = document.createElement('div');
                messageContent.className = 'message-content';

                const avatar = document.createElement('div');
                avatar.className = 'message-avatar';
                avatar.innerHTML = isUser ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>';

                const bubble = document.createElement('div');
                bubble.className = 'message-bubble';
                bubble.textContent = message;

                messageContent.appendChild(avatar);
                messageContent.appendChild(bubble);

                const timeDiv = document.createElement('div');
                timeDiv.className = 'message-time';
                timeDiv.textContent = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

                messageDiv.appendChild(messageContent);
                messageDiv.appendChild(timeDiv);
                
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Cargar historial del chat
            async function loadChatHistory() {
                if (!chatId) return;
                
                chatMessages.innerHTML = '<div class="loading-indicator"><div class="spinner"></div> Cargando historial...</div>';
                
                try {
                    const response = await fetch(`/api/chat/history?chat_id=${chatId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Error del servidor: ${response.status}`);
                    }

                    const data = await response.json();
                    chatMessages.innerHTML = '';
                    
                    if (data.mensajes && data.mensajes.length > 0) {
                        data.mensajes.forEach(mensaje => {
                            addMessage(mensaje.mensaje, mensaje.tipo === 'usuario');
                        });
                    } else {
                        addMessage('¡Bienvenido a la prueba técnica farmacéutica! �‍💻 Como programador, te haré algunas preguntas para evaluar tus conocimientos técnicos. ¿Estás listo para comenzar?', false);
                    }
                    
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } catch (error) {
                    console.error('Error al cargar historial:', error);
                    chatMessages.innerHTML = '';
                    addMessage('Bienvenido a la prueba técnica farmacéutica. Como programador, demostrarás tus habilidades técnicas. ¿Comenzamos?', false);
                }
            }

            // Enviar mensaje
            async function sendMessage() {
                const message = userInput.value.trim();
                
                if (!message || !chatId || !userInfo || !contactoWebId) {
                    alert('No se pudo enviar el mensaje. Por favor, recarga la página.');
                    return;
                }

                userInput.disabled = true;
                sendButton.disabled = true;

                addMessage(message, true);
                userInput.value = '';

                try {
                    const response = await fetch('/api/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            message: message,
                            chat_id: chatId,
                            contacto_web_id: contactoWebId,
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || 'Error al enviar mensaje al servidor');
                    }

                    const data = await response.json();
                    
                    if (data.response) {
                        addMessage(data.response, false);
                    }

                } catch (error) {
                    console.error('Error al enviar mensaje:', error);
                    addMessage(`Error al enviar: ${error.message}`, false);
                } finally {
                    userInput.disabled = false;
                    sendButton.disabled = false;
                    userInput.focus();
                }
            }

            // Iniciar chat
            if (startChatButton) {
                startChatButton.addEventListener('click', async function() {
                    const userName = userNameInput.value.trim();
                    const userEmail = userEmailInput.value.trim();

                    if (!userName || !userEmail) {
                        alert('Por favor, completa todos los campos');
                        return;
                    }

                    // Validación básica de email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(userEmail)) {
                        alert('Por favor, ingresa un correo electrónico válido');
                        return;
                    }

                    startChatButton.disabled = true;
                    startChatButton.textContent = 'Iniciando...';

                    try {
                        const response = await fetch('/api/chat/find-or-create', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ nombre: userName, celular: userEmail }) // Usamos celular para almacenar el email
                        });

                        if (!response.ok) {
                            const errorData = await response.json().catch(() => ({}));
                            throw new Error(errorData.message || 'Error al obtener ID del chat');
                        }

                        const data = await response.json();
                        if (!data.chat_id || !data.contacto_web_id) {
                            throw new Error('No se recibieron los IDs necesarios del servidor');
                        }
                        
                        saveChatData(data.chat_id, data.contacto_web_id, userName, userEmail);
                        updateChatUI(true);
                        await loadChatHistory();
                        
                    } catch (error) {
                        console.error('Error al iniciar chat:', error);
                        alert(`No se pudo iniciar el chat: ${error.message}. Intenta de nuevo.`);
                        clearChatData();
                        updateChatUI(false);
                    } finally {
                        startChatButton.disabled = false;
                        startChatButton.textContent = 'Comenzar';
                    }
                });
            }

            // Event listeners
            sendButton.addEventListener('click', sendMessage);
            userInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Inicialización
            if (chatId && userInfo && contactoWebId) {
                updateChatUI(true);
                loadChatHistory();
            } else {
                updateChatUI(false);
            }
        });
    </script>
</body>
</html>
