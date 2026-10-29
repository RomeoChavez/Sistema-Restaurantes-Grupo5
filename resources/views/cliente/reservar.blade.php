@extends('layout.plantilla')

@section('title', 'Reservar Mesa y Pedido - La Casona Chapaca')

@section('content')
    <div class="container py-5 my-3">
        <div class="text-center mb-5">
            <span class="badge-artesanal mb-2">Servicio Exclusivo en Salón</span>
            <h1 class="seccion-titulo mb-2">Reserva tu Mesa & Prepara tu Menú</h1>
            <div class="seccion-divisor"></div>
            <p class="text-secondary small mb-0">Selecciona tu mesa preferida y escoge tus platos favoritos para tenerlos listos a tu llegada.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger bg-dark border-danger text-danger mb-4">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($mesas->isEmpty())
            <div class="p-5 text-center rounded-4" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde);">
                <i class="bi bi-clock-history fs-1 text-warning mb-3 d-block"></i>
                <h3 class="text-white font-display">Salón Completo en este Momento</h3>
                <p class="text-secondary small mb-4">Todas nuestras mesas están ocupadas o reservadas. Por favor comunícate a nuestro número directo o intenta nuevamente en unos minutos.</p>
                <a href="{{ route('inicio') }}" class="btn-dorado">Volver al Inicio</a>
            </div>
        @else
            <form action="{{ route('cliente.pedidos.store') }}" method="POST" id="formReserva">
                @csrf

                <div class="p-4 rounded-4 mb-5" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-warning text-dark fw-bold rounded-circle px-2 py-1">1</span>
                        <h4 class="mb-0 text-white font-display">Selecciona tu Mesa</h4>
                    </div>
                    <p class="text-secondary small mb-4">Elige una de las mesas disponibles en el salón:</p>

                    <div class="row g-3">
                        @foreach ($mesas as $mesa)
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="w-100 cursor-pointer">
                                    <input type="radio" name="mesa_id" value="{{ $mesa->id }}" class="d-none mesa-radio" required {{ $loop->first ? 'checked' : '' }}>
                                    <div class="p-3 text-center rounded-3 border mesa-card transition-all">
                                        <i class="bi bi-display fs-3 d-block mb-1 text-warning"></i>
                                        <div class="fw-bold text-white">Mesa #{{ $mesa->numero }}</div>
                                        <small class="text-secondary d-block">Capacidad: {{ $mesa->capacidad }} pers.</small>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle mt-2" style="font-size: 0.68rem;">Libre</span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 rounded-4 mb-5" style="background: var(--color-tarjeta); border: 1px solid var(--color-borde);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-warning text-dark fw-bold rounded-circle px-2 py-1">2</span>
                        <h4 class="mb-0 text-white font-display">Elige tus Platos y Bebidas</h4>
                    </div>
                    <p class="text-secondary small mb-4">Agrega entradas, platos fuertes, bebidas o postres indicando las cantidades deseadas:</p>

                    <div class="accordion accordion-flush" id="acordeonCategorias">
                        @foreach ($categorias as $categoria)
                            <div class="accordion-item bg-transparent border-secondary mb-3">
                                <h2 class="accordion-header" id="heading-{{ $categoria->id }}">
                                    <button class="accordion-button bg-dark text-white rounded-3 shadow-none border border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $categoria->id }}" aria-expanded="true" aria-controls="collapse-{{ $categoria->id }}">
                                        <span class="font-display fs-5 text-warning me-2">{{ $categoria->nombre }}</span>
                                        <small class="text-secondary">({{ $categoria->platos->count() }} opciones)</small>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $categoria->id }}" class="accordion-collapse collapse show" aria-labelledby="heading-{{ $categoria->id }}">
                                    <div class="accordion-body px-0 pt-3">
                                        <div class="row g-3">
                                            @foreach ($categoria->platos as $plato)
                                                <div class="col-md-6">
                                                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center gap-3 border border-secondary" style="background: #15151c;">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold text-white">{{ $plato->nombre }}</div>
                                                            <div class="text-warning font-display small">Bs. {{ number_format($plato->precio, 2) }}</div>
                                                            @if ($plato->descripcion)
                                                                <small class="text-secondary d-block" style="font-size: 0.78rem;">{{ Str::limit($plato->descripcion, 60) }}</small>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="hidden" name="platos[{{ $plato->id }}][id]" value="{{ $plato->id }}">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary text-white btn-qty" data-action="minus" data-id="{{ $plato->id }}">-</button>
                                                            <input type="number" name="platos[{{ $plato->id }}][cantidad]" id="qty-{{ $plato->id }}" value="0" min="0" max="20" class="form-control form-control-sm text-center input-cantidad bg-dark text-white border-secondary" style="width: 55px;" data-precio="{{ $plato->precio }}" readonly>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary text-white btn-qty" data-action="plus" data-id="{{ $plato->id }}">+</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 rounded-4 d-flex flex-wrap justify-content-between align-items-center gap-3 sticky-bottom shadow-lg" style="background: rgba(18, 18, 22, 0.95); backdrop-filter: blur(10px); border: 1.5px solid var(--color-dorado); z-index: 1020;">
                    <div>
                        <span class="text-secondary small text-uppercase fw-semibold" style="letter-spacing: 1px;">Total de tu Orden Estimado:</span>
                        <div class="h3 mb-0 text-warning font-display" id="totalVisual">Bs. 0.00</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('inicio') }}" class="btn-outline-dorado btn-sm">Cancelar</a>
                        <button type="submit" class="btn-dorado" id="btnConfirmar" disabled>
                            <i class="bi bi-check2-circle"></i> Confirmar Reserva & Pedido
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>

    @push('styles')
        <style>
            .cursor-pointer { cursor: pointer; }
            .mesa-radio:checked + .mesa-card {
                border-color: var(--color-dorado) !important;
                background-color: rgba(229, 169, 59, 0.15) !important;
                box-shadow: 0 0 15px rgba(229, 169, 59, 0.3);
            }
            .mesa-card {
                background: #14141a;
                border-color: rgba(255, 255, 255, 0.1);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const totalVisual = document.getElementById('totalVisual');
                const btnConfirmar = document.getElementById('btnConfirmar');
                const inputs = document.querySelectorAll('.input-cantidad');

                function calcularTotal() {
                    let total = 0;
                    let itemsCount = 0;

                    inputs.forEach(input => {
                        const cantidad = parseInt(input.value) || 0;
                        const precio = parseFloat(input.dataset.precio) || 0;
                        if (cantidad > 0) {
                            total += (cantidad * precio);
                            itemsCount += cantidad;
                        }
                    });

                    totalVisual.textContent = 'Bs. ' + total.toFixed(2);
                    btnConfirmar.disabled = itemsCount === 0;
                }

                document.querySelectorAll('.btn-qty').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = this.dataset.id;
                        const input = document.getElementById('qty-' + id);
                        let valor = parseInt(input.value) || 0;

                        if (this.dataset.action === 'plus' && valor < 20) {
                            input.value = valor + 1;
                        } else if (this.dataset.action === 'minus' && valor > 0) {
                            input.value = valor - 1;
                        }

                        calcularTotal();
                    });
                });
            });
        </script>
    @endpush
@endsection
