<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= tema_bootstrap() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Administrador TI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f5f6f8; }
        /* Sidebar */
        .sidebar { width: 250px; height: 100vh; position: fixed; left: 0; top: 0; background-color: #212529; color: white; padding: 20px 15px; }
        .sidebar-title { font-size: 22px; font-weight: bold; margin-bottom: 35px; padding-left: 10px; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #ced4da; text-decoration: none; padding: 12px 14px; margin-bottom: 5px; border-radius: 8px; transition: 0.2s; }
        .sidebar a:hover { background-color: #343a40; color: white; }
        .sidebar a.active { background-color: #0d6efd; color: white; }
        
        /* Contenido */
        .main-content { margin-left: 250px; min-height: 100vh; }
        
        /* Header */
        .topbar { height: 75px; background-color: white; border-bottom: 1px solid #dee2e6; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; }
        .page-title { font-size: 24px; font-weight: 600; }
        
        /* Usuario */
        .user-button { border: none; background: transparent; display: flex; align-items: center; gap: 10px; padding: 5px 10px; }
        .user-button:hover { background-color: #f1f3f5; border-radius: 8px; }
        .user-name { font-weight: 600; }
        .user-role { font-size: 13px; color: #6c757d; }
        
        .content { padding: 30px; }
        
        .ticket-table { background-color: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .ticket-table th { font-size: 13px; color: #6c757d; font-weight: 600; }
    </style>
    <?= view('layout/tema') ?>
</head>

<body>

<!-- SIDEBAR -->
<?= view('layout/sidebar', ['active' => 'inicio']) ?>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content">

    <!-- HEADER -->
    <?= view('layout/topbar', ['pageTitle' => 'Inicio Administrador']) ?>

    <!-- CONTENIDO -->
    <div class="content">

        <div class="mb-4">
            <h2 class="fw-bold">Hola, <?= esc($nombre) ?> (<?= esc($rol) ?>)</h2>
            <p class="text-muted fs-5">Resumen global del sistema HelpDesk</p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Totales -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0d6efd !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Total Tickets</h6>
                            <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-list-task fs-5"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['total'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Sin Asignar -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #dc3545 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Sin Asignar (Pendientes)</h6>
                            <div class="bg-danger bg-opacity-10 text-danger rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-exclamation-triangle fs-5"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['pendientes'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>

            <!-- En Proceso -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #ffc107 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">En Proceso</h6>
                            <div class="bg-warning bg-opacity-10 text-warning rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-gear fs-5"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['en_proceso'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Resueltos -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #198754 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Resueltos</h6>
                            <div class="bg-success bg-opacity-10 text-success rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-check-circle fs-5"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['resueltos'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 fw-semibold">Tickets Recientes en el Sistema</h4>
            <a href="<?= base_url('/tickets/todos') ?>" class="btn btn-sm btn-outline-primary">Ver Todos</a>
        </div>
        
        <div class="ticket-table">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Ticket</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>Área</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ticketsRecientes)): ?>
                        <?php foreach ($ticketsRecientes as $tr): ?>
                        <tr>
                            <td class="fw-bold">#<?= esc($tr['codigo_ticket'] ?? 'TK-' . $tr['id_ticket']) ?></td>
                            <td><?= esc($tr['solicitante_nombre']) ?></td>
                            <td><?= esc($tr['asunto']) ?></td>
                            <td><?= esc($tr['area_nombre']) ?></td>
                            <td>
                                <?= badge_prioridad($tr['prioridad_nombre'], (int)$tr['sla_minutos']) ?>
                            </td>
                            <td>
                                <?php if ($tr['estado_nombre'] === 'Pendiente'): ?>
                                    <span class="badge bg-danger">Pendiente</span>
                                <?php elseif ($tr['estado_nombre'] === 'En Proceso'): ?>
                                    <span class="badge bg-warning text-dark">En Proceso</span>
                                <?php elseif ($tr['estado_nombre'] === 'Resuelto'): ?>
                                    <span class="badge bg-success">Resuelto</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Cerrado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('/tickets/detalles/' . $tr['id_ticket']) ?>" class="btn btn-sm btn-primary fw-bold">Ver Ticket</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No hay tickets registrados aún.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>