@extends('layout.plantilla')

@section('title', 'Panel de Administración - La Casona Chapaca')

@section('content')
    <div class="container py-5 my-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="badge-artesanal mb-2">Panel Maestro</span>
                <h1 class="h2 font-display text-white mb-1">Administración General</h1>
                <p class="text-secondary small mb-0">Gestión de la carta gastronómica, disponibilidad de platos y control del salón.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('cocina.inicio') }}" class="btn-outline-dorado btn-sm">
                    <i class="bi bi-fire"></i> Ver Monitor de Cocina
                </a>
                <a href="{{ route('inicio') }}" class="btn-dorado btn-sm">
                    <i class="bi bi-house-door-fill"></i> Ver Portal Web
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="tarjeta-stat p-3">
                    <div class="tarjeta-stat-icono bg-success-subtle text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary small">Mesas Libres</div>
                        <div class="h4 mb-0 text-white font-display">{{ $conteoMesas['libres'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="tarjeta-stat p-3">
                    <div class="tarjeta-stat-icono bg-warning-subtle text-warning">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary small">Reservadas</div>
                        <div class="h4 mb-0 text-white font-display">{{ $conteoMesas['reservadas'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="tarjeta-stat p-3">
                    <div class="tarjeta-stat-icono bg-danger-subtle text-danger">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary small">Ocupadas</div>
                        <div class="h4 mb-0 text-white font-display">{{ $conteoMesas['ocupadas'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="tarjeta-stat p-3">
                    <div class="tarjeta-stat-icono bg-info-subtle text-info">
                        <i class="bi bi-cup-hot-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary small">Total Platos</div>
                        <div class="h4 mb-0 text-white font-display">{{ $platos->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-pills nav-tabs-casona mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="platos-tab" data-bs-toggle="pill" data-bs-target="#tab-platos" type="button" role="tab" aria-controls="tab-platos" aria-selected="true">
                    <i class="bi bi-menu-button-wide me-1"></i> Gestión de Platos ({{ $platos->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mesas-tab" data-bs-toggle="pill" data-bs-target="#tab-mesas" type="button" role="tab" aria-controls="tab-mesas" aria-selected="false">
                    <i class="bi bi-grid-3x3-gap me-1"></i> Control de Mesas ({{ $mesas->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            <div class="tab-pane fade show active" id="tab-platos" role="tabpanel" aria-labelledby="platos-tab">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="p-4 rounded-4" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde);">
                            <h4 class="text-white font-display mb-3">Registrar Nuevo Plato</h4>
                            <form action="{{ route('admin.platos.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label text-secondary small">Categoría</label>
                                    <select name="categoria_id" class="form-select bg-dark text-white border-secondary" required>
                                        <option value="">Selecciona categoría...</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-secondary small">Nombre del Plato</label>
                                    <input type="text" name="nombre" class="form-control bg-dark text-white border-secondary" placeholder="Ej: Pique Macho Especial" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-secondary small">Precio (en Bolivianos)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary border-secondary text-white">Bs.</span>
                                        <input type="number" step="0.50" min="1" name="precio" class="form-control bg-dark text-white border-secondary" placeholder="45.00" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-secondary small">Descripción (opcional)</label>
                                    <textarea name="descripcion" rows="3" class="form-control bg-dark text-white border-secondary" placeholder="Ingredientes, preparación o detalles..."></textarea>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="disponible" value="1" id="checkDisponible" checked>
                                    <label class="form-check-label text-secondary small" for="checkDisponible">Marcar disponible inmediatamente</label>
                                </div>

                                <button type="submit" class="btn-dorado w-100 py-2">
                                    <i class="bi bi-plus-circle"></i> Guardar en la Carta
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="p-4 rounded-4" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde-suave);">
                            <h4 class="text-white font-display mb-3">Platos Registrados en la Carta</h4>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover align-middle small mb-0">
                                    <thead>
                                        <tr class="text-secondary border-bottom border-secondary">
                                            <th>Plato</th>
                                            <th>Categoría</th>
                                            <th>Precio</th>
                                            <th>Disponibilidad</th>
                                            <th class="text-end">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($platos as $plato)
                                            <tr>
                                                <td class="text-white fw-semibold">{{ $plato->nombre }}</td>
                                                <td>
                                                    <span class="badge bg-dark border border-secondary text-warning">
                                                        {{ $plato->categoria?->nombre ?? 'General' }}
                                                    </span>
                                                </td>
                                                <td class="text-warning font-display">Bs. {{ number_format($plato->precio, 2) }}</td>
                                                <td>
                                                    <span class="badge {{ $plato->disponible ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1">
                                                        {{ $plato->disponible ? 'Disponible' : 'Agotado' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <form action="{{ route('admin.platos.toggle', $plato) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm {{ $plato->disponible ? 'btn-outline-danger' : 'btn-outline-success' }}" style="font-size: 0.72rem;">
                                                            {{ $plato->disponible ? 'Desactivar' : 'Activar' }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-mesas" role="tabpanel" aria-labelledby="mesas-tab">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="p-4 rounded-4" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde);">
                            <h4 class="text-white font-display mb-3">Añadir Nueva Mesa</h4>
                            <form action="{{ route('admin.mesas.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label text-secondary small">Número de Mesa</label>
                                    <input type="number" name="numero" min="1" class="form-control bg-dark text-white border-secondary" placeholder="Ej: 17" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-secondary small">Capacidad (Personas)</label>
                                    <input type="number" name="capacidad" min="1" max="20" class="form-control bg-dark text-white border-secondary" placeholder="4" required>
                                </div>

                                <button type="submit" class="btn-dorado w-100 py-2">
                                    <i class="bi bi-plus-square"></i> Registrar Mesa
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="p-4 rounded-4" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde-suave);">
                            <h4 class="text-white font-display mb-3">Mesas y Estado Operativo</h4>
                            <div class="row g-3">
                                @foreach ($mesas as $mesa)
                                    @php
                                        $badgeMesa = match ($mesa->estado) {
                                            'libre' => 'bg-success-subtle text-success border-success-subtle',
                                            'reservado' => 'bg-warning-subtle text-warning border-warning-subtle',
                                            'ocupado' => 'bg-danger-subtle text-danger border-danger-subtle',
                                            default => 'bg-secondary text-white',
                                        };
                                    @endphp
                                    <div class="col-md-6 col-xl-4">
                                        <div class="p-3 rounded-3 border border-secondary" style="background: #14141a;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0 text-white font-display">Mesa #{{ $mesa->numero }}</h5>
                                                <span class="badge border {{ $badgeMesa }} text-uppercase" style="font-size: 0.68rem;">
                                                    {{ $mesa->estado }}
                                                </span>
                                            </div>
                                            <div class="text-secondary small mb-3">Capacidad: {{ $mesa->capacidad }} personas</div>

                                            <form action="{{ route('admin.mesas.estado', $mesa) }}" method="POST" class="d-flex gap-2">
                                                @csrf
                                                <select name="estado" class="form-select form-select-sm bg-dark text-white border-secondary">
                                                    <option value="libre" @selected($mesa->estado === 'libre')>Libre</option>
                                                    <option value="reservado" @selected($mesa->estado === 'reservado')>Reservado</option>
                                                    <option value="ocupado" @selected($mesa->estado === 'ocupado')>Ocupado</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-secondary text-white">Cambiar</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection