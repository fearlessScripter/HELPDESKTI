<?= $this->extend('layout/principal') ?>
<?php
$this->setVar('menuActivo', 'tickets');
$this->setVar('topbar', ['pageTitle' => 'Mis Tickets']);
?>

<?= $this->section('titulo') ?>Tickets - HelpDesk TI<?= $this->endSection() ?>

<?= $this->section('contenido') ?>



    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->

    <div class="content">


        <!-- PESTAÑAS -->

        <?= view('layout/ticket_tabs', ['activeTab' => 'mis_tickets']) ?>



        <!-- BUSCADOR Y FILTROS -->

        <?= view('layout/filtros_tickets', $rol === 'Solicitante' ? ['campos' => ['estado', 'area', 'fechas'], 'ordenes' => ['recientes', 'antiguos']] : ['campos' => ['estado', 'area', 'prioridad', 'sla', 'fechas']]) ?>




        <!-- ================================================= -->
        <!-- TABLA DE TICKETS -->
        <!-- ================================================= -->

        <div class="ticket-table" data-region="resultados">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>ID Ticket</th>
                        <?php if (isset($rol) && $rol === 'Técnico'): ?>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Prioridad (SLA)</th>
                        <th>Estado</th>
                        <th>Vencimiento SLA</th>
                        <?php else: ?>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <?php endif; ?>
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
                            <?php if (isset($rol) && $rol === 'Técnico'): ?>
                                <td><?= esc($t['solicitante_nombre']) ?></td>
                                <td><?= esc($t['asunto']) ?></td>
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
                                <td>
                                    <?= sla_restante_html($t) ?>
                                </td>
                            <?php else: ?>
                                <td><?= esc($t['asunto']) ?></td>
                                <td><?= esc(fecha_hora($t['fecha_creacion'])) ?></td>
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
                            <?php endif; ?>

                            <td class="text-center">
                                <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="btn btn-sm btn-outline-secondary">Ver Detalles</a>
                                <?php if ((int)$t['id_solicitante'] === (int)$id_usuario && (int)$t['id_estado'] === 3): ?>
                                    <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="btn btn-sm btn-success ms-1 fw-bold">
                                        <i class="bi bi-check2-circle"></i> Confirmar Cierre
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?= view('layout/sin_resultados', ['columnas' => (isset($rol) && $rol === 'Técnico') ? 7 : 5, 'mensaje' => 'Aún no tienes tickets registrados.']) ?>
                    <?php endif; ?>
                </tbody>

            </table>


            <?= view('layout/paginacion') ?>

        </div>


    </div>
<?= $this->endSection() ?>
