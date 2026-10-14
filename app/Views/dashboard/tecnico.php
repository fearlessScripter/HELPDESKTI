<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Técnico TI</title>
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
        .ticket-count { margin-left: auto; background-color: #dc3545; color: white; font-size: 12px; padding: 2px 7px; border-radius: 10px; }
        
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
</head>

<body>

<!-- SIDEBAR -->
<?= view('layout/sidebar', ['active' => 'inicio']) ?>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content">

    <!-- HEADER -->
    <?= view('layout/topbar', ['pageTitle' => 'Inicio Técnico']) ?>

    <!-- CONTENIDO -->
    <div class="content">

        <div class="mb-4">
            <h2 class="fw-bold">Hola, <?= esc($nombre) ?> (<?= esc($rol) ?>)</h2>
            <p class="text-muted fs-5">Resumen de tu trabajo asignado</p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Asignados -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #0dcaf0 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Tickets Asignados</h6>
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-gear"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['en_proceso'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>

            <!-- Resueltos -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #198754 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted text-uppercase fw-bold mb-0">Tickets Resueltos</h6>
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold display-5 mb-0"><?= esc($metricas['resueltos'] ?? 0) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mb-3 fw-semibold">Mis Tickets Asignados En Proceso</h4>
        <div class="ticket-table">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Ticket</th>
                        <th>Solicitante</th>
                        <th>Asunto</th>
                        <th>SLA (Prioridad)</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($misTickets)): ?>
                        <?php foreach ($misTickets as $mt): ?>
                        <tr>
                            <td class="fw-bold">#<?= esc($mt['codigo_ticket'] ?? 'TK-' . $mt['id_ticket']) ?></td>
                            <td><?= esc($mt['solicitante_nombre']) ?></td>
                            <td><?= esc($mt['asunto']) ?></td>
                            <td>
                                <?php if ($mt['prioridad_nombre'] === 'Alta'): ?>
                                    <span class="badge bg-danger">Alta (<?= formato_minutos((int)$mt['sla_minutos']) ?>)</span>
                                <?php elseif ($mt['prioridad_nombre'] === 'Media'): ?>
                                    <span class="badge bg-warning text-dark">Media (<?= formato_minutos((int)$mt['sla_minutos']) ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">Baja (<?= formato_minutos((int)$mt['sla_minutos']) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-warning text-dark"><?= esc($mt['estado_nombre']) ?></span></td>
                            <td class="text-center">
                                <a href="<?= base_url('/tickets/detalles/' . $mt['id_ticket']) ?>" class="btn btn-sm btn-primary fw-bold">Atender</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No tienes tickets asignados en proceso en este momento.</td>
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