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


    <a href="<?= base_url('/dashboard') ?>">

        <i class="bi bi-ticket-detailed"></i>

        <span>Tickets</span>

    </a>





    <a href="<?= base_url('/usuarios') ?>">

        <i class="bi bi-people"></i>

        <span>Usuarios</span>

    </a>


    <a href="<?= base_url('/base-conocimientos') ?>" class="active">

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
            Documentación Técnica
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

                            Cerrar sesiÃ³n

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
<div class="container-fluid p-0">
<div class="row justify-content-center mb-4">
    <div class="col-md-8 text-center">
        <h2 class="fw-bold">Base de Conocimientos</h2>
        <p class="text-muted">Encuentra soluciones rápidas a problemas comunes (Procedimientos Estándar de Operación).</p>
        <div class="input-group mt-3 shadow-sm">
            <input type="text" class="form-control form-control-lg" placeholder="Buscar por error, software o hardware...">
            <button class="btn btn-primary px-4" type="button">Buscar</button>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="accordion shadow-sm" id="accordionConocimientos">
            <!-- Articulo 1 -->
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#colapsouno">
                        PEO-01: Pasos para restablecer credenciales del ERP
                    </button>
                </h2>
                <div id="colapsouno" class="accordion-collapse collapse show" data-bs-parent="#accordionConocimientos">
                    <div class="accordion-body text-muted">
                        <strong>1.</strong> Ingresar al panel de administración de Active Directory.<br>
                        <strong>2.</strong> Buscar el correo del usuario solicitante.<br>
                        <strong>3.</strong> Hacer clic derecho > Restablecer contraseña y marcar la opción "El usuario debe cambiar la contraseña en el siguiente inicio de sesión".
                    </div>
                </div>
            </div>
            <!-- Articulo 2 -->
            <div class="accordion-item border-0 mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#colapsodos">
                        PEO-02: Solución a pantalla azul (Error código 0x0000007B)
                    </button>
                </h2>
                <div id="colapsodos" class="accordion-collapse collapse" data-bs-parent="#accordionConocimientos">
                    <div class="accordion-body text-muted">
                        Este error indica un problema con el controlador de almacenamiento SATA. Ingresa a la BIOS del equipo (presionando F2 o DEL), ve a la pestaña "Advanced", cambia la configuración SATA de IDE a AHCI y guarda los cambios.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

