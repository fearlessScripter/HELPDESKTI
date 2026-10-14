<?php
/**
 * Tono claro / oscuro de la interfaz. Se incluye dentro del <head> de cada página.
 *
 * El <html> ya trae data-bs-theme desde el servidor (sin parpadeo al cargar). Bootstrap 5.3
 * adapta solo sus componentes (menús, modales, formularios, alertas); aquí se corrigen los
 * colores fijos propios del sistema (fondos "white", table-light, bg-white, text-dark...).
 */
?>
<style>
    /* Resaltado de búsqueda: texto oscuro sobre amarillo en ambos tonos */
    mark.resaltado { color: #212529; }

    [data-bs-theme="dark"] {
        --hd-fondo-pagina: #16191d;
        --hd-superficie: var(--bs-body-bg);       /* tarjetas, tablas, barra superior */
        --hd-superficie-2: var(--bs-tertiary-bg); /* encabezados, zonas secundarias */
        --hd-borde: var(--bs-border-color);
        color-scheme: dark;
    }

    /* Estructura */
    [data-bs-theme="dark"] body { background-color: var(--hd-fondo-pagina) !important; }
    [data-bs-theme="dark"] .sidebar { background-color: #0f1114; border-right: 1px solid #23272c; }
    [data-bs-theme="dark"] .sidebar a:hover { background-color: #1f2328; }
    [data-bs-theme="dark"] .btn-acerca { background-color: #1f2328; border-color: #343a40; }
    [data-bs-theme="dark"] .topbar { background-color: var(--hd-superficie); border-bottom-color: var(--hd-borde); }
    [data-bs-theme="dark"] .user-button { color: var(--bs-body-color); }
    [data-bs-theme="dark"] .user-button:hover { background-color: var(--hd-superficie-2); }
    [data-bs-theme="dark"] .user-role,
    [data-bs-theme="dark"] .ticket-table th,
    [data-bs-theme="dark"] .ticket-tabs a { color: var(--bs-secondary-color); }
    [data-bs-theme="dark"] .ticket-tabs { border-bottom-color: var(--hd-borde); }
    [data-bs-theme="dark"] .ticket-tabs a.active,
    [data-bs-theme="dark"] .ticket-tabs a:hover { color: #6ea8fe; border-bottom-color: #6ea8fe; }

    /* Superficies: en oscuro la sombra no separa, se usa un borde sutil */
    [data-bs-theme="dark"] .ticket-table { background-color: var(--hd-superficie); }
    [data-bs-theme="dark"] .ticket-table,
    [data-bs-theme="dark"] .card.shadow-sm { box-shadow: 0 0 0 1px rgba(255, 255, 255, .07) !important; }

    /* Utilidades de Bootstrap con color fijo claro */
    [data-bs-theme="dark"] .bg-white { background-color: var(--hd-superficie) !important; }
    [data-bs-theme="dark"] .bg-light { background-color: var(--hd-superficie-2) !important; }
    [data-bs-theme="dark"] .text-bg-light { background-color: var(--hd-superficie-2) !important; color: var(--bs-body-color) !important; }
    [data-bs-theme="dark"] .text-dark:not(.badge):not(.btn):not([class*="bg-warning"]) { color: var(--bs-body-color) !important; }
    [data-bs-theme="dark"] .table-light {
        --bs-table-bg: var(--hd-superficie-2);
        --bs-table-color: var(--bs-body-color);
        --bs-table-border-color: var(--hd-borde);
        --bs-table-striped-bg: var(--hd-superficie-2);
        --bs-table-hover-bg: var(--hd-superficie-2);
    }
    [data-bs-theme="dark"] .btn-light {
        --bs-btn-bg: var(--hd-superficie-2);
        --bs-btn-border-color: var(--hd-borde);
        --bs-btn-color: var(--bs-body-color);
        --bs-btn-hover-bg: #343a40;
        --bs-btn-hover-border-color: #495057;
        --bs-btn-hover-color: #fff;
        --bs-btn-active-bg: #343a40;
        --bs-btn-active-color: #fff;
    }

    /* Colores de texto: Bootstrap mantiene los tonos del modo claro (bajo contraste sobre fondo oscuro);
       se usan sus variantes "emphasis", pensadas para el modo oscuro */
    [data-bs-theme="dark"] .text-secondary { color: var(--bs-secondary-color) !important; }
    [data-bs-theme="dark"] .text-primary   { color: var(--bs-primary-text-emphasis) !important; }
    [data-bs-theme="dark"] .text-success   { color: var(--bs-success-text-emphasis) !important; }
    [data-bs-theme="dark"] .text-danger    { color: var(--bs-danger-text-emphasis) !important; }
    [data-bs-theme="dark"] .text-warning   { color: var(--bs-warning-text-emphasis) !important; }
    [data-bs-theme="dark"] .text-info      { color: var(--bs-info-text-emphasis) !important; }
    [data-bs-theme="dark"] .link-primary   { color: var(--bs-primary-text-emphasis) !important; }
    [data-bs-theme="dark"] .dropdown-header { color: var(--bs-secondary-color); }

    /* Botones delineados: texto y borde aclarados para que se lean sobre fondo oscuro */
    [data-bs-theme="dark"] .btn-outline-secondary {
        --bs-btn-color: #c4cad0; --bs-btn-border-color: #6c757d;
        --bs-btn-hover-bg: #495057; --bs-btn-hover-border-color: #6c757d; --bs-btn-hover-color: #fff;
        --bs-btn-active-bg: #5c636a; --bs-btn-active-border-color: #6c757d; --bs-btn-active-color: #fff;
    }
    [data-bs-theme="dark"] .btn-outline-primary {
        --bs-btn-color: #8bb9fe; --bs-btn-border-color: #6ea8fe;
        --bs-btn-hover-bg: #0d6efd; --bs-btn-hover-border-color: #0d6efd; --bs-btn-hover-color: #fff;
    }
    [data-bs-theme="dark"] .btn-outline-success { --bs-btn-color: #75b798; --bs-btn-border-color: #75b798; }
    [data-bs-theme="dark"] .btn-outline-danger  { --bs-btn-color: #ea868f; --bs-btn-border-color: #ea868f; }

    /* Detalle del ticket: línea de tiempo del historial */
    [data-bs-theme="dark"] .timeline { border-left-color: var(--hd-borde); }
    [data-bs-theme="dark"] .timeline-item::before { border-color: var(--hd-superficie); }

    /* Reportes: pista de las barras */
    [data-bs-theme="dark"] .barra-pista { background-color: var(--hd-superficie-2); }

    /* Selector de tono en el menú de perfil */
    .selector-tono .btn.active { pointer-events: none; }
</style>
<script>
(function () {
    var raiz = document.documentElement;
    var URL_GUARDAR = <?= json_encode(base_url('/perfil/tema')) ?>;

    function aplicar(tema) {
        raiz.setAttribute('data-bs-theme', tema === 'oscuro' ? 'dark' : 'light');
        document.querySelectorAll('[data-tema]').forEach(function (b) {
            var activo = b.dataset.tema === tema;
            b.classList.toggle('active', activo);
            b.setAttribute('aria-pressed', activo ? 'true' : 'false');
        });
    }

    // Botones "Claro" / "Oscuro" del menú de perfil
    document.addEventListener('click', function (e) {
        var boton = e.target.closest('[data-tema]');
        if (!boton) return;
        var tema = boton.dataset.tema === 'oscuro' ? 'oscuro' : 'claro';
        aplicar(tema);
        // Cookie: para que la pantalla de login use el último tono de este navegador
        document.cookie = 'tema=' + tema + '; path=/; max-age=31536000; SameSite=Lax';
        // Se guarda en el perfil del usuario (lo sigue en cualquier equipo)
        fetch(URL_GUARDAR, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'tema=' + encodeURIComponent(tema)
        }).catch(function () { /* el tono ya se aplicó; si falla se reintenta en el próximo cambio */ });
    });

    // Al imprimir / guardar PDF siempre se usa el tono claro (texto claro sobre papel sería ilegible)
    var temaAntesDeImprimir = null;
    window.addEventListener('beforeprint', function () {
        temaAntesDeImprimir = raiz.getAttribute('data-bs-theme');
        raiz.setAttribute('data-bs-theme', 'light');
    });
    window.addEventListener('afterprint', function () {
        if (temaAntesDeImprimir) raiz.setAttribute('data-bs-theme', temaAntesDeImprimir);
    });
})();
</script>
