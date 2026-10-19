<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi sitio Web')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    <header class="bg-dark text-white shadow">
        <div class="container py-4">
            <h1 class="text-center mb-0">
                @yield('header', 'Mi Cabecera')
            </h1>
        </div>
    </header>
    <main class="container my-5 flex-grow-1">
        @yield('content')
    </main>

    <footer class="bg-dark text-white mt-auto">
        <div class="container py-4">
            <div class="text-center">
                <p class="mb-1">
                    &copy; {{ date('Y') }} Mi sitio Web
                </p>
                <small class="text-secondary">
                    Todos los derechos reservados
                </small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>
</html>