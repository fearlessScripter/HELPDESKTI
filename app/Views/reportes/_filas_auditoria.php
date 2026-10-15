<?php
/**
 * Filas de la tabla "Auditoría de incumplimientos".
 * Se usa dos veces: en pantalla (paginada) y en la impresión / PDF (lista completa).
 *
 * @var list<array> $ticketsAuditoria
 */
use App\Models\ReporteModel;
?>
<?php if (!empty($ticketsAuditoria)): ?>
    <?php foreach ($ticketsAuditoria as $t): ?>
        <?php $brechas = ReporteModel::brechas($t); $numBrechas = count($brechas); ?>
        <?php foreach ($brechas as $i => $b): ?>
        <tr>
            <?php if ($i === 0): ?>
                <td class="fw-bold" rowspan="<?= $numBrechas ?>">
                    <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="text-decoration-none">#<?= esc($t['codigo_ticket']) ?></a>
                    <div class="small text-muted fw-normal"><?= esc(fecha_hora($t['fecha_creacion'])) ?></div>
                </td>
                <td rowspan="<?= $numBrechas ?>"><?= !empty($t['tecnico_nombre']) ? esc($t['tecnico_nombre']) : '<span class="text-muted fst-italic">Sin asignar</span>' ?></td>
                <td rowspan="<?= $numBrechas ?>" class="small"><?= esc($t['area_nombre']) ?></td>
                <td rowspan="<?= $numBrechas ?>"><?= badge_prioridad($t['prioridad_nombre'], (int)$t['sla_minutos']) ?></td>
            <?php endif; ?>
            <td>
                <span class="badge <?= $b['tipo'] === 'SLA' ? 'text-bg-secondary' : 'text-bg-info' ?>"><?= $b['tipo'] ?></span>
                <span class="small"><?= esc($b['detalle']) ?></span>
            </td>
            <td><span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-2">+<?= formato_minutos($b['minutos']) ?></span></td>
            <?php if ($i === 0): ?>
                <td rowspan="<?= $numBrechas ?>"><?= esc($t['estado_nombre']) ?></td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7" class="text-center text-muted py-4">
            <i class="bi bi-shield-check fs-2 d-block mb-2 text-success"></i>
            No hay incumplimientos de SLA ni OLA en el período seleccionado.
        </td>
    </tr>
<?php endif; ?>
