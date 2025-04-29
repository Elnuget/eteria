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

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Popper.js -->
    <script src="https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --hover-color: #2980b9;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--light-bg);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.5rem 1rem;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            color: var(--accent-color);
        }

        .nav-link {
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: var(--border-radius);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .dropdown-menu {
            display: block;
            position: absolute;
            background: var(--primary-color);
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: var(--border-radius);
            padding: 0.5rem;
            margin-top: 0.5rem;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
            transform: translateY(-10px);
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            color: white !important;
            padding: 0.8rem 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .dropdown-item:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            color: var(--accent-color);
        }

        .menu-section {
            position: relative;
        }

        .menu-dropdown {
            background-color: transparent;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
        }

        .menu-dropdown:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .menu-dropdown i {
            color: var(--accent-color);
            width: 20px;
            text-align: center;
        }

        .menu-dropdown .menu-title {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .user-dropdown {
            background-color: rgba(255,255,255,0.1);
            border-radius: var(--border-radius);
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            transition: var(--transition);
        }

        .user-dropdown:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .user-dropdown i {
            color: var(--accent-color);
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: var(--primary-color);
                padding: 1rem;
                border-radius: var(--border-radius);
                margin-top: 1rem;
            }

            .menu-section {
                margin: 0.5rem 0;
            }

            .dropdown-menu {
                position: static;
                float: none;
                width: auto;
                margin-top: 0;
                background-color: rgba(0,0,0,0.2);
                border: 0;
                box-shadow: none;
                transform: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-code"></i>
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            <!-- Gestión de Proyectos -->
                            <li class="nav-item dropdown menu-section">
                                <button class="menu-dropdown dropdown-toggle" 
                                        type="button" 
                                        id="proyectosDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="fas fa-project-diagram"></i>
                                    <span class="menu-title">Proyectos</span>
                                </button>
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

                            <!-- Finanzas -->
                            <li class="nav-item dropdown menu-section">
                                <button class="menu-dropdown dropdown-toggle" 
                                        type="button" 
                                        id="finanzasDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span class="menu-title">Finanzas</span>
                                </button>
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
                                </ul>
                            </li>

                            <!-- Clientes -->
                            <li class="nav-item dropdown menu-section">
                                <button class="menu-dropdown dropdown-toggle" 
                                        type="button" 
                                        id="clientesDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="fas fa-users"></i>
                                    <span class="menu-title">Clientes</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="clientesDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('clientes.index') }}">
                                            <i class="fas fa-users"></i> Clientes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('contactos.index') }}">
                                            <i class="fas fa-address-book"></i> Contactos
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Comunicación -->
                            <li class="nav-item dropdown menu-section">
                                <button class="menu-dropdown dropdown-toggle" 
                                        type="button" 
                                        id="comunicacionDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="fas fa-comments"></i>
                                    <span class="menu-title">Comunicación</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="comunicacionDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('whatsapp.index') }}">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('mensajes.index') }}">
                                            <i class="fas fa-inbox"></i> Mensajes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('chat-web.index') }}">
                                            <i class="fas fa-comment-dots"></i> Chat Web
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('contacto-webs.index') }}">
                                            <i class="fas fa-id-card"></i> Contactos Web
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('contextos.index') }}">
                                            <i class="fas fa-robot"></i> Contextos IA
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Agenda -->
                            <li class="nav-item dropdown menu-section">
                                <button class="menu-dropdown dropdown-toggle" 
                                        type="button" 
                                        id="agendaDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span class="menu-title">Agenda</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="agendaDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('turnos.index') }}">
                                            <i class="fas fa-calendar-alt"></i> Turnos
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            @if(Auth::user()->is_admin)
                                <!-- Administración -->
                                <li class="nav-item dropdown menu-section">
                                    <button class="menu-dropdown dropdown-toggle" 
                                            type="button" 
                                            id="adminDropdown" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <i class="fas fa-users-cog"></i>
                                        <span class="menu-title">Administración</span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.index') }}">
                                                <i class="fas fa-users-cog"></i> Usuarios
                                            </a>
                                        </li>
                                    </ul>
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
                                <button class="user-dropdown dropdown-toggle" 
                                        type="button"
                                        id="navbarDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="fas fa-user-circle"></i>
                                    {{ Auth::user()->name }}
                                </button>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
    $(document).ready(function() {
        let closeTimeout;
        const CLOSE_DELAY = 300; // 300ms antes de cerrar

        // Función para cerrar todos los dropdowns
        function closeAllDropdowns() {
            $('.dropdown-menu').removeClass('show');
            $('.menu-dropdown').attr('aria-expanded', 'false');
        }

        // Función para cerrar un dropdown específico con delay
        function closeDropdownWithDelay($dropdownMenu, $button) {
            clearTimeout(closeTimeout);
            closeTimeout = setTimeout(() => {
                if (!$dropdownMenu.is(':hover') && !$button.is(':hover')) {
                    $dropdownMenu.removeClass('show');
                    $button.attr('aria-expanded', 'false');
                }
            }, CLOSE_DELAY);
        }

        // Manejar clic en los botones del menú
        $('.menu-dropdown').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $this = $(this);
            const $dropdownMenu = $this.next('.dropdown-menu');
            const isExpanded = $this.attr('aria-expanded') === 'true';
            
            // Cerrar otros dropdowns
            $('.dropdown-menu').not($dropdownMenu).removeClass('show');
            $('.menu-dropdown').not($this).attr('aria-expanded', 'false');
            
            // Toggle del dropdown actual
            $dropdownMenu.toggleClass('show');
            $this.attr('aria-expanded', !isExpanded);
        });

        // Manejar hover en dispositivos no táctiles
        if (window.matchMedia('(hover: hover)').matches) {
            $('.menu-section').hover(
                function() {
                    clearTimeout(closeTimeout);
                    const $dropdownMenu = $(this).find('.dropdown-menu');
                    const $button = $(this).find('.menu-dropdown');
                    
                    // Cerrar otros dropdowns
                    $('.dropdown-menu').not($dropdownMenu).removeClass('show');
                    $('.menu-dropdown').not($button).attr('aria-expanded', 'false');
                    
                    // Abrir el actual
                    $dropdownMenu.addClass('show');
                    $button.attr('aria-expanded', 'true');
                },
                function() {
                    const $dropdownMenu = $(this).find('.dropdown-menu');
                    const $button = $(this).find('.menu-dropdown');
                    closeDropdownWithDelay($dropdownMenu, $button);
                }
            );

            // Mantener abierto mientras el cursor esté sobre el menú
            $('.dropdown-menu').hover(
                function() {
                    clearTimeout(closeTimeout);
                },
                function() {
                    const $dropdownMenu = $(this);
                    const $button = $dropdownMenu.prev('.menu-dropdown');
                    closeDropdownWithDelay($dropdownMenu, $button);
                }
            );
        }

        // Cerrar dropdowns al hacer clic fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                closeAllDropdowns();
            }
        });

        // Cerrar dropdowns al presionar la tecla Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllDropdowns();
            }
        });

        // Prevenir que los clicks en los items del menú propaguen
        $('.dropdown-item').on('click', function(e) {
            e.stopPropagation();
        });
    });
    </script>
</body>
</html>


