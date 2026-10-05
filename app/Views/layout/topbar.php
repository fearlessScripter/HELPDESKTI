<?php
$session = session();
$nombre = $session->get('nombre_completo');
$rol = $session->get('rol');
$pageTitle = $pageTitle ?? 'Inicio';
?>
<div class="topbar">

    <div class="page-title">
        <?= esc($pageTitle) ?>
    </div>

    <div class="d-flex align-items-center gap-3">

        <!-- Notificaciones -->
        <button type="button" class="btn btn-light position-relative">
            <i class="bi bi-bell fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                3
            </span>
        </button>

        <!-- Usuario -->
        <div class="dropdown">
            <button class="user-button dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <div class="text-end">
                    <div class="user-name">
                        <?= esc($nombre) ?>
                    </div>
                    <div class="user-role">
                        <?= esc($rol) ?>
                    </div>
                </div>
                <i class="bi bi-person-circle fs-3"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <span class="dropdown-item-text">
                        <strong><?= esc($nombre) ?></strong><br>
                        <small class="text-muted"><?= esc($rol) ?></small>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= base_url('/logout') ?>">
                        <i class="bi bi-box-arrow-right"></i>
                        Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>

    </div>

</div>
