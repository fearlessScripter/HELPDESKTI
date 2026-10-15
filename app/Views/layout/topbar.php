<?php
$session = session();
$nombre = $session->get('nombre_completo');
$rol = $session->get('rol');
$idUsuario = (int)$session->get('id_usuario');
$pageTitle = $pageTitle ?? 'Inicio';

// Cargar notificaciones dinámicas si no vienen inyectadas
if (!isset($notificaciones)) {
    $notifModel = new \App\Models\NotificacionModel();
    $notificaciones = $notifModel->getPorUsuario($idUsuario, 5);
    $conteoNoLeidas = $notifModel->getConteoNoLeidas($idUsuario);
}
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
            <button type="button" class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones">
                <i class="bi bi-bell fs-5"></i>
                <?php if (!empty($conteoNoLeidas) && $conteoNoLeidas > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= esc($conteoNoLeidas) ?>
                </span>
                <?php endif; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 360px; max-height: 400px; overflow-y: auto;">
                <li class="d-flex justify-content-between align-items-center pe-2">
                    <h6 class="dropdown-header fw-bold">Notificaciones Recientes</h6>
                    <?php if (!empty($conteoNoLeidas) && $conteoNoLeidas > 0): ?>
                    <form action="<?= base_url('/notificaciones/leer-todas') ?>" method="post" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0" style="font-size: 12px;">
                            <i class="bi bi-check2-all"></i> Marcar todas como leídas
                        </button>
                    </form>
                    <?php endif; ?>
                </li>
                <?php if (!empty($notificaciones)): ?>
                    <?php foreach ($notificaciones as $n): ?>
                        <?php $noLeida = (int)$n['leida'] === 0; ?>
                        <li>
                            <a class="dropdown-item text-wrap py-2 <?= $noLeida ? 'fw-semibold bg-primary-subtle' : 'text-secondary' ?>" href="<?= base_url('/notificaciones/leer/' . $n['id_notificacion']) ?>">
                                <?php if ($noLeida): ?>
                                    <i class="bi bi-circle-fill text-primary me-1" style="font-size: 8px;" title="No leída"></i>
                                <?php endif; ?>
                                <?php if ($n['tipo_alerta'] === 'alerta' || $n['tipo_alerta'] === 'sla_vencido' || $n['tipo_alerta'] === 'urgente'): ?>
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                <?php elseif ($n['tipo_alerta'] === 'resolucion'): ?>
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <?php elseif ($n['tipo_alerta'] === 'asignacion'): ?>
                                    <i class="bi bi-gear-fill text-primary me-2"></i>
                                <?php else: ?>
                                    <i class="bi bi-info-circle-fill text-info me-2"></i>
                                <?php endif; ?>
                                <span><?= esc($n['mensaje']) ?></span>
                                <br><small class="text-muted ms-4" style="font-size: 11px;"><?= esc(fecha_hora($n['fecha_creacion'])) ?></small>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><span class="dropdown-item text-muted small py-2">No tienes notificaciones pendientes.</span></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Usuario -->
        <div class="dropdown">
            <!-- auto-close="outside": al elegir el tono el menú sigue abierto para ver el cambio -->
            <button class="user-button dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <div class="text-end">
                    <div class="user-name">
                        <?= esc($nombre) ?>
                    </div>
                    <div class="user-role">
                        <?= esc($rol) ?>
                    </div>
                </div>
                <i class="bi bi-person-circle fs-3 text-secondary"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 240px;">
                <li>
                    <span class="dropdown-item-text">
                        <strong><?= esc($nombre) ?></strong><br>
                        <small class="text-muted"><?= esc($rol) ?></small>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li class="px-3 py-1">
                    <?php $tono = tema_actual(); ?>
                    <div class="small text-muted mb-2"><i class="bi bi-circle-half me-1"></i> Tono</div>
                    <div class="btn-group btn-group-sm w-100 selector-tono" role="group" aria-label="Tono de la interfaz">
                        <button type="button" class="btn btn-outline-secondary <?= $tono === 'claro' ? 'active' : '' ?>"
                                data-tema="claro" aria-pressed="<?= $tono === 'claro' ? 'true' : 'false' ?>">
                            <i class="bi bi-sun me-1"></i> Claro
                        </button>
                        <button type="button" class="btn btn-outline-secondary <?= $tono === 'oscuro' ? 'active' : '' ?>"
                                data-tema="oscuro" aria-pressed="<?= $tono === 'oscuro' ? 'true' : 'false' ?>">
                            <i class="bi bi-moon-stars me-1"></i> Oscuro
                        </button>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= base_url('/logout') ?>">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>

    </div>

</div>

<!-- Alertas Flash -->
<?php if (session()->getFlashdata('success')): ?>
<div class="container-fluid px-4 pt-3">
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-0" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="container-fluid px-4 pt-3">
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-0" role="alert">
        <i class="bi bi-exclamation-octagon-fill me-2"></i>
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>
