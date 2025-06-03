<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Vite Assets -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-code"></i>
                    <span>{{ config('app.name', 'Laravel') }}</span>
                </a>
                
                <button class="navbar-toggler" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" 
                        aria-expanded="false" 
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if(Auth::user()->is_admin)
                                <!-- Gestión de Proyectos - Solo Admin -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" 
                                       href="#" 
                                       id="proyectosDropdown" 
                                       role="button" 
                                       data-bs-toggle="dropdown" 
                                       aria-expanded="false">
                                        <i class="fas fa-project-diagram"></i>
                                        <span class="menu-title">Proyectos</span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="proyectosDropdown">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('projects.index') }}">
                                                <i class="fas fa-project-diagram"></i> Proyectos
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('tasks.index') }}">
                                                <i class="fas fa-tasks"></i> Tareas
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Finanzas - Solo Admin -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" 
                                       href="#" 
                                       id="finanzasDropdown" 
                                       role="button" 
                                       data-bs-toggle="dropdown" 
                                       aria-expanded="false">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span class="menu-title">Finanzas</span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="finanzasDropdown">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('balances.index') }}">
                                                <i class="fas fa-balance-scale"></i> Saldos
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('payments.index') }}">
                                                <i class="fas fa-money-bill-wave"></i> Pagos
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('contabilidad.index') }}">
                                                <i class="fas fa-calculator"></i> Contabilidad
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('facturas.index') }}">
                                                <i class="fas fa-receipt"></i> Facturas
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Clientes - Solo Admin -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" 
                                       href="#" 
                                       id="clientesDropdown" 
                                       role="button" 
                                       data-bs-toggle="dropdown" 
                                       aria-expanded="false">
                                        <i class="fas fa-users"></i>
                                        <span class="menu-title">Clientes</span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="clientesDropdown">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('clientes.index') }}">
                                                <i class="fas fa-users"></i> Clientes
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('contactos.index') }}">
                                                <i class="fas fa-address-book"></i> Contactos WhatsApp
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('contacto-webs.index') }}">
                                                <i class="fas fa-id-card"></i> Contactos Web
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Comunicación - Solo Admin -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" 
                                       href="#" 
                                       id="comunicacionDropdown" 
                                       role="button" 
                                       data-bs-toggle="dropdown" 
                                       aria-expanded="false">
                                        <i class="fas fa-comments"></i>
                                        <span class="menu-title">Comunicación</span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="comunicacionDropdown">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('whatsapp.index') }}">
                                                <i class="fab fa-whatsapp"></i> WhatsApp
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('mensajes.index') }}">
                                                <i class="fas fa-inbox"></i> Mensajes WhatsApp
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('chat-web.index') }}">
                                                <i class="fas fa-comment-dots"></i> Chat Web
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('contextos.index') }}">
                                                <i class="fas fa-robot"></i> Contextos IA
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Agenda - Solo Admin -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" 
                                       href="#" 
                                       id="agendaDropdown" 
                                       role="button" 
                                       data-bs-toggle="dropdown" 
                                       aria-expanded="false">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span class="menu-title">Agenda</span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="agendaDropdown">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('turnos.index') }}">
                                                <i class="fas fa-calendar-alt"></i> Turnos
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Administración - Solo Admin -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" 
                                       href="#" 
                                       id="adminDropdown" 
                                       role="button" 
                                       data-bs-toggle="dropdown" 
                                       aria-expanded="false">
                                        <i class="fas fa-users-cog"></i>
                                        <span class="menu-title">Administración</span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.index') }}">
                                                <i class="fas fa-users-cog"></i> Usuarios
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <!-- Contabilidad - Solo para usuarios NO admin -->
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('contabilidad.index') }}">
                                        <i class="fas fa-calculator"></i>
                                        <span class="menu-title">Contabilidad</span>
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" 
                                   href="#" 
                                   id="navbarDropdown" 
                                   role="button" 
                                   data-bs-toggle="dropdown" 
                                   aria-expanded="false">
                                    <i class="fas fa-user-circle"></i>
                                    {{ Auth::user()->name }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->is_admin)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.index') }}">
                                                <i class="fas fa-users-cog"></i> Usuarios
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('login') }}">
                                            <i class="fas fa-user-switch"></i> Cambiar Usuario
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>


