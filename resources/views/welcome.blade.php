<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Eteria - Desarrollo Web Responsivo</title>
    <link rel="icon" type="image/png" href="favicon.png">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" />
    <link rel="stylesheet" href="css/all.min.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/templatemo-style.css" />
<!--
Parallo Template
https://templatemo.com/tm-534-parallo
-->
  </head>
  <body>
    <div class="parallax-window" data-parallax="scroll" data-image-src="img/bg-01.jpg">
      <div class="container-fluid">
        <div class="row tm-brand-row">
          <div class="col-lg-4 col-11">
            <div class="tm-brand-container tm-bg-white-transparent">
              <i class="fas fa-2x fa-code tm-brand-icon"></i>
              <div class="tm-brand-texts">
                <h1 class="text-uppercase tm-brand-name">Eteria</h1>
                <p class="small">desarrollo web responsivo</p>
              </div>
            </div>
          </div>
          <div class="col-lg-8 col-1">
            <div class="tm-nav">
              <nav class="navbar navbar-expand-lg navbar-light tm-bg-white-transparent tm-navbar">
                <button class="navbar-toggler" type="button"
                  data-toggle="collapse" data-target="#navbarNav"
                  aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                  <ul class="navbar-nav">
                    <li class="nav-item active">
                      <div class="tm-nav-link-highlight"></div>
                      <a class="nav-link" href="#">Inicio <span class="sr-only">(actual)</span></a>
                    </li>
                    <li class="nav-item">
                      <div class="tm-nav-link-highlight"></div>
                      <a class="nav-link" href="about.html">Nosotros</a>
                    </li>
                    <li class="nav-item">
                      <div class="tm-nav-link-highlight"></div>
                      <a class="nav-link" href="services.html">Servicios</a>
                    </li>
                    <li class="nav-item">
                      <div class="tm-nav-link-highlight"></div>
                      <a class="nav-link" href="testimonials.html">Testimonios</a>
                    </li>
                    <li class="nav-item">
                      <div class="tm-nav-link-highlight"></div>
                      <a class="nav-link" href="contact.html">Contacto</a>
                    </li>
                    <li class="nav-item">
                      <div class="tm-nav-link-highlight"></div>
                      <a class="nav-link tm-bg-white-transparent" href="{{ route('login') }}" style="margin-left: 15px; padding: 8px 20px; border-radius: 5px; font-weight: bold;">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                      </a>
                    </li>
                  </ul>
                </div>
              </nav>
            </div>
          </div>
        </div>

        <section class="row" id="tmHome">
          <div class="col-12 tm-home-container">
            <div class="text-white tm-home-left">
              <p class="text-uppercase tm-slogan">Podemos desarrollar</p>
              <hr class="tm-home-hr" />
              <h2 class="tm-home-title">Sitios Web Responsivos para tu Negocio</h2>
              <p class="tm-home-text">
                Eteria es una empresa especializada en el desarrollo de sitios web modernos y adaptables. Creamos experiencias digitales únicas para tu negocio.
              </p>
              <a href="#tmFeatures" class="btn btn-primary">Saber Más</a>
            </div>
            <div class="tm-home-right">
              <img src="img/mobile-screen.png" alt="App on Mobile mockup" />
            </div>
          </div>
        </section>

        <!-- Features -->
        <div class="row" id="tmFeatures">
          <div class="col-lg-4">
            <div class="tm-bg-white-transparent tm-feature-box">
            <h3 class="tm-feature-name">Alto Rendimiento</h3>
            
            <div class="tm-feature-icon-container">
                <i class="fas fa-3x fa-server"></i>
            </div>

            <p class="text-center">Desarrollamos sitios web optimizados para un rendimiento excepcional.</p>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="tm-bg-white-transparent tm-feature-box">
                <h3 class="tm-feature-name">Soporte Rápido</h3>

                <div class="tm-feature-icon-container">
                    <i class="fas fa-3x fa-headphones"></i>
                </div>
                <p class="text-center">Brindamos soporte técnico especializado y respuesta inmediata.</p>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="tm-bg-white-transparent tm-feature-box">
                <h3 class="tm-feature-name">Marketing Digital</h3>

                <div class="tm-feature-icon-container">
                    <i class="fas fa-3x fa-satellite-dish"></i>
                </div>
                <p class="text-center">Estrategias efectivas para potenciar tu presencia en línea.</p>
            </div>
          </div>
        </div>
        <!-- Call to Action -->
        <section class="row" id="tmCallToAction">
          <div class="col-12 tm-call-to-action-col">
            <img src="img/call-to-action.jpg" alt="Image" class="img-fluid tm-call-to-action-image" />
            <div class="tm-bg-white tm-call-to-action-text">
              <h2 class="tm-call-to-action-title">¿Listo para empezar?</h2>
              <p class="tm-call-to-action-description">
                Permítenos ayudarte a crear la presencia digital que tu negocio necesita. Diseñamos soluciones web adaptadas a tus necesidades específicas.
              </p>
              <form action="#" method="get" class="tm-call-to-action-form">                
                <input name="email" type="email" class="tm-email-input" id="email" placeholder="Correo electrónico" />
                <button type="submit" class="btn btn-secondary">Recibir Información</button>
              </form>
            </div>
          </div>
        </section>

        <!-- Page footer -->
        <footer class="row">
          <p class="col-12 text-white text-center tm-copyright-text">
            Copyright &copy; 2024 Eteria. 
            Todos los derechos reservados
          </p>
        </footer>
      </div>
      <!-- .container-fluid -->
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/parallax.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <!-- Chat Widget -->
    <div class="chat-widget" id="chat-widget">
        <div class="chat-header">
            <div class="chat-header-content">
                <div class="chat-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chat-title">
                    <h3>Asistente Eteria</h3>
                    <span class="chat-status">En línea</span>
                </div>
            </div>
            <button id="minimize-chat" class="minimize-btn">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        <div class="chat-messages" id="chat-messages">
            <div class="message bot-message">
                <div class="message-content">
                    <div class="message-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="message-bubble">
                        ¡Bienvenido a Eteria! 👋 ¿En qué puedo ayudarte?
                    </div>
                </div>
                <div class="message-time">ahora</div>
            </div>
        </div>
        <div class="chat-input">
            <div class="input-wrapper">
                <input type="text" id="user-input" placeholder="Escribe tu mensaje...">
                <button id="send-message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        .chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 80px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: all 0.3s ease;
            font-family: 'Open Sans', sans-serif;
            overflow: hidden;
        }

        @media (max-width: 480px) {
            .chat-widget {
                width: 100%;
                height: 80px;
                bottom: 0;
                right: 0;
                border-radius: 0;
            }
        }

        .chat-header {
            padding: 15px 20px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 480px) {
            .chat-header {
                border-radius: 0;
            }
        }

        .chat-header-content {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 5px;
            border-radius: 10px;
        }

        .chat-header-content:hover {
            background: rgba(255,255,255,0.1);
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-title {
            display: flex;
            flex-direction: column;
        }

        .chat-title h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .chat-status {
            font-size: 12px;
            opacity: 0.8;
        }

        .minimize-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .minimize-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .chat-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .message {
            margin-bottom: 20px;
            max-width: 85%;
        }

        .message-content {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .message-avatar {
            width: 30px;
            height: 30px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .bot-message .message-avatar {
            background: #007bff;
            color: white;
        }

        .message-bubble {
            background: white;
            padding: 12px 15px;
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            font-size: 14px;
            line-height: 1.4;
        }

        .bot-message {
            margin-right: auto;
        }

        .bot-message .message-bubble {
            background: #007bff;
            color: white;
            border-top-left-radius: 5px;
        }

        .user-message {
            margin-left: auto;
        }

        .user-message .message-content {
            flex-direction: row-reverse;
        }

        .user-message .message-bubble {
            background: #e9ecef;
            border-top-right-radius: 5px;
        }

        .message-time {
            font-size: 11px;
            color: #6c757d;
            margin-top: 5px;
            margin-left: 40px;
        }

        .chat-input {
            padding: 15px 20px;
            background: white;
            border-top: 1px solid #eee;
            border-radius: 0 0 15px 15px;
        }

        @media (max-width: 480px) {
            .chat-input {
                border-radius: 0;
            }
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 25px;
        }

        .chat-input input {
            flex-grow: 1;
            border: none;
            background: transparent;
            padding: 8px 0;
            outline: none;
            font-size: 14px;
        }

        .chat-input button {
            background: #007bff;
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .chat-input button:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message {
            animation: fadeIn 0.3s ease;
        }

        /* Scrollbar personalizado */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Estilos para el indicador de escritura */
        .typing-indicator .message-bubble {
            padding: 15px !important;
            min-width: 60px;
        }

        .typing-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 4px;
            background: #fff;
            border-radius: 50%;
            animation: typing 1s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) { animation-delay: 0.2s; }
        .typing-dot:nth-child(2) { animation-delay: 0.3s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Estilos para estados deshabilitados */
        .chat-input input:disabled,
        .chat-input button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chat-messages');
            const userInput = document.getElementById('user-input');
            const sendButton = document.getElementById('send-message');
            const minimizeButton = document.getElementById('minimize-chat');
            const chatWidget = document.getElementById('chat-widget');
            const chatInput = document.querySelector('.chat-input');
            const chatHeader = document.querySelector('.chat-header-content');

            let isMinimized = true;
            let isTyping = false;

            function getCurrentTime() {
                const now = new Date();
                return now.getHours().toString().padStart(2, '0') + ':' + 
                       now.getMinutes().toString().padStart(2, '0');
            }

            function showTypingIndicator() {
                if (!isTyping) {
                    isTyping = true;
                    const typingDiv = document.createElement('div');
                    typingDiv.className = 'message bot-message typing-indicator';
                    typingDiv.innerHTML = `
                        <div class="message-content">
                            <div class="message-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="message-bubble">
                                <span class="typing-dot"></span>
                                <span class="typing-dot"></span>
                                <span class="typing-dot"></span>
                            </div>
                        </div>
                    `;
                    chatMessages.appendChild(typingDiv);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }

            function removeTypingIndicator() {
                const typingIndicator = document.querySelector('.typing-indicator');
                if (typingIndicator) {
                    typingIndicator.remove();
                }
                isTyping = false;
            }

            function addMessage(message, isUser = false) {
                removeTypingIndicator();
                
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
                timeDiv.textContent = getCurrentTime();

                messageDiv.appendChild(messageContent);
                messageDiv.appendChild(timeDiv);
                
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            async function sendMessage() {
                const message = userInput.value.trim();
                if (!message) return;

                // Deshabilitar el input y botón mientras se envía el mensaje
                userInput.disabled = true;
                sendButton.disabled = true;

                try {
                    // Agregar el mensaje del usuario
                    addMessage(message, true);
                    userInput.value = '';

                    // Mostrar indicador de escritura
                    showTypingIndicator();

                    const response = await fetch('/api/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            message: message
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`Error del servidor: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    // Pequeña pausa para simular tiempo de respuesta natural
                    await new Promise(resolve => setTimeout(resolve, 500));
                    
                    // Agregar la respuesta del bot
                    addMessage(data.response);
                } catch (error) {
                    console.error('Error:', error);
                    addMessage('Lo siento, ha ocurrido un error. Por favor, intenta de nuevo más tarde.');
                } finally {
                    // Habilitar el input y botón nuevamente
                    userInput.disabled = false;
                    sendButton.disabled = false;
                    userInput.focus();
                }
            }

            function toggleChat() {
                isMinimized = !isMinimized;
                
                if (isMinimized) {
                    chatWidget.style.height = '80px';
                    chatMessages.style.display = 'none';
                    chatInput.style.display = 'none';
                    minimizeButton.innerHTML = '<i class="fas fa-plus"></i>';
                } else {
                    chatWidget.style.height = window.innerWidth <= 480 ? '100%' : '500px';
                    chatMessages.style.display = 'block';
                    chatInput.style.display = 'block';
                    minimizeButton.innerHTML = '<i class="fas fa-minus"></i>';
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }

            // Ajuste para dispositivos móviles
            function adjustMobileView() {
                if (window.innerWidth <= 480) {
                    chatWidget.style.height = isMinimized ? '80px' : '100%';
                    chatWidget.style.width = '100%';
                } else {
                    chatWidget.style.height = isMinimized ? '80px' : '500px';
                    chatWidget.style.width = '350px';
                }
            }

            // Inicializar el chat
            function initializeChat() {
                chatWidget.style.height = '80px';
                chatMessages.style.display = 'none';
                chatInput.style.display = 'none';
                minimizeButton.innerHTML = '<i class="fas fa-plus"></i>';
            }

            initializeChat();
            adjustMobileView();

            // Event Listeners
            sendButton.addEventListener('click', sendMessage);
            
            userInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            chatHeader.addEventListener('click', function(e) {
                if (isMinimized) {
                    toggleChat();
                }
            });

            minimizeButton.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleChat();
            });
            
            window.addEventListener('resize', adjustMobileView);
        });
    </script>
  </body>
</html>