<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('tasks.index') }}">
                                    <i class="fas fa-tasks"></i> Tareas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('projects.index') }}">
                                    <i class="fas fa-project-diagram"></i> Proyectos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('balances.index') }}">
                                    <i class="fas fa-balance-scale"></i> Saldos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('payments.index') }}">
                                    <i class="fas fa-money-bill-wave"></i> Pagos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('clientes.index') }}">
                                    <i class="fas fa-users"></i> Clientes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('whatsapp.index') }}">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('contextos.index') }}">
                                    <i class="fas fa-comments"></i> Contextos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('mensajes.index') }}">
                                    <i class="fas fa-envelope"></i> Mensajes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('turnos.index') }}">
                                    <i class="fas fa-calendar-alt"></i> Turnos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('contactos.index') }}">
                                    <i class="fas fa-address-book"></i> Contactos
                                </a>
                            </li>
                            @if(Auth::user()->is_admin)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('users.index') }}">
                                        <i class="fas fa-users"></i> Usuarios
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <button class="nav-link dropdown-toggle btn btn-link" 
                                        type="button"
                                        id="navbarDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->is_admin)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('users.index') }}">
                                                <i class="fas fa-users"></i> Usuarios
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
                                            <i class="fas fa-sign-out-alt"></i> {{ __('Cerrar Sesión') }}
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
</body>
</html>


