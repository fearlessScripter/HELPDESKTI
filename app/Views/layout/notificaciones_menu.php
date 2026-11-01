<?php
/**
 * Contenido del menú de la campana de notificaciones.
 *
 * Lo usan el topbar al cargar la página y Notificaciones::estado() al actualizarlo en tiempo real,
 * así el menú se arma en un solo lugar.
 *
 * @var list<array> $notificaciones
 * @var int         $conteoNoLeidas
 */
?>
<li class="d-flex justify-content-between align-items-center pe-2">
    <h6 class="dropdown-header fw-bold">Notificaciones Recientes</h6>
    <?php if ($conteoNoLeidas > 0): ?>
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
                <i class="bi <?= icono_notificacion($n['tipo_alerta']) ?> me-2"></i>
                <span><?= esc($n['mensaje']) ?></span>
                <br><small class="text-muted ms-4" style="font-size: 11px;"><?= esc(fecha_hora($n['fecha_creacion'])) ?></small>
            </a>
        </li>
        <li><hr class="dropdown-divider my-1"></li>
    <?php endforeach; ?>
<?php else: ?>
    <li><span class="dropdown-item text-muted small py-2">No tienes notificaciones pendientes.</span></li>
<?php endif; ?>
