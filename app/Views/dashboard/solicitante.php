<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inicio - HelpDesk TI</title>

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

    </style>

</head>


<body>


<!-- ===================================================== -->
<!-- SIDEBAR -->
<!-- ===================================================== -->

<?= view('layout/sidebar', ['active' => 'inicio']) ?>



<!-- ===================================================== -->
<!-- CONTENIDO PRINCIPAL -->
<!-- ===================================================== -->

<div class="main-content">


    <!-- ================================================= -->
    <!-- HEADER -->
    <!-- ================================================= -->

    <?= view('layout/topbar', ['pageTitle' => 'Inicio']) ?>



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

</div>



<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>