
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Trivia gastronómica interactiva Sabores de Bolivia para la Feria UPDS. Pon a prueba tus conocimientos sobre la cocina tradicional boliviana.">
    <title>Sabores de Bolivia • Trivia Gastronómica UPDS</title>

    <!-- Fuentes Google Fonts con respaldo del sistema -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('juego-assets/css/style.css') }}">
    <script src="{{ asset('juego-assets/js/preguntas.js') }}"></script>
    <script type="module" src="{{ asset('juego-assets/js/juego.js') }}"></script></head>
<body>

    <!-- Canvas de Confetti para celebraciones -->
    <canvas id="canvas-confetti" class="canvas-confetti" aria-hidden="true"></canvas>

    <!-- Barra de utilidades superiores: Sonido, Pantalla Completa y Ranking -->
    <div class="barra-utilidades">
        <button id="btn-ranking-top" class="boton-utilidad" title="Ver Ranking de la Feria" aria-label="Ver Ranking de la Feria">
            <span class="icono">🏆</span>
            <span class="texto-utilidad">Ranking</span>
        </button>

        <div class="utilidades-derecha">
            <button id="btn-sonido" class="boton-utilidad" title="Alternar Sonido" aria-label="Alternar Sonido">
                <span id="icono-sonido" class="icono">🔊</span>
            </button>

            <button id="btn-pantalla-completa" class="boton-utilidad" title="Pantalla Completa (Modo Kiosco)" aria-label="Pantalla Completa">
                <span id="icono-pantalla" class="icono">⛶</span>
            </button>
        </div>
    </div>

    <main class="contenedor">

        <!-- ENCABEZADO PRINCIPAL -->
        <header class="encabezado">
            <div class="banner-contenedor">
               <img src="{{ asset('juego-assets/assets/banner.jpg') }}" alt="Banner del Juego">                <div class="banner-overlay"></div>
            </div>
            <p class="etiqueta">FERIA UNIVERSITARIA UPDS • TRIVIA GASTRONÓMICA</p>
            <h1>Sabores de Bolivia</h1>
            <p class="subtitulo">
                Descubre cuánto sabes sobre las delicias, aromas e historia de nuestra gastronomía.
            </p>
        </header>

        <!-- 1. PANTALLA DE INICIO / REGISTRO -->
        <section id="pantalla-inicio" class="tarjeta pantalla-inicio">
            <div class="bienvenida-contenido">
                <div class="insignia-bienvenida">🍽️ ¡Bienvenido al Stand!</div>
                <h2>¿Listo para el desafío del sabor?</h2>
                <p class="instrucciones">
                    Responde 10 preguntas aleatorias de nuestra comida tradicional. Gana puntos base, bonificaciones por responder rápido y usa tus comodines estratégicamente.
                </p>

                <div class="formulario-jugador">
                    <label for="input-nombre" class="etiqueta-input">
                        Ingresa tu nombre o apodo: <span class="asterisco-obligatorio" title="Campo obligatorio">*</span>
                    </label>
                    <div id="contenedor-input-nombre" class="grupo-input">
                        <span class="icono-input" aria-hidden="true">👤</span>
                        <input 
                            type="text" 
                            id="input-nombre" 
                            placeholder="Ej. Andrés (Ing. de Sistemas)" 
                            maxlength="28" 
                            autocomplete="off"
                            required
                            aria-required="true"
                            aria-describedby="mensaje-error-nombre"
                        >
                    </div>
                    <!-- Advertencia visual cuando el nombre no es ingresado -->
                    <div id="mensaje-error-nombre" class="alerta-error-input" role="alert" hidden>
                        <span class="icono-alerta" aria-hidden="true">⚠️</span>
                        <div class="texto-alerta">
                            <strong>¡Nombre obligatorio!</strong>
                            <span>Por favor, ingresa tu nombre o apodo para poder comenzar y registrar tu récord en la feria.</span>
                        </div>
                    </div>
                </div>

                <div class="reglas-resumen">
                    <div class="regla-item">
                        <span class="regla-icono">⏱️</span>
                        <div><strong>15 segundos</strong><span>por pregunta</span></div>
                    </div>
                    <div class="regla-item">
                        <span class="regla-icono">⚡</span>
                        <div><strong>2 Comodines</strong><span>50/50 y +10 segundos</span></div>
                    </div>
                    <div class="regla-item">
                        <span class="regla-icono">🔥</span>
                        <div><strong>Rachas & Bonos</strong><span>multiplica tu puntaje</span></div>
                    </div>
                </div>

                <div class="acciones-inicio">
                    <button id="btn-iniciar" class="boton principal grande">
                        ¡Comenzar Trivia Ahora! 🚀
                    </button>
                    <button id="btn-abrir-ranking-inicio" class="boton secundario">
                        🏆 Ver Mejores Récords
                    </button>
                </div>
            </div>
        </section>

        <!-- 2. PANTALLA DE JUEGO (TRIVIA) -->
        <section id="pantalla-juego" class="tarjeta" hidden>

            <div class="barra-superior">
                <div class="datos-jugador">
                    <span id="jugador-activo" class="nombre-jugador">👤 Participante</span>
                    <span id="indicador-racha" class="insignia-racha" hidden>🔥 Racha x1</span>
                </div>
                <div class="datos-puntaje">
                    <span id="progreso">Pregunta 1 de 10</span>
                    <strong id="puntaje">0 pts</strong>
                </div>
            </div>

            <!-- Barra de progreso del cuestionario -->
            <div class="barra-progreso" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div id="relleno-progreso"></div>
            </div>

            <!-- Zona de Comodines -->
            <div class="zona-comodines">
                <span class="comodines-etiqueta">Comodines:</span>
                <button id="btn-comodin-5050" class="btn-comodin" title="Descarta 2 respuestas incorrectas">
                    <span class="comodin-icono">⚡</span> 50/50
                </button>
                <button id="btn-comodin-tiempo" class="btn-comodin" title="Añade 10 segundos al temporizador">
                    <span class="comodin-icono">⏳</span> +10s Tiempo
                </button>
            </div>

            <!-- Temporizador -->
            <div class="temporizador">
                <span>Tiempo para responder</span>
                <strong id="tiempo">15</strong>
            </div>

            <div class="barra-tiempo">
                <div id="relleno-tiempo"></div>
            </div>

            <!-- Pregunta -->
            <h2 id="pregunta">Cargando pregunta...</h2>

            <!-- Opciones de respuesta -->
            <div id="opciones" class="opciones"></div>

            <!-- Mensaje de Acierto / Error -->
            <p id="mensaje" class="mensaje" aria-live="polite"></p>

            <!-- Recuadro Educativo "Dato Curioso" -->
            <div id="caja-curiosidad" class="caja-curiosidad" hidden>
                <div class="curiosidad-encabezado">
                    <span class="curiosidad-icono">💡</span>
                    <strong>¿SABÍAS QUE?</strong>
                </div>
                <p id="texto-curiosidad" class="texto-curiosidad"></p>
            </div>

            <!-- Botón Siguiente -->
            <button id="btn-siguiente" class="boton principal" hidden>
                Siguiente pregunta →
            </button>
        </section>

        <!-- 3. PANTALLA FINAL / RESULTADOS -->
        <section id="pantalla-final" class="tarjeta resultado" hidden>
            <p class="etiqueta">TRIVIA FINALIZADA</p>
            <div id="contenedor-insignia" class="contenedor-insignia">
                <span id="icono-rango" class="icono-rango">👑</span>
                <h3 id="titulo-rango" class="titulo-rango">Maestro Chef Boliviano</h3>
            </div>

            <h2 id="encabezado-final">¡Excelente participación!</h2>

            <p class="texto-resultado">Puntuación total acumulada:</p>
            <p id="resultado-final" class="puntuacion-final">0 pts</p>

            <!-- Desglose de estadísticas -->
            <div class="desglose-estadisticas">
                <div class="stat-card">
                    <span class="stat-numero" id="stat-aciertos">0 / 10</span>
                    <span class="stat-label">Aciertos</span>
                </div>
                <div class="stat-card">
                    <span class="stat-numero" id="stat-racha">0</span>
                    <span class="stat-label">Mayor Racha</span>
                </div>
                <div class="stat-card">
                    <span class="stat-numero" id="stat-precision">0%</span>
                    <span class="stat-label">Precisión</span>
                </div>
            </div>

            <p id="mensaje-final" class="mensaje-final"></p>
            <div id="notificacion-ranking" class="notificacion-ranking" hidden></div>

            <div class="acciones-finales">
                <button id="btn-reiniciar" class="boton principal grande">
                    🔄 Jugar otra partida
                </button>
                <button id="btn-ver-ranking-final" class="boton secundario">
                    🏆 Ver Tabla de Líderes
                </button>
                <button id="btn-cambiar-jugador" class="boton texto">
                    👤 Cambiar de jugador
                </button>
            </div>
        </section>

        <!-- MODAL: TABLA DE LÍDERES / RANKING DEL STAND -->
        <div id="modal-ranking" class="modal-overlay" hidden>
            <div class="modal-tarjeta">
                <div class="modal-encabezado">
                    <div class="modal-titulo-bloque">
                        <span class="modal-icono">🏆</span>
                        <div>
                            <h3>Tabla de Líderes</h3>
                            <p class="modal-subtitulo">Mejores puntajes de la Feria UPDS</p>
                        </div>
                    </div>
                    <button id="btn-cerrar-ranking" class="btn-cerrar" aria-label="Cerrar ranking">✕</button>
                </div>

                <div class="modal-cuerpo">
                    <div id="lista-ranking" class="lista-ranking">
                        <!-- Las filas del ranking se inyectan dinámicamente -->
                    </div>
                </div>

                <div class="modal-pie">
                    <button id="btn-limpiar-ranking" class="boton peligro discreto" title="Para organizadores del stand">
                        🗑️ Reiniciar Tabla
                    </button>
                    <button id="btn-cerrar-ranking-pie" class="boton principal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>


        <footer>
            <p>Feria Universitaria UPDS • Desarrollado con ❤️ para celebrar la gastronomía boliviana.</p>
        </footer>
    </main>

</body>
</html>
