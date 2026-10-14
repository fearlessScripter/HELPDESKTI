<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= tema_bootstrap() ?>">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tickets - HelpDesk TI</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        body {
            background-color: #f5f6f8;
        }

        /* Sidebar */

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #212529;
            color: white;
            padding: 20px 15px;
        }

        .sidebar-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 35px;
            padding-left: 10px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;

            color: #ced4da;
            text-decoration: none;

            padding: 12px 14px;
            margin-bottom: 5px;

            border-radius: 8px;

            transition: 0.2s;
        }

        .sidebar a:hover {
            background-color: #343a40;
            color: white;
        }

        .sidebar a.active {
            background-color: #0d6efd;
            color: white;
        }

        .ticket-count {
            margin-left: auto;
            background-color: #dc3545;
            color: white;

            font-size: 12px;

            padding: 2px 7px;
            border-radius: 10px;
        }


        /* Contenido */

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }


        /* Header */

        .topbar {
            height: 75px;

            background-color: white;

            border-bottom: 1px solid #dee2e6;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 30px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
        }


        /* Usuario */

        .user-button {
            border: none;
            background: transparent;

            display: flex;
            align-items: center;
            gap: 10px;

            padding: 5px 10px;
        }

        .user-button:hover {
            background-color: #f1f3f5;
            border-radius: 8px;
        }

        .user-name {
            font-weight: 600;
        }

        .user-role {
            font-size: 13px;
            color: #6c757d;
        }


        /* Contenido */

        .content {
            padding: 30px;
        }


        /* Tabs */

        .ticket-tabs {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 25px;
        }

        .ticket-tabs a {
            text-decoration: none;
            color: #6c757d;

            padding: 12px 18px;

            display: inline-block;
        }

        .ticket-tabs a:hover {
            color: #0d6efd;
        }

        .ticket-tabs a.active {
            color: #0d6efd;

            border-bottom: 2px solid #0d6efd;

            font-weight: 600;
        }


        /* Tabla */

        .ticket-table {
            background-color: white;

            border-radius: 10px;

            overflow: hidden;

            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .ticket-table table {
            margin-bottom: 0;
        }

        .ticket-table th {
            font-size: 13px;
            color: #6c757d;
            font-weight: 600;
        }

        .ticket-table td {
            vertical-align: middle;
        }


        /* Prioridades */

        .priority-high {
            color: #dc3545;
            font-weight: 600;
        }

        .priority-medium {
            color: #fd7e14;
            font-weight: 600;
        }

        .priority-low {
            color: #198754;
            font-weight: 600;
        }


        /* Estados */

        .status {
            padding: 5px 10px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-process {
            background-color: #cff4fc;
            color: #055160;
        }

        .status-resolved {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-closed {
            background-color: #e2e3e5;
            color: #41464b;
        }

    </style>

    <?= view('layout/tema') ?>
</head>


<body>


<!-- ===================================================== -->
<!-- SIDEBAR -->
<!-- ===================================================== -->

<?= view('layout/sidebar', ['active' => 'tickets']) ?>



<!-- ===================================================== -->
<!-- CONTENIDO PRINCIPAL -->
<!-- ===================================================== -->

<div class="main-content">


    <!-- ================================================= -->
    <!-- HEADER -->
    <!-- ================================================= -->

    <?= view('layout/topbar', ['pageTitle' => 'Tickets Asignados']) ?>



    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->

    <div class="content">


        <!-- PESTAÑAS -->

        <?= view('layout/ticket_tabs', ['activeTab' => 'asignados']) ?>



        <!-- BUSCADOR Y FILTROS -->

        <?= view('layout/filtros_tickets', ['campos' => $rol === 'Administrador' ? ['area', 'prioridad', 'tecnico', 'sla', 'fechas'] : ['area', 'prioridad', 'sla', 'fechas'], 'ordenes' => ['sla', 'prioridad', 'recientes', 'antiguos']]) ?>




        <!-- ================================================= -->
        <!-- TABLA DE TICKETS -->
        <!-- ================================================= -->

        <div class="ticket-table" data-region="resultados">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>ID</th>

                        <th>Solicitante</th>

                        <th>Asunto</th>

                        <th>Prioridad (SLA)</th>

                        <th>Estado</th>
                        <th>Vencimiento SLA</th>

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
                            <td>
                                <?= badge_prioridad($t['prioridad_nombre'], (int)$t['sla_minutos']) ?>
                            </td>
                            <td><span class="badge bg-warning text-dark">En Proceso</span></td>
                            <td>
                                <?= sla_restante_html($t) ?>
                            </td>
                            <td class="text-center">
                                <?php if (isset($rol) && $rol === 'Administrador'): ?>
                                <!-- El admin supervisa y reasigna; la solución la registra el técnico -->
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="btn btn-outline-secondary" title="Ver Detalles">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <button class="btn btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#reasignarModal<?= $t['id_ticket'] ?>">
                                        <i class="bi bi-arrow-left-right me-1"></i> Reasignar
                                    </button>
                                </div>
                                <?php else: ?>
                                <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="btn btn-sm btn-primary fw-bold">
                                    <i class="bi bi-tools me-1"></i> Atender
                                </a>
                                <?php endif; ?>

                                <?php if (isset($rol) && $rol === 'Administrador'): ?>
                                <div class="modal fade" id="reasignarModal<?= $t['id_ticket'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog text-start">
                                        <div class="modal-content">
                                            <form action="<?= base_url('/tickets/reasignar') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id_ticket" value="<?= $t['id_ticket'] ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Reasignar Ticket #<?= esc($t['codigo_ticket']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted fw-bold">Asignar a nuevo técnico:</label>
                                                        <select class="form-select" name="id_tecnico" required>
                                                            <option value="" selected disabled>Seleccione un Técnico...</option>
                                                            <?php if (!empty($tecnicos)): ?>
                                                                <?php foreach ($tecnicos as $tec): ?>
                                                                    <option value="<?= esc($tec['id_usuario']) ?>" <?= ((int)($t['id_tecnico_asignado'] ?? 0) === (int)$tec['id_usuario']) ? 'selected' : '' ?>>
                                                                        <?= esc($tec['nombre_completo']) ?> (<?= esc($tec['area'] ?? 'Soporte TI') ?>)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted fw-bold">Motivo de Reasignación</label>
                                                        <textarea class="form-control" name="motivo" rows="3" placeholder="Ej: Especialista requerido para resolver la falla..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-warning fw-bold text-dark">Confirmar Reasignación</button>
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
                        <?= view('layout/sin_resultados', ['columnas' => 7, 'mensaje' => 'No hay tickets asignados en proceso.']) ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?= view('layout/paginacion') ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>