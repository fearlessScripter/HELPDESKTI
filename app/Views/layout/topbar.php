<?php
$session = session();
$nombre = $session->get('nombre_completo');
$rol = $session->get('rol');
$idUsuario = (int)$session->get('id_usuario');
$pageTitle = $pageTitle ?? 'Inicio';

$notifModel = new \App\Models\NotificacionModel();

// Cargar notificaciones dinámicas si no vienen inyectadas
if (!isset($notificaciones)) {
    $notificaciones = $notifModel->getPorUsuario($idUsuario, 5);
    $conteoNoLeidas = $notifModel->getConteoNoLeidas($idUsuario);
}
$conteoNoLeidas = (int)($conteoNoLeidas ?? 0);

// Marca para la actualización en tiempo real: solo se avisa lo que llegue después de esta carga
$ultimoIdNotif = $notifModel->getUltimoId($idUsuario);
?>
<div class="topbar">

    <div class="page-title">
        <?= esc($pageTitle) ?>
    </div>

    <div class="d-flex align-items-center gap-3">
        <?php if(isset($topbarButton)): ?>
            <?= $topbarButton ?>
        <?php endif; ?>

        <!-- Notificaciones (se actualizan en tiempo real, ver layout/tiempo_real) -->
        <div class="dropdown" data-notificaciones
             data-url="<?= base_url('/notificaciones/estado') ?>"
             data-ultimo="<?= $ultimoIdNotif ?>"
             data-conteo="<?= $conteoNoLeidas ?>">
            <button type="button" class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones" data-notif-campana>
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger<?= $conteoNoLeidas > 0 ? '' : ' d-none' ?>" data-notif-conteo>
                    <?= $conteoNoLeidas > 99 ? '99+' : $conteoNoLeidas ?>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 360px; max-height: 400px; overflow-y: auto;" data-notif-menu>
                <?= view('layout/notificaciones_menu', ['notificaciones' => $notificaciones, 'conteoNoLeidas' => $conteoNoLeidas]) ?>
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
