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

<div class="sidebar">

    <div class="sidebar-title">
        <i class="bi bi-headset"></i>
        HelpDesk TI
    </div>


    <a href="<?= base_url('/dashboard') ?>" class="active">

        <i class="bi bi-ticket-detailed"></i>

        <span>Tickets</span>

    </a>





    <a href="<?= base_url('/usuarios') ?>">

        <i class="bi bi-people"></i>

        <span>Usuarios</span>

    </a>


    <a href="<?= base_url('/base-conocimientos') ?>">

        <i class="bi bi-book"></i>

        <span>Base de conocimientos</span>

    </a>


    <a href="<?= base_url('/reportes') ?>">

        <i class="bi bi-bar-chart"></i>

        <span>Reportes</span>

    </a>

</div>



<!-- ===================================================== -->
<!-- CONTENIDO PRINCIPAL -->
<!-- ===================================================== -->

<div class="main-content">


    <!-- ================================================= -->
    <!-- HEADER -->
    <!-- ================================================= -->

    <div class="topbar">


        <div class="page-title">

            Tickets

        </div>



        <div class="d-flex align-items-center gap-3">


            <!-- Notificaciones -->

            <button
                type="button"
                class="btn btn-light position-relative">

                <i class="bi bi-bell fs-5"></i>

                <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                    3

                </span>

            </button>



            <!-- Usuario -->

            <div class="dropdown">

                <button
                    class="user-button dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">


                    <div class="text-end">

                        <div class="user-name">

                            <?= esc($nombre) ?>

                        </div>

                        <div class="user-role">

                            <?= esc($rol) ?>

                        </div>

                    </div>


                    <i class="bi bi-person-circle fs-3"></i>


                </button>


                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <span class="dropdown-item-text">

                            <strong><?= esc($nombre) ?></strong>

                            <br>

                            <small class="text-muted">
                                <?= esc($rol) ?>
                            </small>

                        </span>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>

                        <a
                            class="dropdown-item text-danger"
                            href="<?= base_url('/logout') ?>">

                            <i class="bi bi-box-arrow-right"></i>

                            Cerrar sesión

                        </a>

                    </li>

                </ul>

            </div>

        </div>

    </div>



    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->

    <div class="content">


        <!-- PESTAÑAS -->

        <div class="ticket-tabs">

            <a href="<?= base_url('/dashboard') ?>" class="active">

                Todos

            </a>

            <a href="<?= base_url('/tickets/asignados') ?>">

                Asignados

            </a>

            <a href="<?= base_url('/tickets/cola') ?>">

                Sin asignar

            </a>

            <a href="<?= base_url('/tickets/mis-tickets') ?>">

                Mis tickets

            </a>

        </div>



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


            <button
                class="btn btn-outline-secondary">

                <i class="bi bi-funnel"></i>

                Filtros

                <i class="bi bi-chevron-down"></i>

            </button>

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

                        <th>Área Afectada</th>

                        <th>Prioridad (SLA)</th>

                        <th class="text-center">Acción</th>

                    </tr>

                </thead>


                <tbody>


                    <!-- Ticket TK-1042 -->

                    <tr>

                        <td class="fw-bold">
                            #TK-1042
                        </td>

                        <td>
                            Alejandro Silva
                        </td>

                        <td>
                            Redes y Conectividad
                        </td>

                        <td>

                            <span class="badge bg-danger">

                                Alta (2h)

                            </span>

                        </td>

                        <td class="text-center">
                            <button class="btn btn-sm btn-success fw-bold" onclick="alert('Simulación: Abriendo modal para asignar técnico')">Asignar Técnico</button>
                        </td>

                    </tr>


                    <!-- Ticket TK-1043 -->

                    <tr>

                        <td class="fw-bold">
                            #TK-1043
                        </td>

                        <td>
                            Beatriz Fuentes
                        </td>

                        <td>
                            Sistemas ERP
                        </td>

                        <td>

                            <span class="badge bg-warning text-dark">

                                Media (4h)

                            </span>

                        </td>

                        <td class="text-center">
                            <button class="btn btn-sm btn-success fw-bold" onclick="alert('Simulación: Abriendo modal para asignar técnico')">Asignar Técnico</button>
                        </td>

                    </tr>


                </tbody>

            </table>

        </div>


    </div>

</div>



<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>