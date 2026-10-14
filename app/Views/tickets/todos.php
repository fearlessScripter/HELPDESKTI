<!DOCTYPE html>
<html lang="es">

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

    <?= view('layout/topbar', ['pageTitle' => 'Inicio (Todos los Tickets)']) ?>



    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->

    <div class="content">


        <!-- PESTAÑAS -->

        <?= view('layout/ticket_tabs', ['activeTab' => 'todos']) ?>



        <!-- BUSCADOR Y FILTROS -->

        <div class="d-flex justify-content-between align-items-center mb-4">


            <div class="input-group" style="max-width: 450px;">

                <span class="input-group-text bg-white">

                    <i class="bi bi-search"></i>

                </span>

                <input
                    type="text"
                    class="form-control"
                    placeholder="Buscar tickets...">

            </div>


            <div class="d-flex gap-2">
                <button
                    class="btn btn-outline-secondary">
                    <i class="bi bi-funnel"></i>
                    Filtros
                    <i class="bi bi-chevron-down"></i>
                </button>
                <?php if (isset($rol) && $rol !== 'Técnico'): ?>
                <a href="<?= base_url('/tickets/nuevo') ?>" class="btn btn-primary fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Ticket
                </a>
                <?php endif; ?>
            </div>
        </div>



        <!-- ================================================= -->
        <!-- TABLA DE TICKETS -->
        <!-- ================================================= -->

        <div class="ticket-table">
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
                <tbody>
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
                                <?php if ($t['prioridad_nombre'] === 'Alta'): ?>
                                    <span class="badge bg-danger">Alta (<?= formato_minutos((int)$t['sla_minutos']) ?>)</span>
                                <?php elseif ($t['prioridad_nombre'] === 'Media'): ?>
                                    <span class="badge bg-warning text-dark">Media (<?= formato_minutos((int)$t['sla_minutos']) ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">Baja (<?= formato_minutos((int)$t['sla_minutos']) ?>)</span>
                                <?php endif; ?>
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
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No hay tickets registrados en el sistema.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>


    </div>

</div>



<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>