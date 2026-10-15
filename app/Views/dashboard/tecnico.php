<?= $this->extend('layout/principal') ?>
<?php
$this->setVar('menuActivo', 'inicio');
$this->setVar('topbar', ['pageTitle' => 'Inicio Técnico']);
?>

<?= $this->section('titulo') ?>Inicio - Técnico TI<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

    <!-- CONTENIDO -->
    <div class="content">

        <div class="mb-4">
            <h2 class="fw-bold">Hola, <?= esc($nombre) ?> (<?= esc($rol) ?>)</h2>
            <p class="text-muted fs-5">Resumen de tu trabajo asignado</p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Asignados -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0dcaf0 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Tickets Asignados</h6>
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-gear"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['en_proceso'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Resueltos -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #198754 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Tickets Resueltos</h6>
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['resueltos'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 fw-semibold">Mis Tickets En Proceso <small class="text-muted fs-6 fw-normal">(los 5 más próximos a vencer)</small></h4>
            <a href="<?= base_url('/tickets/mis-tickets') ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                Ver todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="ticket-table">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Ticket</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>SLA (Prioridad)</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($misTickets)): ?>
                        <?php foreach ($misTickets as $mt): ?>
                        <tr>
                            <td class="fw-bold">#<?= esc($mt['codigo_ticket'] ?? 'TK-' . $mt['id_ticket']) ?></td>
                            <td><?= esc($mt['solicitante_nombre']) ?></td>
                            <td><?= esc($mt['asunto']) ?></td>
                            <td>
                                <?= badge_prioridad($mt['prioridad_nombre'], (int)$mt['sla_minutos']) ?>
                            </td>
                            <td><span class="badge bg-warning text-dark"><?= esc($mt['estado_nombre']) ?></span></td>
                            <td class="text-center">
                                <a href="<?= base_url('/tickets/detalles/' . $mt['id_ticket']) ?>" class="btn btn-sm btn-primary fw-bold">Atender</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No tienes tickets asignados en proceso en este momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
<?= $this->endSection() ?>
