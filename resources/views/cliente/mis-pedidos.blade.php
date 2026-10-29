@extends('layout.plantilla')

@section('title', 'Mis Pedidos - La Casona Chapaca')

@section('content')
    <div class="container py-5 my-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="badge-artesanal mb-2">Historial de Comensal</span>
                <h1 class="h2 font-display text-white mb-1">Mis Pedidos y Reservas</h1>
                <p class="text-secondary small mb-0">Revisa el estado de preparación de tus órdenes en cocina y el detalle de tu mesa.</p>
            </div>
            <a href="{{ route('cliente.reservar') }}" class="btn-dorado btn-sm">
                <i class="bi bi-plus-circle-fill"></i> Nueva Reserva / Pedido
            </a>
        </div>

        @if ($pedidos->isEmpty())
            <div class="p-5 text-center rounded-4" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde-suave);">
                <i class="bi bi-receipt-cutoff fs-1 text-secondary mb-3 d-block"></i>
                <h4 class="text-white font-display">Aún no tienes pedidos registrados</h4>
                <p class="text-secondary small mb-4">Explora nuestra carta gastronómica y reserva tu mesa cuando desees visitarnos.</p>
                <a href="{{ route('cliente.reservar') }}" class="btn-dorado">Hacer mi Primer Pedido</a>
            </div>
        @else
            <div class="row g-4">
                @foreach ($pedidos as $pedido)
                    @php
                        $colorBadge = match ($pedido->estado) {
                            'pendiente' => 'bg-warning text-dark',
                            'preparando' => 'bg-danger text-white',
                            'listo' => 'bg-info text-dark',
                            'servido' => 'bg-primary text-white',
                            'pagado' => 'bg-success text-white',
                            'cancelado' => 'bg-secondary text-white',
                            default => 'bg-secondary text-white',
                        };
                    @endphp
                    <div class="col-lg-6">
                        <div class="p-4 rounded-4 h-100 d-flex flex-column" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde-suave);">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="text-secondary small text-uppercase">Pedido #{{ $pedido->id }}</div>
                                    <h4 class="text-white font-display mb-0">Mesa #{{ $pedido->mesa?->numero ?? 'S/N' }}</h4>
                                    <small class="text-secondary">{{ $pedido->fecha_hora?->format('d/m/Y H:i') }}</small>
                                </div>
                                <span class="badge {{ $colorBadge }} px-3 py-2 text-uppercase fw-bold rounded-pill" style="font-size: 0.75rem;">
                                    {{ $pedido->estado }}
                                </span>
                            </div>

                            <div class="table-responsive flex-grow-1 my-3">
                                <table class="table table-dark table-sm table-borderless small mb-0">
                                    <thead>
                                        <tr class="text-secondary border-bottom border-secondary">
                                            <th>Plato</th>
                                            <th class="text-center">Cant.</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pedido->detalles as $detalle)
                                            <tr>
                                                <td class="text-light">{{ $detalle->plato?->nombre ?? 'Plato' }}</td>
                                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                                <td class="text-end text-secondary">Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                                <td class="text-end text-warning">Bs. {{ number_format($detalle->subtotal(), 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                                <span class="text-secondary small">Total Consumido:</span>
                                <div class="h4 mb-0 text-warning font-display">Bs. {{ number_format($pedido->total(), 2) }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $pedidos->links() }}
            </div>
        @endif
    </div>
@endsection
