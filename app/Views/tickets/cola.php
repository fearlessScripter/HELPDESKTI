<?= $this->extend('layout/principal') ?>
<?php
$this->setVar('menuActivo', 'tickets');
$this->setVar('topbar', ['pageTitle' => 'Tickets Sin Asignar']);
?>

<?= $this->section('titulo') ?>Tickets - HelpDesk TI<?= $this->endSection() ?>

<?= $this->section('contenido') ?>



    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->

    <div class="content">


        <!-- PESTAÑAS -->

        <?= view('layout/ticket_tabs', ['activeTab' => 'cola']) ?>



        <!-- BUSCADOR Y FILTROS -->

        <?= view('layout/filtros_tickets', ['campos' => ['area', 'prioridad', 'fechas'], 'ordenes' => ['prioridad', 'antiguos', 'recientes']]) ?>




        <!-- ================================================= -->
        <!-- TABLA DE TICKETS -->
        <!-- ================================================= -->

        <div class="ticket-table" data-region="resultados">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>ID Ticket</th>

                        <th>Solicitante</th>

                        <th>Área Afectada</th>

                        <th>Prioridad (SLA)</th>

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
                            <td>
                                <?= esc($t['solicitante_nombre']) ?>
                            </td>
                            <td>
                                <?= esc($t['area_nombre']) ?>
                            </td>
                            <td>
                                <?= badge_prioridad($t['prioridad_nombre'], (int)$t['sla_minutos']) ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="btn btn-outline-secondary" title="Ver Detalles">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <?php if ($rol === 'Administrador'): ?>
                                    <button class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalAsignar<?= $t['id_ticket'] ?>">
                                        <i class="bi bi-person-check me-1"></i> Asignar
                                    </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Modal Asignar Técnico -->
                                <?php if ($rol === 'Administrador'): ?>
                                <div class="modal fade" id="modalAsignar<?= $t['id_ticket'] ?>" tabindex="-1" aria-hidden="true">
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
                                                    <p class="mb-2"><strong>Asunto:</strong> <?= esc($t['asunto']) ?></p>
                                                    <p class="text-muted small mb-3"><strong>Área:</strong> <?= esc($t['area_nombre']) ?> | <strong>Prioridad:</strong> <?= badge_prioridad($t['prioridad_nombre'], (int)$t['sla_minutos']) ?></p>
                                                    
                                                    <label class="form-label fw-bold">Seleccionar Técnico Encargado <span class="text-danger">*</span></label>
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
                                                    <div class="form-text">Al asignar, el ticket pasará inmediatamente al estado <strong>En Proceso</strong>.</div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success fw-bold">Confirmar Asignación</button>
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
                        <?= view('layout/sin_resultados', ['columnas' => 5, 'mensaje' => 'No hay tickets pendientes en la cola en este momento.']) ?>
                    <?php endif; ?>
                </tbody>

            </table>


            <?= view('layout/paginacion') ?>

        </div>


    </div>
<?= $this->endSection() ?>
