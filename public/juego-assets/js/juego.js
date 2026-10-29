// ==========================================================================
// SABORES DE BOLIVIA - TRIVIA GASTRONÓMICA UPDS
// Lógica Principal del Juego, Gamificación, Sonidos y Leaderboard
// ==========================================================================

import { bancoPreguntas } from "./preguntas.js";

// --------------------------------------------------------------------------
// CONSTANTES Y CONFIGURACIÓN
// --------------------------------------------------------------------------
const DURACION_PREGUNTA = 15;
const CANTIDAD_PREGUNTAS_PARTIDA = 10;
const PUNTOS_BASE_ACIERTO = 100;
const BONO_POR_SEGUNDO = 10;

const CLAVE_RANKING = "saboresBolivia_ranking_upds";
const CLAVE_SONIDO = "saboresBolivia_sonido_upds";

// --------------------------------------------------------------------------
// ELEMENTOS DEL DOM
// --------------------------------------------------------------------------
// Pantallas
const pantallaInicio = document.getElementById("pantalla-inicio");
const pantallaJuego = document.getElementById("pantalla-juego");
const pantallaFinal = document.getElementById("pantalla-final");
const modalRanking = document.getElementById("modal-ranking");

// Barra superior de utilidades
const botonSonido = document.getElementById("btn-sonido");
const iconoSonido = document.getElementById("icono-sonido");
const botonPantalla = document.getElementById("btn-pantalla-completa");
const iconoPantalla = document.getElementById("icono-pantalla");
const botonRankingTop = document.getElementById("btn-ranking-top");

// Elementos de la pantalla de inicio
const inputNombre = document.getElementById("input-nombre");
const contenedorInputNombre = document.getElementById("contenedor-input-nombre");
const mensajeErrorNombre = document.getElementById("mensaje-error-nombre");
const botonIniciar = document.getElementById("btn-iniciar");
const botonAbrirRankingInicio = document.getElementById("btn-abrir-ranking-inicio");

// Elementos de la pantalla de juego
const elementoJugadorActivo = document.getElementById("jugador-activo");
const indicadorRacha = document.getElementById("indicador-racha");
const elementoProgreso = document.getElementById("progreso");
const elementoPuntaje = document.getElementById("puntaje");
const rellenoProgreso = document.getElementById("relleno-progreso");
const elementoTiempo = document.getElementById("tiempo");
const rellenoTiempo = document.getElementById("relleno-tiempo");

const botonComodin5050 = document.getElementById("btn-comodin-5050");
const botonComodinTiempo = document.getElementById("btn-comodin-tiempo");

const elementoPregunta = document.getElementById("pregunta");
const elementoOpciones = document.getElementById("opciones");
const elementoMensaje = document.getElementById("mensaje");

const cajaCuriosidad = document.getElementById("caja-curiosidad");
const textoCuriosidad = document.getElementById("texto-curiosidad");
const botonSiguiente = document.getElementById("btn-siguiente");

// Elementos de la pantalla final
const iconoRango = document.getElementById("icono-rango");
const tituloRango = document.getElementById("titulo-rango");
const encabezadoFinal = document.getElementById("encabezado-final");
const elementoResultadoFinal = document.getElementById("resultado-final");
const statAciertos = document.getElementById("stat-aciertos");
const statRacha = document.getElementById("stat-racha");
const statPrecision = document.getElementById("stat-precision");
const elementoMensajeFinal = document.getElementById("mensaje-final");
const notificacionRanking = document.getElementById("notificacion-ranking");

const botonReiniciar = document.getElementById("btn-reiniciar");
const botonVerRankingFinal = document.getElementById("btn-ver-ranking-final");
const botonCambiarJugador = document.getElementById("btn-cambiar-jugador");

// Elementos del modal ranking
const listaRanking = document.getElementById("lista-ranking");
const botonCerrarRanking = document.getElementById("btn-cerrar-ranking");
const botonCerrarRankingPie = document.getElementById("btn-cerrar-ranking-pie");
const botonLimpiarRanking = document.getElementById("btn-limpiar-ranking");

// Canvas de Confetti
const canvasConfetti = document.getElementById("canvas-confetti");

// --------------------------------------------------------------------------
// ESTADO DEL JUEGO
// --------------------------------------------------------------------------
let preguntasPartida = [];
let indicePregunta = 0;
let puntajeTotal = 0;
let aciertosTotales = 0;
let rachaActual = 0;
let rachaMaxima = 0;
let nombreJugador = "Gourmet Anónimo";
let tiempoRestante = DURACION_PREGUNTA;
let temporizador = null;
let respuestaBloqueada = false;

// Estado de comodines por partida
let comodin5050Usado = false;
let comodinTiempoUsado = false;

// Audio
let sonidoHabilitado = localStorage.getItem(CLAVE_SONIDO) !== "false";
let contextoAudio = null;

// --------------------------------------------------------------------------
// SISTEMA DE AUDIO (Web Audio API sintético - 100% Offline)
// --------------------------------------------------------------------------
function inicializarContextoAudio() {
    if (!contextoAudio) {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
            contextoAudio = new AudioCtx();
        }
    }
    if (contextoAudio && contextoAudio.state === "suspended") {
        contextoAudio.resume();
    }
}

function reproducirTono(frecuencia, duracion = 0.15, tipo = "sine", volumenMax = 0.12) {
    if (!sonidoHabilitado) return;
    try {
        inicializarContextoAudio();
        if (!contextoAudio) return;

        const osc = contextoAudio.createOscillator();
        const ganancia = contextoAudio.createGain();

        osc.type = tipo;
        osc.frequency.value = frecuencia;

        const ahora = contextoAudio.currentTime;
        ganancia.gain.setValueAtTime(volumenMax, ahora);
        ganancia.gain.exponentialRampToValueAtTime(0.0001, ahora + duracion);

        osc.connect(ganancia);
        ganancia.connect(contextoAudio.destination);

        osc.start(ahora);
        osc.stop(ahora + duracion);
    } catch (e) {
        console.warn("Audio error:", e);
    }
}

function reproducirSonidoClick() {
    reproducirTono(440, 0.08, "triangle", 0.08);
}

function reproducirSonidoAcierto() {
    if (!sonidoHabilitado) return;
    try {
        inicializarContextoAudio();
        // Acorde alegre de dos notas
        setTimeout(() => reproducirTono(587.33, 0.12, "sine", 0.12), 0);
        setTimeout(() => reproducirTono(880, 0.28, "sine", 0.15), 110);
    } catch (e) {}
}

function reproducirSonidoError() {
    if (!sonidoHabilitado) return;
    try {
        inicializarContextoAudio();
        setTimeout(() => reproducirTono(240, 0.15, "sawtooth", 0.1), 0);
        setTimeout(() => reproducirTono(190, 0.25, "sawtooth", 0.12), 120);
    } catch (e) {}
}

function reproducirSonidoComodin() {
    if (!sonidoHabilitado) return;
    try {
        inicializarContextoAudio();
        [523.25, 659.25, 783.99, 1046.5].forEach((freq, idx) => {
            setTimeout(() => reproducirTono(freq, 0.14, "sine", 0.09), idx * 70);
        });
    } catch (e) {}
}

function reproducirSonidoFanfarria() {
    if (!sonidoHabilitado) return;
    try {
        inicializarContextoAudio();
        const notas = [523.25, 659.25, 783.99, 1046.5, 783.99, 1046.5];
        const tiempos = [0, 140, 280, 420, 580, 740];
        notas.forEach((nota, i) => {
            setTimeout(() => reproducirTono(nota, 0.35, "triangle", 0.16), tiempos[i]);
        });
    } catch (e) {}
}

function alternarSonido() {
    sonidoHabilitado = !sonidoHabilitado;
    localStorage.setItem(CLAVE_SONIDO, String(sonidoHabilitado));
    actualizarBotonSonido();
    if (sonidoHabilitado) {
        reproducirSonidoClick();
    }
}

function actualizarBotonSonido() {
    if (sonidoHabilitado) {
        iconoSonido.textContent = "🔊";
        botonSonido.title = "Sonido activado (clic para silenciar)";
    } else {
        iconoSonido.textContent = "🔇";
        botonSonido.title = "Sonido silenciado (clic para activar)";
    }
}

// --------------------------------------------------------------------------
// MODO PANTALLA COMPLETA (KIOSCO PARA STAND DE FERIA)
// --------------------------------------------------------------------------
function alternarPantallaCompleta() {
    reproducirSonidoClick();
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch((err) => {
            console.warn("No se pudo iniciar pantalla completa:", err);
        });
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

document.addEventListener("fullscreenchange", () => {
    if (document.fullscreenElement) {
        iconoPantalla.textContent = "🗗";
        botonPantalla.title = "Salir de pantalla completa";
    } else {
        iconoPantalla.textContent = "⛶";
        botonPantalla.title = "Pantalla Completa (Modo Kiosco)";
    }
});

// --------------------------------------------------------------------------
// ALGORITMO FISHER-YATES (Barajado aleatorio limpio)
// --------------------------------------------------------------------------
function barajarArray(arrayOriginal) {
    const copia = [...arrayOriginal];
    for (let i = copia.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [copia[i], copia[j]] = [copia[j], copia[i]];
    }
    return copia;
}

// Prepara 10 preguntas con sus opciones barajadas
function prepararPartida() {
    // 1. Barajar el banco completo de preguntas
    const bancoMezclado = barajarArray(bancoPreguntas);

    // 2. Tomar las primeras N preguntas
    const seleccionadas = bancoMezclado.slice(0, Math.min(CANTIDAD_PREGUNTAS_PARTIDA, bancoMezclado.length));

    // 3. Para cada pregunta, barajar también sus 4 opciones y rastrear la respuesta correcta
    preguntasPartida = seleccionadas.map((item) => {
        const textoCorrecta = item.opciones[item.correcta];
        const opcionesMezcladas = barajarArray(item.opciones);
        const nuevoIndiceCorrecta = opcionesMezcladas.indexOf(textoCorrecta);

        return {
            pregunta: item.pregunta,
            opciones: opcionesMezcladas,
            correcta: nuevoIndiceCorrecta,
            curiosidad: item.curiosidad
        };
    });
}

// --------------------------------------------------------------------------
// VALIDACIÓN Y ADVERTENCIAS DE ENTRADA
// --------------------------------------------------------------------------
function mostrarAdvertenciaNombre(mensaje) {
    if (mensajeErrorNombre) {
        if (mensaje) {
            const spanTexto = mensajeErrorNombre.querySelector(".texto-alerta span");
            if (spanTexto) {
                spanTexto.textContent = mensaje;
            }
        }
        mensajeErrorNombre.hidden = false;
    }

    if (contenedorInputNombre) {
        contenedorInputNombre.classList.remove("con-error");
        // Forzar reflujo del navegador para reiniciar la animación de sacudida
        void contenedorInputNombre.offsetWidth;
        contenedorInputNombre.classList.add("con-error");
    }

    inputNombre.setAttribute("aria-invalid", "true");
}

function ocultarAdvertenciaNombre() {
    if (mensajeErrorNombre) {
        mensajeErrorNombre.hidden = true;
    }
    if (contenedorInputNombre) {
        contenedorInputNombre.classList.remove("con-error");
    }
    inputNombre.removeAttribute("aria-invalid");
}

// --------------------------------------------------------------------------
// CONTROL DE FLUJO DEL JUEGO
// --------------------------------------------------------------------------
function iniciarPartida() {
    inicializarContextoAudio();

    // 1. Validar obligatoriedad del nombre
    const nombreIngresado = inputNombre.value.trim();
    if (!nombreIngresado) {
        mostrarAdvertenciaNombre("Por favor, ingresa tu nombre o apodo para poder comenzar y registrar tu récord en la feria.");
        reproducirSonidoError();
        inputNombre.focus();
        return;
    }

    // Nombre válido: remover advertencia
    ocultarAdvertenciaNombre();
    reproducirSonidoClick();

    // Guardar nombre del jugador
    nombreJugador = nombreIngresado;

    // Reiniciar variables
    indicePregunta = 0;
    puntajeTotal = 0;
    aciertosTotales = 0;
    rachaActual = 0;
    rachaMaxima = 0;
    comodin5050Usado = false;
    comodinTiempoUsado = false;

    // Actualizar comodines en la UI
    botonComodin5050.disabled = false;
    botonComodin5050.classList.remove("usado");
    botonComodinTiempo.disabled = false;
    botonComodinTiempo.classList.remove("usado");

    elementoJugadorActivo.textContent = `👤 ${nombreJugador}`;
    actualizarIndicadorRacha();

    // Preparar preguntas aleatorias
    prepararPartida();

    // Cambiar pantalla
    pantallaInicio.hidden = true;
    pantallaFinal.hidden = true;
    pantallaJuego.hidden = false;

    mostrarPregunta();
}

function mostrarPregunta() {
    detenerTemporizador();

    respuestaBloqueada = false;
    tiempoRestante = DURACION_PREGUNTA;

    const preguntaActual = preguntasPartida[indicePregunta];

    // Actualizar encabezados
    elementoProgreso.textContent = `Pregunta ${indicePregunta + 1} de ${preguntasPartida.length}`;
    elementoPuntaje.textContent = `${puntajeTotal} pts`;
    elementoPregunta.textContent = preguntaActual.pregunta;

    // Resetear mensajes y curiosidad
    elementoMensaje.textContent = "";
    elementoMensaje.className = "mensaje";
    cajaCuriosidad.hidden = true;
    textoCuriosidad.textContent = "";
    botonSiguiente.hidden = true;

    // Barra de progreso
    const porcentajeProgreso = ((indicePregunta + 1) / preguntasPartida.length) * 100;
    rellenoProgreso.style.width = `${porcentajeProgreso}%`;

    // Renderizar opciones de respuesta con letras A, B, C, D
    const letras = ["A", "B", "C", "D"];
    elementoOpciones.innerHTML = "";

    preguntaActual.opciones.forEach((opcionTexto, i) => {
        const boton = document.createElement("button");
        boton.type = "button";
        boton.className = "opcion";
        boton.id = `opcion-${i}`;

        const letraSpan = document.createElement("span");
        letraSpan.className = "opcion-letra";
        letraSpan.textContent = letras[i];

        const textoSpan = document.createElement("span");
        textoSpan.className = "opcion-texto";
        textoSpan.textContent = opcionTexto;

        boton.appendChild(letraSpan);
        boton.appendChild(textoSpan);

        boton.addEventListener("click", () => {
            verificarRespuesta(i);
        });

        elementoOpciones.appendChild(boton);
    });

    actualizarTemporizadorUI();
    iniciarTemporizador();
}

function actualizarTemporizadorUI() {
    elementoTiempo.textContent = tiempoRestante;
    const porcentaje = (tiempoRestante / DURACION_PREGUNTA) * 100;
    rellenoTiempo.style.width = `${Math.min(100, Math.max(0, porcentaje))}%`;

    if (tiempoRestante <= 5) {
        rellenoTiempo.classList.add("urgente");
        elementoTiempo.style.color = "var(--color-error)";
    } else {
        rellenoTiempo.classList.remove("urgente");
        elementoTiempo.style.color = "var(--color-primario)";
    }
}

function iniciarTemporizador() {
    temporizador = setInterval(() => {
        tiempoRestante--;
        actualizarTemporizadorUI();

        if (tiempoRestante <= 5 && tiempoRestante > 0) {
            reproducirTono(520, 0.06, "sine", 0.08);
        }

        if (tiempoRestante <= 0) {
            detenerTemporizador();
            verificarRespuesta(-1); // -1 indica tiempo agotado
        }
    }, 1000);
}

function detenerTemporizador() {
    if (temporizador !== null) {
        clearInterval(temporizador);
        temporizador = null;
    }
}

// --------------------------------------------------------------------------
// VERIFICACIÓN DE RESPUESTAS & GAMIFICACIÓN
// --------------------------------------------------------------------------
function verificarRespuesta(indiceElegido) {
    if (respuestaBloqueada) return;

    respuestaBloqueada = true;
    detenerTemporizador();

    const preguntaActual = preguntasPartida[indicePregunta];
    const botones = elementoOpciones.querySelectorAll(".opcion");

    // Deshabilitar todas las opciones
    botones.forEach((btn) => {
        btn.disabled = true;
    });

    // Marcar visualmente la correcta
    botones[preguntaActual.correcta].classList.add("correcta");

    const esCorrecta = indiceElegido === preguntaActual.correcta;

    if (esCorrecta) {
        aciertosTotales++;
        rachaActual++;
        rachaMaxima = Math.max(rachaMaxima, rachaActual);

        // Calcular bono por racha y velocidad
        let multiplicador = 1;
        if (rachaActual >= 5) multiplicador = 2.0;
        else if (rachaActual >= 3) multiplicador = 1.5;
        else if (rachaActual >= 2) multiplicador = 1.2;

        const bonoVelocidad = Math.max(0, tiempoRestante) * BONO_POR_SEGUNDO;
        const puntosGanados = Math.round((PUNTOS_BASE_ACIERTO + bonoVelocidad) * multiplicador);
        puntajeTotal += puntosGanados;

        let extraMsg = bonoVelocidad > 0 ? ` (+${bonoVelocidad} bono por velocidad)` : "";
        if (multiplicador > 1) extraMsg += ` 🔥 Combo x${multiplicador}`;

        elementoMensaje.textContent = `¡Correcto! Ganaste +${puntosGanados} pts.${extraMsg}`;
        elementoMensaje.className = "mensaje acierto";

        reproducirSonidoAcierto();
    } else {
        rachaActual = 0; // Se rompe la racha

        if (indiceElegido === -1) {
            elementoMensaje.textContent = `¡Tiempo agotado! La respuesta correcta era: ${preguntaActual.opciones[preguntaActual.correcta]}.`;
        } else {
            botones[indiceElegido].classList.add("incorrecta");
            elementoMensaje.textContent = `Incorrecto. La respuesta correcta era: ${preguntaActual.opciones[preguntaActual.correcta]}.`;
        }
        elementoMensaje.className = "mensaje error";

        reproducirSonidoError();
    }

    actualizarIndicadorRacha();
    elementoPuntaje.textContent = `${puntajeTotal} pts`;

    // Mostrar recuadro educativo "Sabías que..."
    if (preguntaActual.curiosidad) {
        textoCuriosidad.textContent = preguntaActual.curiosidad;
        cajaCuriosidad.hidden = false;
    }

    // Botón siguiente
    botonSiguiente.textContent =
        indicePregunta === preguntasPartida.length - 1
            ? "Ver Resultados Finales 🏆"
            : "Siguiente Pregunta →";
    botonSiguiente.hidden = false;
}

function actualizarIndicadorRacha() {
    if (rachaActual >= 2) {
        let textoCombo = `🔥 Racha x${rachaActual >= 5 ? "2.0" : rachaActual >= 3 ? "1.5" : "1.2"}`;
        indicadorRacha.textContent = textoCombo;
        indicadorRacha.hidden = false;
    } else {
        indicadorRacha.hidden = true;
    }
}

function siguientePregunta() {
    reproducirSonidoClick();
    indicePregunta++;

    if (indicePregunta < preguntasPartida.length) {
        mostrarPregunta();
    } else {
        mostrarResultados();
    }
}

// --------------------------------------------------------------------------
// COMODINES (POWER-UPS)
// --------------------------------------------------------------------------
function usarComodin5050() {
    if (comodin5050Usado || respuestaBloqueada) return;

    comodin5050Usado = true;
    botonComodin5050.disabled = true;
    botonComodin5050.classList.add("usado");

    reproducirSonidoComodin();

    const preguntaActual = preguntasPartida[indicePregunta];
    const indicesIncorrectos = [0, 1, 2, 3].filter((idx) => idx !== preguntaActual.correcta);

    // Barajar los 3 incorrectos y elegir 2 para descartar
    const aDescartar = barajarArray(indicesIncorrectos).slice(0, 2);

    aDescartar.forEach((idx) => {
        const btn = document.getElementById(`opcion-${idx}`);
        if (btn) {
            btn.classList.add("descartada-5050");
            btn.disabled = true;
        }
    });
}

function usarComodinTiempo() {
    if (comodinTiempoUsado || respuestaBloqueada) return;

    comodinTiempoUsado = true;
    botonComodinTiempo.disabled = true;
    botonComodinTiempo.classList.add("usado");

    reproducirSonidoComodin();

    // Añadir 10 segundos extra
    tiempoRestante = Math.min(25, tiempoRestante + 10);
    actualizarTemporizadorUI();

    // Destello de feedback
    rellenoTiempo.style.backgroundColor = "#22c55e";
    setTimeout(() => {
        actualizarTemporizadorUI();
    }, 400);
}

// --------------------------------------------------------------------------
// RESULTADOS & EVALUACIÓN GASTRONÓMICA
// --------------------------------------------------------------------------
function calcularRango(aciertos, total) {
    if (aciertos === 10) {
        return {
            icono: "👑",
            titulo: "Maestro Chef Boliviano",
            mensaje: "¡Puntuación perfecta! Tu sabiduría sobre las tradiciones y sabores bolivianos es digna de admiración."
        };
    } else if (aciertos >= 8) {
        return {
            icono: "🍲",
            titulo: "Gourmet de Tradición",
            mensaje: "¡Extraordinario! Saboreas y conoces las raíces culinarias de nuestra tierra con gran maestría."
        };
    } else if (aciertos >= 6) {
        return {
            icono: "🥟",
            titulo: "Aficionado del Buen Comer",
            mensaje: "¡Muy bien jugado! Tienes un excelente paladar y gran aprecio por nuestra gastronomía."
        };
    } else if (aciertos >= 4) {
        return {
            icono: "🌽",
            titulo: "Comensal Curioso",
            mensaje: "¡Buen intento! Te espera un maravilloso banquete de nuevos sabores típicos por descubrir."
        };
    } else {
        return {
            icono: "🎒",
            titulo: "Turista en su Propia Tierra",
            mensaje: "¡A ponerse al día! Te recomendamos hacer un delicioso tour gastronómico por los mercados tradicionales."
        };
    }
}

function mostrarResultados() {
    detenerTemporizador();

    pantallaJuego.hidden = true;
    pantallaFinal.hidden = false;

    // Rango culinario
    const rango = calcularRango(aciertosTotales, preguntasPartida.length);
    iconoRango.textContent = rango.icono;
    tituloRango.textContent = rango.titulo;
    elementoMensajeFinal.textContent = rango.mensaje;

    // Estadísticas
    elementoResultadoFinal.textContent = `${puntajeTotal} pts`;
    statAciertos.textContent = `${aciertosTotales} / ${preguntasPartida.length}`;
    statRacha.textContent = `${rachaMaxima}`;
    const precision = Math.round((aciertosTotales / preguntasPartida.length) * 100);
    statPrecision.textContent = `${precision}%`;

    // Guardar en la tabla de líderes
    const posicionRanking = registrarPuntajeRanking({
        nombre: nombreJugador,
        puntaje: puntajeTotal,
        aciertos: aciertosTotales,
        racha: rachaMaxima,
        fecha: new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })
    });

    if (posicionRanking !== -1 && posicionRanking <= 5) {
        notificacionRanking.textContent = `🎉 ¡Orgullo UPDS! Ingresaste en el puesto #${posicionRanking} de la Tabla de Líderes.`;
        notificacionRanking.hidden = false;
        dispararConfetti(5000);
        reproducirSonidoFanfarria();
    } else if (aciertosTotales >= 7) {
        dispararConfetti(3500);
        reproducirSonidoFanfarria();
        notificacionRanking.hidden = true;
    } else {
        notificacionRanking.hidden = true;
    }
}

// --------------------------------------------------------------------------
// TABLA DE LÍDERES (LOCALSTORAGE - TOP 10 FERIA UPDS)
// --------------------------------------------------------------------------
function obtenerRanking() {
    try {
        const datos = localStorage.getItem(CLAVE_RANKING);
        if (!datos) return [];
        const parseado = JSON.parse(datos);
        return Array.isArray(parseado) ? parseado : [];
    } catch (e) {
        return [];
    }
}

function registrarPuntajeRanking(registro) {
    try {
        const ranking = obtenerRanking();
        ranking.push(registro);

        // Ordenar desc por puntaje, luego por aciertos
        ranking.sort((a, b) => b.puntaje - a.puntaje || b.aciertos - a.aciertos);

        // Mantener solo Top 10
        const top10 = ranking.slice(0, 10);
        localStorage.setItem(CLAVE_RANKING, JSON.stringify(top10));

        // Encontrar posición del jugador
        const pos = top10.findIndex(
            (item) => item.nombre === registro.nombre && item.puntaje === registro.puntaje
        );

        return pos !== -1 ? pos + 1 : -1;
    } catch (e) {
        console.warn("Error guardando ranking:", e);
        return -1;
    }
}

function renderizarRankingModal() {
    const ranking = obtenerRanking();
    listaRanking.innerHTML = "";

    if (ranking.length === 0) {
        listaRanking.innerHTML = `
            <div class="ranking-vacio">
                <p>Aún no hay puntuaciones registradas hoy.</p>
                <p>¡Sé el primer participante en inaugurar el podio!</p>
            </div>
        `;
        return;
    }

    const medallas = ["🥇", "🥈", "🥉"];

    ranking.forEach((item, index) => {
        const fila = document.createElement("div");
        fila.className = `item-ranking ${index < 3 ? `top-${index + 1}` : ""}`;

        const medalla = index < 3 ? medallas[index] : `#${index + 1}`;

        fila.innerHTML = `
            <span class="ranking-posicion">${medalla}</span>
            <div class="ranking-info">
                <span class="ranking-nombre">${escapeHTML(item.nombre)}</span>
                <span class="ranking-detalles">${item.aciertos}/10 aciertos • Racha: ${item.racha || 0} • ${item.fecha || ""}</span>
            </div>
            <span class="ranking-puntos">${item.puntaje} pts</span>
        `;

        listaRanking.appendChild(fila);
    });
}

function escapeHTML(str) {
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
}

function abrirModalRanking() {
    reproducirSonidoClick();
    renderizarRankingModal();
    modalRanking.hidden = false;
}

function cerrarModalRanking() {
    reproducirSonidoClick();
    modalRanking.hidden = true;
}

function reiniciarTablaRanking() {
    if (confirm("¿Estás seguro de reiniciar la tabla de récords del stand? Esta acción no se puede deshacer.")) {
        localStorage.removeItem(CLAVE_RANKING);
        renderizarRankingModal();
        reproducirSonidoClick();
    }
}

// --------------------------------------------------------------------------
// MOTOR DE CONFETTI ULTRALIVIANO (Puro Canvas HTML5 - Sin Dependencias)
// --------------------------------------------------------------------------
let confettiActivo = false;
let particulasConfetti = [];
let animacionConfettiId = null;

function dispararConfetti(duracionMs = 4000) {
    if (!canvasConfetti) return;
    const ctx = canvasConfetti.getContext("2d");
    if (!ctx) return;

    canvasConfetti.width = window.innerWidth;
    canvasConfetti.height = window.innerHeight;

    const colores = ["#b84318", "#f59e0b", "#10b981", "#ef4444", "#3b82f6", "#ec4899", "#8b5cf6"];
    particulasConfetti = [];

    for (let i = 0; i < 90; i++) {
        particulasConfetti.push({
            x: Math.random() * canvasConfetti.width,
            y: Math.random() * -canvasConfetti.height * 0.5,
            ancho: Math.random() * 9 + 6,
            alto: Math.random() * 5 + 4,
            color: colores[Math.floor(Math.random() * colores.length)],
            velocidadY: Math.random() * 3 + 2.5,
            velocidadX: Math.random() * 3 - 1.5,
            rotacion: Math.random() * 360,
            velocidadRotacion: Math.random() * 6 - 3
        });
    }

    confettiActivo = true;

    function animar() {
        if (!confettiActivo) {
            ctx.clearRect(0, 0, canvasConfetti.width, canvasConfetti.height);
            return;
        }

        ctx.clearRect(0, 0, canvasConfetti.width, canvasConfetti.height);

        particulasConfetti.forEach((p) => {
            p.y += p.velocidadY;
            p.x += p.velocidadX;
            p.rotacion += p.velocidadRotacion;

            if (p.y > canvasConfetti.height) {
                p.y = -20;
                p.x = Math.random() * canvasConfetti.width;
            }

            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotacion * Math.PI) / 180);
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.ancho / 2, -p.alto / 2, p.ancho, p.alto);
            ctx.restore();
        });

        animacionConfettiId = requestAnimationFrame(animar);
    }

    if (animacionConfettiId) cancelAnimationFrame(animacionConfettiId);
    animar();

    setTimeout(() => {
        confettiActivo = false;
        if (animacionConfettiId) {
            cancelAnimationFrame(animacionConfettiId);
        }
        ctx.clearRect(0, 0, canvasConfetti.width, canvasConfetti.height);
    }, duracionMs);
}

window.addEventListener("resize", () => {
    if (canvasConfetti && confettiActivo) {
        canvasConfetti.width = window.innerWidth;
        canvasConfetti.height = window.innerHeight;
    }
});

// --------------------------------------------------------------------------
// REGISTRO DE EVENTOS
// --------------------------------------------------------------------------
botonIniciar.addEventListener("click", iniciarPartida);
botonSiguiente.addEventListener("click", siguientePregunta);

botonReiniciar.addEventListener("click", () => {
    if (!inputNombre.value.trim() && nombreJugador) {
        inputNombre.value = nombreJugador;
    }
    iniciarPartida();
});

botonCambiarJugador.addEventListener("click", () => {
    reproducirSonidoClick();
    pantallaFinal.hidden = true;
    pantallaInicio.hidden = false;
    inputNombre.value = "";
    ocultarAdvertenciaNombre();
    inputNombre.focus();
});

// Comodines
botonComodin5050.addEventListener("click", usarComodin5050);
botonComodinTiempo.addEventListener("click", usarComodinTiempo);

// Utilidades superiores
botonSonido.addEventListener("click", alternarSonido);
botonPantalla.addEventListener("click", alternarPantallaCompleta);
botonRankingTop.addEventListener("click", abrirModalRanking);
botonAbrirRankingInicio.addEventListener("click", abrirModalRanking);
botonVerRankingFinal.addEventListener("click", abrirModalRanking);

// Modal Ranking
botonCerrarRanking.addEventListener("click", cerrarModalRanking);
botonCerrarRankingPie.addEventListener("click", cerrarModalRanking);
botonLimpiarRanking.addEventListener("click", reiniciarTablaRanking);
modalRanking.addEventListener("click", (e) => {
    if (e.target === modalRanking) {
        cerrarModalRanking();
    }
});

// Limpiar advertencia en tiempo real al escribir
inputNombre.addEventListener("input", () => {
    if (inputNombre.value.trim().length > 0) {
        ocultarAdvertenciaNombre();
    }
});

// Teclado accesible (Enter para iniciar si está en el input)
inputNombre.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        iniciarPartida();
    }
});

// Inicialización de la UI
actualizarBotonSonido();
