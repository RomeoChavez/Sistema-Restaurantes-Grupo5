<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'La Casona Chapaca - Tradición Culinaria')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/restaurante.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-casona sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inicio') }}">
                <i class="bi bi-cup-hot text-warning"></i>
                <span>La Casona <span>Chapaca</span></span>
            </a>
            <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Mostrar menú">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav mx-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}#menu">Nuestra Carta</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}#mesas">Mesas</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}#nosotros">Tradición</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}#contacto">Contacto</a></li>
                </ul>
                <ul class="navbar-nav align-items-lg-center gap-2">
                    @auth
                        @if (auth()->user()->esAdmin())
                            <li class="nav-item"><a class="nav-link text-warning" href="{{ route('admin.inicio') }}"><i class="bi bi-speedometer2 me-1"></i> Administración</a></li>
                        @endif
                        @if (auth()->user()->tieneRol('admin', 'mesero'))
                            <li class="nav-item"><a class="nav-link text-info" href="{{ route('cocina.inicio') }}"><i class="bi bi-fire me-1"></i> Cocina</a></li>
                        @endif
                        @if (auth()->user()->esUsuario())
                            <li class="nav-item"><a class="nav-link" href="{{ route('cliente.pedidos') }}"><i class="bi bi-receipt me-1 text-warning"></i> Mis Pedidos</a></li>
                            <li class="nav-item"><a class="btn-dorado btn-sm" href="{{ route('cliente.reservar') }}"><i class="bi bi-calendar2-check me-1"></i> Reservar Mesa</a></li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1 text-warning"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow">
                                <li><span class="dropdown-item-text text-secondary small">Rol: {{ \App\Models\User::ROLES[auth()->user()->rol] ?? 'Cliente' }}</span></li>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="btn-outline-dorado btn-sm" href="{{ route('login') }}"><i class="bi bi-calendar2-check me-1"></i> Reservar Mesa</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a></li>
                        <li class="nav-item"><a class="btn-dorado btn-sm" href="{{ route('registro.create') }}">Registrarme</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if (session('success'))
        <div class="container mt-3">
            <div class="alert alert-success bg-dark border-success text-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger bg-dark border-danger text-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="container mt-3">
            <div class="alert alert-danger bg-dark border-danger text-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Por favor corrige los siguientes inconvenientes:</strong>
                </div>
                <ul class="mb-0 small ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="footer-casona" id="contacto">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <h5>La Casona <span class="text-warning">Chapaca</span></h5>
                    <p class="text-secondary small pe-lg-4">
                        Gastronomía auténtica boliviana y tarijeña preparada al momento con ingredientes frescos, recetas tradicionales y el calor de nuestra tierra.
                    </p>
                    <div class="d-flex gap-3 text-warning">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span class="text-light small">Calle Sucre esq. Bolívar, Tarija - Bolivia</span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5>Navegación</h5>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ route('inicio') }}">Inicio</a></li>
                        <li><a href="{{ route('inicio') }}#menu">Nuestra Carta</a></li>
                        <li><a href="{{ route('inicio') }}#mesas">Mesas y Salón</a></li>
                        <li><a href="{{ route('inicio') }}#nosotros">Nuestra Historia</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Horarios de Atención</h5>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-1">
                        <li><span class="text-light">Martes a Viernes:</span> 11:30 - 22:30</li>
                        <li><span class="text-light">Sábados y Domingos:</span> 11:00 - 23:00</li>
                        <li><span class="text-light">Lunes:</span> Descanso del personal</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Reservas & Pedidos</h5>
                    <p class="text-secondary small mb-2">Atención directa en salón y reservas:</p>
                    <p class="h6 text-warning mb-3"><i class="bi bi-telephone-fill me-2"></i> +591 75127428</p>
                    @guest
                        <a href="{{ route('registro.create') }}" class="btn-outline-dorado btn-sm w-100 justify-content-center">Crear cuenta de cliente</a>
                    @endguest
                </div>
            </div>
            <div class="border-top border-secondary pt-3 text-center text-secondary small">
                &copy; {{ date('Y') }} La Casona Chapaca. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>