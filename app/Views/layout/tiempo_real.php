<?php
/**
 * Actualización en tiempo real de la campana de notificaciones. Se incluye en el layout principal.
 *
 * Cada pocos segundos consulta Notificaciones::estado() con la última notificación conocida.
 * El servidor responde solo dos números si nada cambió; si hubo cambios:
 *   - se actualizan el contador, el menú de la campana y el título de la pestaña ("(2) ...");
 *   - las notificaciones nuevas se muestran como aviso en la esquina, con un tono breve;
 *   - los contenedores con data-tiempo-real + data-region (listados y paneles de inicio) se vuelven
 *     a pedir al servidor y se reemplazan sin recargar la página.
 *
 * Con la pestaña oculta la consulta se espacia, y si el servidor no responde se reintenta con
 * espera creciente. Ver la ficha de un ticket: un elemento con data-ticket-actual="<id>".
 */
?>
<style>
    @keyframes campana-vaiven {
        0%, 100% { transform: rotate(0); }
        15% { transform: rotate(14deg); }
        30% { transform: rotate(-12deg); }
        45% { transform: rotate(8deg); }
        60% { transform: rotate(-5deg); }
        75% { transform: rotate(2deg); }
    }
    [data-notif-campana] .bi-bell { display: inline-block; transform-origin: 50% 0; }
    [data-notif-campana].sonando .bi-bell { animation: campana-vaiven .9s ease-in-out; }
    @media (prefers-reduced-motion: reduce) {
        [data-notif-campana].sonando .bi-bell { animation: none; }
    }
    .avisos-tiempo-real { z-index: 1090; }
</style>
<script>
(function () {
    var raiz = document.querySelector('[data-notificaciones]');
    if (!raiz || !window.fetch || !window.bootstrap) return;

    var INTERVALO_MS = 5000;          // pestaña visible
    var INTERVALO_OCULTO_MS = 30000;  // pestaña en segundo plano
    var INTERVALO_MAX_MS = 60000;     // tope de espera mientras el servidor no responde
    var MAX_AVISOS = 3;               // más notificaciones juntas se resumen en un solo aviso
    var DURACION_AVISO_MS = 8000;

    var urlEstado = raiz.dataset.url;
    var ultimo = parseInt(raiz.dataset.ultimo, 10) || 0;
    var conteo = parseInt(raiz.dataset.conteo, 10) || 0;
    var campana = raiz.querySelector('[data-notif-campana]');
    var insignia = raiz.querySelector('[data-notif-conteo]');
    var menu = raiz.querySelector('[data-notif-menu]');
    var fichaTicket = document.querySelector('[data-ticket-actual]');
    var ticketActual = fichaTicket ? (parseInt(fichaTicket.dataset.ticketActual, 10) || 0) : 0;
    var tituloBase = document.title.replace(/^\(\d+\+?\)\s*/, '');

    var temporizador = null;
    var consultando = false;
    var espera = INTERVALO_MS;
    var menuPendiente = null;        // menú recibido mientras el usuario lo tenía abierto
    var regionesPendientes = false;  // listados por refrescar cuando el usuario termine lo que hace
    var audio = null;
    var contenedorAvisos = null;

    // ---------------------------------------------------------------- consulta periódica

    function programar(ms) {
        clearTimeout(temporizador);
        temporizador = setTimeout(consultar, ms);
    }

    function siguienteEspera() {
        return document.hidden ? Math.max(espera, INTERVALO_OCULTO_MS) : espera;
    }

    function consultar() {
        if (consultando) return;
        consultando = true;

        var url = new URL(urlEstado, window.location.href);
        url.searchParams.set('ultimo', ultimo);
        url.searchParams.set('conteo', conteo);

        fetch(url.toString(), {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (resp) {
                // Sesión vencida o cuenta deshabilitada: se deja que el sistema lleve al login
                if (resp.status === 401) { window.location.reload(); return null; }
                if (resp.redirected) { window.location.href = resp.url; return null; }
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                return resp.json();
            })
            .then(function (estado) {
                if (!estado) return; // se está saliendo de la página
                aplicar(estado);
                espera = INTERVALO_MS;
                programar(siguienteEspera());
            })
            .catch(function () {
                espera = Math.min(espera * 2, INTERVALO_MAX_MS);
                programar(siguienteEspera());
            })
            .finally(function () { consultando = false; });
    }

    function aplicar(estado) {
        ultimo = estado.ultimo;
        conteo = estado.conteo;
        actualizarContador();

        if (typeof estado.menu === 'string') reemplazarMenu(estado.menu);

        var nuevas = estado.nuevas || [];
        if (nuevas.length) {
            mostrarAvisos(nuevas);
            avisarConCampana();
            actualizarRegiones();
        } else if (regionesPendientes) {
            actualizarRegiones();
        }
    }

    // ---------------------------------------------------------------- campana

    function actualizarContador() {
        insignia.textContent = conteo > 99 ? '99+' : String(conteo);
        insignia.classList.toggle('d-none', conteo === 0);
        document.title = conteo > 0 ? '(' + (conteo > 99 ? '99+' : conteo) + ') ' + tituloBase : tituloBase;
    }

    // No se cambia el menú mientras el usuario lo está leyendo: se aplica al cerrarlo
    function reemplazarMenu(html) {
        if (menu.classList.contains('show')) { menuPendiente = html; return; }
        menu.innerHTML = html;
    }

    raiz.addEventListener('hidden.bs.dropdown', function () {
        if (menuPendiente !== null) {
            menu.innerHTML = menuPendiente;
            menuPendiente = null;
        }
    });

    function avisarConCampana() {
        campana.classList.remove('sonando');
        void campana.offsetWidth; // reinicia la animación si llegan avisos seguidos
        campana.classList.add('sonando');
        sonar();
    }

    // Tono breve generado con Web Audio (sin archivos). El navegador solo permite sonido después de
    // que el usuario interactuó con la página, por eso el audio se prepara en el primer clic o tecla.
    function prepararAudio() {
        var Contexto = window.AudioContext || window.webkitAudioContext;
        if (!Contexto) return;
        try {
            audio = audio || new Contexto();
            if (audio.state === 'suspended') audio.resume();
        } catch (e) { audio = null; }
    }
    document.addEventListener('pointerdown', prepararAudio, { once: true });
    document.addEventListener('keydown', prepararAudio, { once: true });

    function sonar() {
        if (!audio || audio.state !== 'running') return;
        var inicio = audio.currentTime;
        [880, 1320].forEach(function (frecuencia, i) {
            var t = inicio + i * 0.12;
            var oscilador = audio.createOscillator();
            var volumen = audio.createGain();
            oscilador.type = 'sine';
            oscilador.frequency.value = frecuencia;
            volumen.gain.setValueAtTime(0.0001, t);
            volumen.gain.exponentialRampToValueAtTime(0.08, t + 0.02);
            volumen.gain.exponentialRampToValueAtTime(0.0001, t + 0.35);
            oscilador.connect(volumen).connect(audio.destination);
            oscilador.start(t);
            oscilador.stop(t + 0.4);
        });
    }

    // ---------------------------------------------------------------- avisos emergentes

    function mostrarAvisos(nuevas) {
        if (nuevas.length > MAX_AVISOS) {
            crearAviso({
                icono: 'bi-bell-fill text-primary',
                titulo: 'Notificaciones nuevas',
                mensaje: 'Tienes ' + nuevas.length + ' notificaciones nuevas.',
                accion: { texto: 'Ver notificaciones', alHacerClic: abrirMenu }
            });
            return;
        }

        // El servidor las envía de la más nueva a la más antigua; se muestran en orden de llegada
        nuevas.slice().reverse().forEach(function (n) {
            var accion = null;
            if (n.idTicket) {
                accion = { texto: n.idTicket === ticketActual ? 'Actualizar vista' : 'Ver ticket', href: n.url };
            }
            crearAviso({ icono: n.icono, titulo: 'Nueva notificación', fecha: n.fecha, mensaje: n.mensaje, accion: accion });
        });
    }

    function abrirMenu() {
        bootstrap.Dropdown.getOrCreateInstance(campana).show();
    }

    // Se arma con nodos (textContent), nunca con innerHTML: el mensaje viene de la base de datos
    function crearAviso(opciones) {
        if (!contenedorAvisos) {
            contenedorAvisos = elemento('div', 'toast-container position-fixed bottom-0 end-0 p-3 avisos-tiempo-real');
            document.body.appendChild(contenedorAvisos);
        }

        var aviso = elemento('div', 'toast');
        aviso.setAttribute('role', 'status');
        aviso.setAttribute('aria-live', 'polite');
        aviso.setAttribute('aria-atomic', 'true');

        var encabezado = elemento('div', 'toast-header');
        encabezado.appendChild(elemento('i', 'bi ' + opciones.icono + ' me-2'));
        encabezado.appendChild(elemento('strong', 'me-auto', opciones.titulo));
        if (opciones.fecha) encabezado.appendChild(elemento('small', 'text-body-secondary', opciones.fecha));
        var cerrar = elemento('button', 'btn-close');
        cerrar.type = 'button';
        cerrar.setAttribute('data-bs-dismiss', 'toast');
        cerrar.setAttribute('aria-label', 'Cerrar');
        encabezado.appendChild(cerrar);

        var cuerpo = elemento('div', 'toast-body', opciones.mensaje);
        if (opciones.accion) {
            var pie = elemento('div', 'mt-2 pt-2 border-top text-end');
            var boton = elemento('a', 'btn btn-primary btn-sm', opciones.accion.texto);
            if (opciones.accion.href) {
                boton.href = opciones.accion.href;
            } else {
                boton.href = '#';
                boton.addEventListener('click', function (e) {
                    e.preventDefault();
                    bootstrap.Toast.getOrCreateInstance(aviso).hide();
                    opciones.accion.alHacerClic();
                });
            }
            pie.appendChild(boton);
            cuerpo.appendChild(pie);
        }

        aviso.appendChild(encabezado);
        aviso.appendChild(cuerpo);
        contenedorAvisos.appendChild(aviso);
        aviso.addEventListener('hidden.bs.toast', function () { aviso.remove(); });
        bootstrap.Toast.getOrCreateInstance(aviso, { delay: DURACION_AVISO_MS }).show();
    }

    function elemento(etiqueta, clases, texto) {
        var el = document.createElement(etiqueta);
        el.className = clases;
        if (texto !== undefined) el.textContent = texto;
        return el;
    }

    // ---------------------------------------------------------------- listados en vivo

    // Misma técnica que la búsqueda en vivo: se pide la página actual (con sus filtros) y se
    // reemplazan solo las regiones marcadas.
    function actualizarRegiones() {
        if (!document.querySelector('[data-tiempo-real][data-region]')) return;
        if (usuarioOcupado()) { regionesPendientes = true; return; }
        regionesPendientes = false;

        fetch(window.location.href, {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (resp) { return resp.ok && !resp.redirected ? resp.text() : null; })
            .then(function (html) {
                if (html === null) return;
                if (usuarioOcupado()) { regionesPendientes = true; return; }
                var doc = new DOMParser().parseFromString(html, 'text/html');
                document.querySelectorAll('[data-tiempo-real][data-region]').forEach(function (actual) {
                    var nuevo = doc.querySelector('[data-region="' + actual.dataset.region + '"]');
                    if (nuevo) actual.replaceWith(document.importNode(nuevo, true));
                });
            })
            .catch(function () { regionesPendientes = true; });
    }

    // No se reemplaza contenido mientras el usuario lo está usando: modal abierto,
    // menú desplegado o escribiendo en un campo dentro de la región.
    function usuarioOcupado() {
        if (document.querySelector('.modal.show, .dropdown-menu.show')) return true;
        var activo = document.activeElement;
        return !!(activo && activo.matches('input, textarea, select') && activo.closest('[data-tiempo-real]'));
    }

    // ---------------------------------------------------------------- inicio

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            espera = INTERVALO_MS;
            programar(0); // al volver a la pestaña se consulta de inmediato
        }
    });

    actualizarContador();
    programar(INTERVALO_MS);
})();
</script>
