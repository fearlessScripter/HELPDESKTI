<?php
$session = session();
$rol = $session->get('rol');
$active = $active ?? 'inicio';
?>
<div class="sidebar">

    <div class="sidebar-title">
        <i class="bi bi-headset"></i>
        HelpDesk TI
    </div>

    <!-- Inicio: Visible para todos -->
    <a href="<?= base_url('/dashboard') ?>" class="<?= $active === 'inicio' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i>
        <span>Inicio</span>
    </a>

    <!-- Tickets: Visible para todos. El Administrador parte en "Todos"; el resto en sus tickets -->
    <a href="<?= base_url($rol === 'Administrador' ? '/tickets/todos' : '/tickets/mis-tickets') ?>" class="<?= $active === 'tickets' ? 'active' : '' ?>">
        <i class="bi bi-ticket-detailed"></i>
        <span>Tickets</span>
    </a>

    <!-- Usuarios: Solo Administrador -->
    <?php if ($rol === 'Administrador'): ?>
    <a href="<?= base_url('/usuarios') ?>" class="<?= $active === 'usuarios' ? 'active' : '' ?>">
        <i class="bi bi-people"></i>
        <span>Usuarios</span>
    </a>
    <?php endif; ?>

    <!-- Base de conocimientos: Técnico y Administrador -->
    <?php if ($rol === 'Administrador' || $rol === 'Técnico'): ?>
    <a href="<?= base_url('/base-conocimientos') ?>" class="<?= $active === 'base_conocimientos' ? 'active' : '' ?>">
        <i class="bi bi-book"></i>
        <span>Base de conocimientos</span>
    </a>
    <?php endif; ?>

    <!-- Reportes: Solo Administrador -->
    <?php if ($rol === 'Administrador'): ?>
    <a href="<?= base_url('/reportes') ?>" class="<?= $active === 'reportes' ? 'active' : '' ?>">
        <i class="bi bi-bar-chart"></i>
        <span>Reportes</span>
    </a>
    <?php endif; ?>

    <!-- Acerca de: visible para todos, se abre hacia arriba como el menú de perfil -->
    <div class="dropup sidebar-acerca">
        <button type="button" class="btn-acerca" data-bs-toggle="dropdown" data-bs-offset="0,10"
                aria-expanded="false" title="Acerca de" aria-label="Acerca de <?= esc(APP_NOMBRE) ?>">
            <i class="bi bi-info-circle"></i>
        </button>
        <div class="dropdown-menu shadow acerca-menu">
            <div class="px-3 pt-2 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-headset fs-4 text-primary"></i>
                    <div>
                        <div class="fw-bold"><?= esc(APP_NOMBRE) ?></div>
                        <div class="small text-muted">Mesa de ayuda y gestión de incidencias</div>
                    </div>
                </div>
            </div>
            <div class="px-3 py-2 small">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted"><i class="bi bi-tag me-1"></i> Versión</span>
                    <span class="fw-semibold"><?= esc(APP_VERSION) ?></span>
                </div>
                <div class="py-1">
                    <div class="text-muted"><i class="bi bi-envelope me-1"></i> Contáctanos</div>
                    <a href="mailto:<?= esc(APP_CONTACTO, 'attr') ?>" class="fw-semibold"><?= esc(APP_CONTACTO) ?></a>
                </div>
            </div>
            <div class="px-3 pt-2 border-top small text-muted">
                &copy; <?= date('Y') ?> <?= esc(APP_NOMBRE) ?>. Todos los derechos reservados.
            </div>
        </div>
    </div>

</div>

<style>
    /* Capa propia para el menú lateral: sin esto, el contenido con position (ej. las .card de Bootstrap)
       se dibuja encima y tapa el panel "Acerca de", que sobresale hacia la derecha.
       1030 = nivel de barras fijas de Bootstrap: queda bajo modales (1050+) y sus fondos. */
    .sidebar { z-index: 1030; }
    .sidebar-acerca { position: absolute; left: 15px; bottom: 20px; }
    .btn-acerca {
        width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;
        border: 1px solid #495057; border-radius: 8px; background-color: #343a40; color: #ced4da;
        font-size: 1.25rem; transition: 0.2s;
    }
    .btn-acerca:hover, .btn-acerca[aria-expanded="true"] { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }
    .acerca-menu { width: 290px; padding-top: .5rem; padding-bottom: .5rem; }
    /* Las reglas ".sidebar a" del menú no deben aplicar a los enlaces dentro de este panel */
    .sidebar .acerca-menu a {
        display: inline; padding: 0; margin: 0; border-radius: 0; background: none;
        color: #0d6efd; text-decoration: none; word-break: break-all;
    }
    .sidebar .acerca-menu a:hover { text-decoration: underline; background: none; }
</style>
