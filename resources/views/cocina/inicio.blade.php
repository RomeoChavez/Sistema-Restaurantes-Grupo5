@extends('layout.plantilla')

@section('title', 'Comandas & Cocina - La Casona Chapaca')

@section('content')
    <div class="container-fluid py-4 px-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="badge-artesanal mb-2">Monitor en Vivo</span>
                <h1 class="h2 font-display text-white mb-1">Comandas de Cocina & Salón</h1>
                <p class="text-secondary small mb-0">Gestión de tiempos de preparación, servicio a mesas y cobro de pedidos.</p>
            </div>
            <div class="d-flex gap-2">
                @if (auth()->user()->esAdmin())
                    <a href="{{ route('admin.inicio') }}" class="btn-outline-dorado btn-sm">
                        <i class="bi bi-speedometer2"></i> Administración
                    </a>
                @endif
                <a href="{{ route('inicio') }}" class="btn-dorado btn-sm">
                    <i class="bi bi-house-door-fill"></i> Portal Web
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="p-3 rounded-3 border border-secondary" style="background: var(--color-tarjeta);">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-grid-3x3-gap-fill text-warning"></i>
                            <span class="text-white small fw-bold">Estado del Salón:</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($mesas as $mesa)
                                @php
                                    $colorMesa = match ($mesa->estado) {
                                        'libre' => 'bg-success-subtle text-success border-success-subtle',
                                        'reservado' => 'bg-warning-subtle text-warning border-warning-subtle',
                                        'ocupado' => 'bg-danger-subtle text-danger border-danger-subtle',
                                        default => 'bg-secondary text-white',
                                    };
                                @endphp
                                <span class="badge border {{ $colorMesa }} p-2" title="Capacidad: {{ $mesa->capacidad }} pers.">
                                    M#{{ $mesa->numero }} ({{ substr($mesa->estado, 0, 3) }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 rounded-4 h-100" style="background: var(--color-tarjeta); border: 1.5px solid rgba(229, 169, 59, 0.4);">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark rounded-circle p-2"></span>
                            <h5 class="mb-0 text-white font-display">Pendientes ({{ $pendientes->count() }})</h5>
                        </div>
                        <small class="text-secondary">Nuevas órdenes</small>
                    </div>

                    @forelse ($pendientes as $pedido)
                        <div class="p-3 rounded-3 mb-3 border border-secondary" style="background: #14141a;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-warning fw-bold">Mesa #{{ $pedido->mesa?->numero ?? 'S/N' }}</span>
                                    <div class="text-secondary small">{{ $pedido->cliente?->nombreCompleto() ?? 'Cliente' }}</div>
                                </div>
                                <span class="text-secondary small">{{ $pedido->fecha_hora?->format('H:i') }}</span>
                            </div>

                            <ul class="list-unstyled small mb-3 border-top border-bottom border-secondary py-2">
                                @foreach ($pedido->detalles as $detalle)
                                    <li class="d-flex justify-content-between text-light">
                                        <span>{{ $detalle->plato?->nombre }}</span>
                                        <span class="text-warning fw-bold">x{{ $detalle->cantidad }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <form action="{{ route('cocina.pedidos.estado', $pedido) }}" method="POST">
                                @csrf
                                <input type="hidden" name="estado" value="preparando">
                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-semibold">
                                    <i class="bi bi-fire"></i> Pasar a Cocina
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-secondary small text-center py-4 mb-0">Sin comandas pendientes</p>
                    @endforelse
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 rounded-4 h-100" style="background: var(--color-tarjeta); border: 1.5px solid rgba(220, 53, 69, 0.4);">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger rounded-circle p-2"></span>
                            <h5 class="mb-0 text-white font-display">En Cocina ({{ $preparando->count() }})</h5>
                        </div>
                        <small class="text-secondary">En preparación</small>
                    </div>

                    @forelse ($preparando as $pedido)
                        <div class="p-3 rounded-3 mb-3 border border-secondary" style="background: #14141a;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-danger fw-bold">Mesa #{{ $pedido->mesa?->numero ?? 'S/N' }}</span>
                                    <div class="text-secondary small">{{ $pedido->cliente?->nombreCompleto() ?? 'Cliente' }}</div>
                                </div>
                                <span class="text-secondary small">{{ $pedido->fecha_hora?->format('H:i') }}</span>
                            </div>

                            <ul class="list-unstyled small mb-3 border-top border-bottom border-secondary py-2">
                                @foreach ($pedido->detalles as $detalle)
                                    <li class="d-flex justify-content-between text-light">
                                        <span>{{ $detalle->plato?->nombre }}</span>
                                        <span class="text-danger fw-bold">x{{ $detalle->cantidad }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <form action="{{ route('cocina.pedidos.estado', $pedido) }}" method="POST">
                                @csrf
                                <input type="hidden" name="estado" value="listo">
                                <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold">
                                    <i class="bi bi-check2-circle"></i> Marcar Listo
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-secondary small text-center py-4 mb-0">Fogones libres</p>
                    @endforelse
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 rounded-4 h-100" style="background: var(--color-tarjeta); border: 1.5px solid rgba(13, 202, 240, 0.4);">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info rounded-circle p-2"></span>
                            <h5 class="mb-0 text-white font-display">Listos ({{ $listos->count() }})</h5>
                        </div>
                        <small class="text-secondary">Para servir</small>
                    </div>

                    @forelse ($listos as $pedido)
                        <div class="p-3 rounded-3 mb-3 border border-secondary" style="background: #14141a;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-info fw-bold">Mesa #{{ $pedido->mesa?->numero ?? 'S/N' }}</span>
                                    <div class="text-secondary small">{{ $pedido->cliente?->nombreCompleto() ?? 'Cliente' }}</div>
                                </div>
                                <span class="badge bg-info text-dark">Servir</span>
                            </div>

                            <ul class="list-unstyled small mb-3 border-top border-bottom border-secondary py-2">
                                @foreach ($pedido->detalles as $detalle)
                                    <li class="d-flex justify-content-between text-light">
                                        <span>{{ $detalle->plato?->nombre }}</span>
                                        <span class="text-info fw-bold">x{{ $detalle->cantidad }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <form action="{{ route('cocina.pedidos.estado', $pedido) }}" method="POST">
                                @csrf
                                <input type="hidden" name="estado" value="servido">
                                <button type="submit" class="btn btn-info btn-sm w-100 fw-semibold text-dark">
                                    <i class="bi bi-arrow-right-circle"></i> Servido en Mesa
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-secondary small text-center py-4 mb-0">No hay platos por servir</p>
                    @endforelse
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="p-3 rounded-4 h-100" style="background: var(--color-tarjeta); border: 1.5px solid rgba(25, 135, 84, 0.4);">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle p-2"></span>
                            <h5 class="mb-0 text-white font-display">Servidos ({{ $servidos->count() }})</h5>
                        </div>
                        <small class="text-secondary">En consumo</small>
                    </div>

                    @forelse ($servidos as $pedido)
                        <div class="p-3 rounded-3 mb-3 border border-secondary" style="background: #14141a;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-success fw-bold">Mesa #{{ $pedido->mesa?->numero ?? 'S/N' }}</span>
                                    <div class="text-secondary small">{{ $pedido->cliente?->nombreCompleto() ?? 'Cliente' }}</div>
                                </div>
                                <span class="text-warning font-display small">Bs. {{ number_format($pedido->total(), 2) }}</span>
                            </div>

                            <ul class="list-unstyled small mb-3 border-top border-bottom border-secondary py-2">
                                @foreach ($pedido->detalles as $detalle)
                                    <li class="d-flex justify-content-between text-secondary">
                                        <span>{{ $detalle->plato?->nombre }}</span>
                                        <span>x{{ $detalle->cantidad }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <form action="{{ route('cocina.pedidos.estado', $pedido) }}" method="POST">
                                @csrf
                                <input type="hidden" name="estado" value="pagado">
                                <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold">
                                    <i class="bi bi-cash-stack"></i> Cobrar y Liberar Mesa
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-secondary small text-center py-4 mb-0">Sin mesas por cobrar</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection