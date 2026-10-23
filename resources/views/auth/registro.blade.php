@extends('layout.plantilla')

@section('title', 'Crear Cuenta - La Casona Chapaca')

@section('content')
    <div class="container py-5 my-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="tarjeta-auth form-casona">
                    <div class="text-center mb-4">
                        <span class="badge-artesanal mb-2">Bienvenido a la Mesa</span>
                        <h1 class="h3 font-display text-white">Crear Cuenta</h1>
                        <p class="text-secondary small mb-0">Regístrate para realizar pedidos en salón y recibir atención preferencial.</p>
                    </div>

                    <form action="{{ route('registro.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                    class="form-control @error('nombre') is-invalid @enderror" placeholder="Tu nombre">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" name="apellido" id="apellido" value="{{ old('apellido') }}" required
                                    class="form-control @error('apellido') is-invalid @enderror" placeholder="Tu apellido">
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="telefono" class="form-label">Teléfono <span class="text-secondary">(opcional)</span></label>
                                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" autocomplete="tel"
                                    class="form-control @error('telefono') is-invalid @enderror" placeholder="+591 7XXXXXXX">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required
                                    class="form-control @error('email') is-invalid @enderror" placeholder="nombre@correo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" name="password" id="password" autocomplete="new-password" required
                                    class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-secondary" style="font-size: 0.75rem;">Mínimo 8 caracteres, letras y números.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Repetir contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required
                                    class="form-control" placeholder="••••••••">
                            </div>
                        </div>

                        <button type="submit" class="btn-dorado w-100 py-2 mt-4">
                            <i class="bi bi-person-check-fill"></i> Completar Registro
                        </button>
                    </form>

                    <p class="text-center text-secondary small mt-4 mb-0">
                        ¿Ya tienes una cuenta registrada?
                        <a href="{{ route('login') }}" class="text-warning fw-semibold text-decoration-none">Inicia sesión</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection