@extends('layout.plantilla')

@section('title', 'La Casona Chapaca - Tradición Culinaria & Alta Gastronomía')

@section('content')
    <section class="hero-seccion">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge-artesanal">
                        <i class="bi bi-award-fill"></i> Tradición Tarijeña & Boliviana
                    </span>
                    <h1 class="hero-titulo">
                        El auténtico sabor de nuestra tierra, <span class="texto-dorado">hecho arte.</span>
                    </h1>
                    <p class="hero-subtitulo">
                        En La Casona Chapaca fusionamos recetas ancestrales, ingredientes frescos de los valles y la calidez de nuestro servicio para brindarte una vivencia gastronómica inigualable.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="#menu" class="btn-dorado">
                            <i class="bi bi-book-half"></i> Explorar la Carta
                        </a>
                        @auth
                            @if (auth()->user()->esUsuario())
                                <a href="{{ route('cliente.reservar') }}" class="btn-outline-dorado">
                                    <i class="bi bi-calendar-check-fill"></i> Reservar Mesa
                                </a>
                            @elseif (auth()->user()->esAdmin())
                                <a href="{{ route('admin.inicio') }}" class="btn-outline-dorado">
                                    <i class="bi bi-speedometer2"></i> Administración
                                </a>
                            @elseif (auth()->user()->esMesero())
                                <a href="{{ route('cocina.inicio') }}" class="btn-outline-dorado">
                                    <i class="bi bi-fire"></i> Cocina
                                </a>
                            @endif
                        @else
                            <a href="{{ route('cliente.reservar') }}" class="btn-outline-dorado">
                                <i class="bi bi-calendar-check-fill"></i> Reservar Mesa
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-3">
                        <div class="tarjeta-stat">
                            <div class="tarjeta-stat-icono">
                                <i class="bi bi-door-open-fill"></i>
                            </div>
                            <div>
                                <div class="text-secondary small text-uppercase fw-semibold" style="letter-spacing: 1px;">Salón en Tiempo Real</div>
                                <div class="h4 mb-0 text-white font-display">{{ $mesasLibres }} de {{ $totalMesas }} Mesas Libres</div>
                                <small class="text-success"><i class="bi bi-dot"></i> Atención inmediata disponible</small>
                            </div>
                        </div>

                        <div class="tarjeta-stat">
                            <div class="tarjeta-stat-icono">
                                <i class="bi bi-fire"></i>
                            </div>
                            <div>
                                <div class="text-secondary small text-uppercase fw-semibold" style="letter-spacing: 1px;">Cocina en Vivo</div>
                                <div class="h4 mb-0 text-white font-display">100% Preparado al Momento</div>
                                <small class="text-warning">Sin pre-cocinados, directo del fuego</small>
                            </div>
                        </div>

                        <div class="tarjeta-stat">
                            <div class="tarjeta-stat-icono">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <div class="text-secondary small text-uppercase fw-semibold" style="letter-spacing: 1px;">Ubicación Privilegiada</div>
                                <div class="h5 mb-0 text-white font-display">Tarija, Bolivia</div>
                                <small class="text-secondary">Corazón del valle chapaco</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="menu" style="background-color: var(--color-fondo-alt);">
        <div class="container py-4">
            <div class="seccion-encabezado">
                <div class="seccion-subtitulo">Nuestra Selección Culinaria</div>
                <h2 class="seccion-titulo">Platos Típicos & Especialidades</h2>
                <div class="seccion-divisor"></div>
                <p class="text-secondary mb-0">
                    Cada preparación representa la riqueza cultural de Tarija y Bolivia, elaborada con técnicas tradicionales e insumos de la más alta calidad.
                </p>
            </div>

            <ul class="nav nav-pills nav-tabs-casona justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-todos-btn" data-bs-toggle="pill" data-bs-target="#tab-todos" type="button" role="tab" aria-controls="tab-todos" aria-selected="true">
                        Todos los Platos
                    </button>
                </li>
                @foreach ($categorias as $categoria)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-cat-{{ $categoria->id }}-btn" data-bs-toggle="pill" data-bs-target="#tab-cat-{{ $categoria->id }}" type="button" role="tab" aria-controls="tab-cat-{{ $categoria->id }}" aria-selected="false">
                            {{ $categoria->nombre }} ({{ $categoria->platos->count() }})
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="tab-todos" role="tabpanel" aria-labelledby="tab-todos-btn">
                    <div class="row g-4">
                        @foreach ($categorias as $categoria)
                            @foreach ($categoria->platos as $plato)
                                <div class="col-lg-4 col-md-6">
                                    <div class="tarjeta-plato">
                                        <div class="tarjeta-plato-img-wrapper">
                                            <img src="{{ $plato->imagen_url }}" alt="{{ $plato->nombre }}" class="tarjeta-plato-img" loading="lazy">
                                            <span class="tarjeta-plato-categoria">{{ $categoria->nombre }}</span>
                                        </div>
                                        <div class="tarjeta-plato-cuerpo">
                                            <h3 class="tarjeta-plato-titulo">{{ $plato->nombre }}</h3>
                                            <p class="tarjeta-plato-desc">
                                                {{ $plato->descripcion ?: 'Deliciosa receta artesanal elaborada con ingredientes seleccionados de nuestra huerta y productores locales.' }}
                                            </p>
                                            <div class="tarjeta-plato-pie">
                                                <div class="tarjeta-plato-precio">
                                                    <small>Bs.</small>{{ number_format($plato->precio, 2) }}
                                                </div>
                                                <span class="badge {{ $plato->disponible ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2">
                                                    {{ $plato->disponible ? 'Disponible' : 'Agotado' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                @foreach ($categorias as $categoria)
                    <div class="tab-pane fade" id="tab-cat-{{ $categoria->id }}" role="tabpanel" aria-labelledby="tab-cat-{{ $categoria->id }}-btn">
                        <div class="row g-4">
                            @foreach ($categoria->platos as $plato)
                                <div class="col-lg-4 col-md-6">
                                    <div class="tarjeta-plato">
                                        <div class="tarjeta-plato-img-wrapper">
                                            <img src="{{ $plato->imagen_url }}" alt="{{ $plato->nombre }}" class="tarjeta-plato-img" loading="lazy">
                                            <span class="tarjeta-plato-categoria">{{ $categoria->nombre }}</span>
                                        </div>
                                        <div class="tarjeta-plato-cuerpo">
                                            <h3 class="tarjeta-plato-titulo">{{ $plato->nombre }}</h3>
                                            <p class="tarjeta-plato-desc">
                                                {{ $plato->descripcion ?: 'Especialidad tradicional preparada siguiendo recetas autóctonas y cuidando cada detalle.' }}
                                            </p>
                                            <div class="tarjeta-plato-pie">
                                                <div class="tarjeta-plato-precio">
                                                    <small>Bs.</small>{{ number_format($plato->precio, 2) }}
                                                </div>
                                                <span class="badge {{ $plato->disponible ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2">
                                                    {{ $plato->disponible ? 'Disponible' : 'Agotado' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5" id="mesas">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge-artesanal">
                        <i class="bi bi-clock-history"></i> Salón y Comodidad
                    </span>
                    <h2 class="seccion-titulo text-start">Estado del Salón y Mesas</h2>
                    <div class="seccion-divisor ms-0"></div>
                    <p class="text-secondary leading-relaxed mb-4">
                        Disponemos de un amplio salón colonial ambientado con la calidez del sur boliviano. Nuestro sistema actualiza en tiempo real la disponibilidad de mesas para garantizar que tu visita sea cómoda y sin demoras.
                    </p>
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle p-2"></span>
                            <span class="text-light small">Mesas Libres: <strong>{{ $mesasLibres }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger rounded-circle p-2"></span>
                            <span class="text-light small">Mesas Ocupadas: <strong>{{ max(0, $totalMesas - $mesasLibres) }}</strong></span>
                        </div>
                    </div>
                    @guest
                        <div class="p-4 rounded-4" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde);">
                            <h5 class="text-white mb-2 font-display">¿Deseas realizar pedidos con mayor rapidez?</h5>
                            <p class="text-secondary small mb-3">Crea una cuenta en segundos y accede al menú interactivo para tus órdenes en salón.</p>
                            <a href="{{ route('registro.create') }}" class="btn-dorado btn-sm">Registrarme Ahora</a>
                        </div>
                    @endguest
                </div>
                <div class="col-lg-6">
                    <div class="p-4 rounded-4" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde-suave);">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-white font-display">Distribución del Salón</h4>
                            <span class="badge bg-dark border border-secondary text-warning">Capacidad Total: {{ $totalMesas }} Mesas</span>
                        </div>
                        <div class="row g-3">
                            @for ($i = 1; $i <= min(12, $totalMesas); $i++)
                                @php
                                    $esLibre = $i <= $mesasLibres;
                                @endphp
                                <div class="col-4 col-sm-3">
                                    <div class="p-3 text-center rounded-3 border {{ $esLibre ? 'border-success-subtle bg-dark text-success' : 'border-secondary bg-dark opacity-50 text-secondary' }}">
                                        <i class="bi bi-display fs-4 d-block mb-1"></i>
                                        <div class="small fw-bold">Mesa {{ $i }}</div>
                                        <small class="d-block" style="font-size: 0.7rem;">{{ $esLibre ? 'Libre' : 'Ocupada' }}</small>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <small class="text-secondary d-block mt-3 text-center">Actualizado en vivo mediante el sistema de comandas</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="nosotros" style="background-color: var(--color-fondo-alt);">
        <div class="container py-4">
            <div class="seccion-encabezado">
                <div class="seccion-subtitulo">Identidad y Raíces</div>
                <h2 class="seccion-titulo">Nuestra Tradición Culinaria</h2>
                <div class="seccion-divisor"></div>
                <p class="text-secondary mb-0">
                    Más que un restaurante, somos un homenaje a la historia, a los sabores y a las familias de Tarija.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="tarjeta-stat flex-column align-items-start p-4 h-100">
                        <div class="tarjeta-stat-icono mb-3">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        <h4 class="text-white font-display">Recetas de Antaño</h4>
                        <p class="text-secondary small mb-0">
                            Preparamos desde la emblemática sopa de maní y silpancho hasta los picantes tradicionales, respetando los tiempos de cocción y secretos transmitidos por generaciones.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tarjeta-stat flex-column align-items-start p-4 h-100">
                        <div class="tarjeta-stat-icono mb-3">
                            <i class="bi bi-flower1"></i>
                        </div>
                        <h4 class="text-white font-display">Insumos del Valle</h4>
                        <p class="text-secondary small mb-0">
                            Trabajamos de la mano con agricultores y productores tarijeños, asegurando hortalizas frescas, carnes de primera calidad y especias auténticas de nuestra campiña.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tarjeta-stat flex-column align-items-start p-4 h-100">
                        <div class="tarjeta-stat-icono mb-3">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <h4 class="text-white font-display">Calidez Chapaca</h4>
                        <p class="text-secondary small mb-0">
                            Nos apasiona hacerte sentir como en casa. Cada comensal es recibido con la hospitalidad, la alegría y el trato familiar que caracterizan a nuestra gente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection