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
        <?php if(isset($topbarButton)): ?>
            <?= $topbarButton ?>
        <?php endif; ?>

        <!-- Notificaciones -->
        <div class="dropdown">
            <button type="button" class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 350px;">
                <li><h6 class="dropdown-header fw-bold">Notificaciones Recientes</h6></li>
                <li><a class="dropdown-item text-wrap py-2" href="#"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> ALERTA SLA: Ticket #TCK-1023 superó el tiempo máximo de resolución (+3.5 hrs).</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-wrap py-2" href="#"><i class="bi bi-info-circle-fill text-info me-2"></i> NUEVO TICKET: Se asignó el ticket #TCK-1090 (Prioridad: Alta) a tu cola operativa.</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-wrap py-2" href="#"><i class="bi bi-check-circle-fill text-success me-2"></i> TICKET CERRADO: El solicitante confirmó la solución del ticket #TCK-1042.</a></li>
            </ul>
        </div>

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
