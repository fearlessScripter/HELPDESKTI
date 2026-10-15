<?= $this->extend('layout/principal') ?>
<?php
$this->setVar('menuActivo', 'tickets');
$this->setVar('topbar', ['pageTitle' => 'Inicio (Todos los Tickets)']);
?>

<?= $this->section('titulo') ?>Tickets - HelpDesk TI<?= $this->endSection() ?>

<?= $this->section('contenido') ?>



    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->

    <div class="content">


        <!-- PESTAÑAS -->

        <?= view('layout/ticket_tabs', ['activeTab' => 'todos']) ?>



        <!-- BUSCADOR Y FILTROS -->

        <?= view('layout/filtros_tickets', ['campos' => ['estado', 'area', 'prioridad', 'tecnico', 'tecnico_sin', 'sla', 'fechas']]) ?>




        <!-- ================================================= -->
        <!-- TABLA DE TICKETS -->
        <!-- ================================================= -->

        <div class="ticket-table" data-region="resultados">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Ticket</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Área</th>
                        <th>Técnico</th>
                        <th>Prioridad (SLA)</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody data-resaltable>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td class="fw-bold">
                                <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="text-decoration-none">
                                    #<?= esc($t['codigo_ticket'] ?? 'TK-' . $t['id_ticket']) ?>
                                </a>
                            </td>
                            <td><?= esc($t['solicitante_nombre']) ?></td>
                            <td><?= esc($t['asunto']) ?></td>
                            <td><?= esc($t['area_nombre']) ?></td>
                            <td>
                                <?= !empty($t['tecnico_nombre']) ? esc($t['tecnico_nombre']) : '<span class="text-muted small fst-italic">Sin asignar</span>' ?>
                            </td>
                            <td>
                                <?= badge_prioridad($t['prioridad_nombre'], (int)$t['sla_minutos']) ?>
                            </td>
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
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="btn btn-outline-secondary" title="Ver Detalles">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <?php if ($rol === 'Administrador' && empty($t['id_tecnico_asignado'])): ?>
                                    <button class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalAsignarTodos<?= $t['id_ticket'] ?>">
                                        <i class="bi bi-person-plus"></i> Asignar
                                    </button>
                                    <?php endif; ?>
                                </div>

                                <?php if ($rol === 'Administrador' && empty($t['id_tecnico_asignado'])): ?>
                                <div class="modal fade" id="modalAsignarTodos<?= $t['id_ticket'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog text-start">
                                        <div class="modal-content">
                                            <form action="<?= base_url('/tickets/asignar') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id_ticket" value="<?= $t['id_ticket'] ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Asignar Técnico a #<?= esc($t['codigo_ticket']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label fw-bold">Técnico <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="id_tecnico" required>
                                                        <option value="" selected disabled>Selecciona un técnico...</option>
                                                        <?php if (!empty($tecnicos)): ?>
                                                            <?php foreach ($tecnicos as $tec): ?>
                                                                <option value="<?= esc($tec['id_usuario']) ?>">
                                                                    <?= esc($tec['nombre_completo']) ?> (<?= esc($tec['area'] ?? 'Soporte TI') ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success fw-bold">Asignar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?= view('layout/sin_resultados', ['columnas' => 8, 'mensaje' => 'No hay tickets registrados en el sistema.']) ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?= view('layout/paginacion') ?>
        </div>


    </div>
<?= $this->endSection() ?>
