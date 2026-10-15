<?= $this->extend('layout/principal') ?>
<?php
$this->setVar('menuActivo', 'inicio');
$this->setVar('topbar', ['pageTitle' => 'Inicio']);
?>

<?= $this->section('titulo') ?>Inicio - HelpDesk TI<?= $this->endSection() ?>

<?= $this->section('contenido') ?>



    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->

    <div class="content">

        <div class="mb-4">
            <h2 class="fw-bold">Hola, <?= esc($nombre) ?></h2>
            <p class="text-muted fs-5">¿En qué podemos ayudarte?</p>
        </div>

        <div class="mb-4">
            <h4 class="mb-0 fw-semibold">Mis Solicitudes</h4>
        </div>

        <div class="row g-4">
            <!-- Pendientes -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #dc3545 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Pendientes</h6>
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['pendientes'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>

            <!-- En Proceso -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0dcaf0 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">En Proceso</h6>
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-gear"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['en_proceso'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Resueltas -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #198754 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Resueltas <small class="text-lowercase">(esperando confirmación)</small></h6>
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['resueltos'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="<?= base_url('/tickets/nuevo') ?>" class="btn btn-primary btn-lg fw-bold shadow px-5 py-3 rounded-pill" style="font-size: 1.1rem;">
                <i class="bi bi-plus-circle me-2"></i> CREAR TICKET
            </a>
        </div>

        <!-- Últimos tickets del solicitante (máx. 5) -->
        <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
            <h4 class="mb-0 fw-semibold">Mis Últimos Tickets</h4>
            <a href="<?= base_url('/tickets/mis-tickets') ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                Ver todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card border-0 shadow-sm overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-muted">ID Ticket</th>
                        <th class="small text-muted">Asunto</th>
                        <th class="small text-muted">Área</th>
                        <th class="small text-muted">Fecha</th>
                        <th class="small text-muted">Estado</th>
                        <th class="small text-muted text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($misTickets)): ?>
                        <?php foreach ($misTickets as $t): ?>
                        <tr>
                            <td class="fw-bold">
                                <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="text-decoration-none">#<?= esc($t['codigo_ticket']) ?></a>
                            </td>
                            <td><?= esc($t['asunto']) ?></td>
                            <td class="small text-muted"><?= esc($t['area_nombre']) ?></td>
                            <td class="small"><?= esc(fecha_hora($t['fecha_creacion'])) ?></td>
                            <td>
                                <?php if ($t['estado_nombre'] === 'Pendiente'): ?>
                                    <span class="badge bg-danger">Pendiente</span>
                                <?php elseif ($t['estado_nombre'] === 'En Proceso'): ?>
                                    <span class="badge bg-warning text-dark">En Proceso</span>
                                <?php elseif ($t['estado_nombre'] === 'Resuelto'): ?>
                                    <span class="badge bg-success">Resuelto</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Cerrado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ((int)$t['id_estado'] === 3): ?>
                                    <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="btn btn-sm btn-success fw-bold">
                                        <i class="bi bi-check2-circle"></i> Confirmar Cierre
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="btn btn-sm btn-outline-secondary">Ver Detalles</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Aún no has creado tickets. Usa el botón <strong>Crear Ticket</strong> para registrar tu primera solicitud.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
<?= $this->endSection() ?>
