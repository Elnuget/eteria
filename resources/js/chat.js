let chatId = null;
let userInfo = {
    nombre: '',
    email: ''
};

function getUserChat() {
    if (!userInfo.email) {
        console.error('Email no proporcionado');
        return;
    }

    fetch('/api/chat/user?email=' + encodeURIComponent(userInfo.email))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                chatId = data.chat_id;
                console.log('Chat ID obtenido:', chatId);
            } else {
                console.error('Error al obtener chat:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al obtener chat:', error);
        });
}

function setUserInfo(nombre, email) {
    userInfo.nombre = nombre;
    userInfo.email = email;
    getUserChat();
}

function sendMessage() {
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();

    if (!message || !chatId || !userInfo.nombre || !userInfo.email) {
        console.error('Faltan datos requeridos');
        return;
    }

    const data = {
        message: message,
        chat_id: chatId,
        nombre: userInfo.nombre,
        email: userInfo.email
    };

    fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            appendMessage(message, 'user');
        } else {
            console.error('Error al enviar mensaje:', data.message);
        }
    })
    .catch(error => {
        console.error('Error al enviar mensaje:', error);
    });
}

function appendMessage(message, type) {
    const chatMessages = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}-message`;
    messageDiv.textContent = message;
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
}); 