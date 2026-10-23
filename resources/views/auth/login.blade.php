@extends('layout.plantilla')

@section('title', 'Iniciar Sesión - La Casona Chapaca')

@section('content')
    <div class="container py-5 my-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="tarjeta-auth form-casona">
                    <div class="text-center mb-4">
                        <span class="badge-artesanal mb-2">Acceso a la Casona</span>
                        <h1 class="h3 font-display text-white">Iniciar Sesión</h1>
                        <p class="text-secondary small mb-0">Ingresa tus credenciales para gestionar tus órdenes o acceder al panel.</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" autofocus required
                                class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@casonachapaca.test">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" id="password" autocomplete="current-password" required
                                class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" name="recordar" id="recordar" value="1" class="form-check-input bg-dark border-secondary" @checked(old('recordar'))>
                            <label for="recordar" class="form-check-label text-secondary small">Recordar mi sesión</label>
                        </div>

                        <button type="submit" class="btn-dorado w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> Entrar
                        </button>
                    </form>

                    <p class="text-center text-secondary small mt-4 mb-0">
                        ¿No tienes una cuenta aún?
                        <a href="{{ route('registro.create') }}" class="text-warning fw-semibold text-decoration-none">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection