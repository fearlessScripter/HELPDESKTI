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

    <?= view('layout/topbar', ['pageTitle' => 'Tickets Asignados']) ?>



    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->

    <div class="content">


        <!-- PESTAÑAS -->

        <?= view('layout/ticket_tabs', ['activeTab' => 'asignados']) ?>



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

                        <th>ID</th>

                        <th>Solicitante</th>

                        <th>Asunto</th>

                        <th>Prioridad (SLA)</th>

                        <th>Estado</th>
                        <th>Vencimiento SLA</th>

                        <th class="text-center">Acción</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td class="fw-bold">#TK-1045</td>

                        <td>Ana Usuario</td>

                        <td>Error al abrir ERP</td>

                        <td><span class="badge bg-danger">Alta (2h)</span></td>

                        <td><span class="badge bg-danger">Pendiente</span></td>
                        <td><span class="text-danger fw-bold"><i class="bi bi-clock-history"></i> Quedan 30 min</span></td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <?php if (isset($rol) && $rol === 'Técnico'): ?>
                                <button class="btn btn-sm btn-primary fw-bold">Atender</button>
                                <?php endif; ?>
                                <?php if (isset($rol) && $rol === 'Administrador'): ?>
                                <button class="btn btn-sm btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#reasignarModal">Reasignar</button>
                                <?php endif; ?>
                            </div>
                        </td>

                    </tr>

                    <tr>

                        <td class="fw-bold">#TK-1048</td>

                        <td>Carlos Ramos</td>

                        <td>Configuración de impresora</td>

                        <td><span class="badge bg-info text-dark">Baja (24h)</span></td>

                        <td><span class="badge bg-warning text-dark">En Proceso</span></td>
                        <td><span class="text-success"><i class="bi bi-clock"></i> Quedan 22 hrs</span></td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <?php if (isset($rol) && $rol === 'Técnico'): ?>
                                <button class="btn btn-sm btn-primary fw-bold">Atender</button>
                                <?php endif; ?>
                                <?php if (isset($rol) && $rol === 'Administrador'): ?>
                                <button class="btn btn-sm btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#reasignarModal">Reasignar</button>
                                <?php endif; ?>
                            </div>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


    </div>

</div>



<!-- Modal Reasignar Ticket -->
<div class="modal fade" id="reasignarModal" tabindex="-1" aria-labelledby="reasignarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="reasignarModalLabel">Reasignar / Escalar Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reasignarForm">
                    <div class="mb-3">
                        <label for="tecnicoSelect" class="form-label text-muted fw-bold">Asignar a:</label>
                        <select class="form-select" id="tecnicoSelect" required>
                            <option value="" selected disabled>Seleccione un Técnico / Administrador...</option>
                            <option value="1">Luis Técnico (Técnico N1)</option>
                            <option value="2">María Soporte (Técnico N2)</option>
                            <option value="3">Jorge Redes (Especialista Redes)</option>
                            <option value="4">Admin Principal (Administrador)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="motivoReasignacion" class="form-label text-muted fw-bold">Motivo de Reasignación</label>
                        <textarea class="form-control" id="motivoReasignacion" rows="3" placeholder="Ej: Requiere escalamiento a nivel 2 por complejidad..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning fw-bold text-dark" onclick="alert('Simulación: Ticket reasignado exitosamente.')" data-bs-dismiss="modal">Confirmar Reasignación</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>