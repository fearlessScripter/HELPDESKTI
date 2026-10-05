<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - HelpDesk TI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f5f6f8; }
        .sidebar { width: 250px; height: 100vh; position: fixed; left: 0; top: 0; background-color: #212529; color: white; padding: 20px 15px; }
        .sidebar-title { font-size: 22px; font-weight: bold; margin-bottom: 35px; padding-left: 10px; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #ced4da; text-decoration: none; padding: 12px 14px; margin-bottom: 5px; border-radius: 8px; transition: 0.2s; }
        .sidebar a:hover { background-color: #343a40; color: white; }
        .sidebar a.active { background-color: #0d6efd; color: white; }
        .main-content { margin-left: 250px; min-height: 100vh; }
        .topbar { height: 75px; background-color: white; border-bottom: 1px solid #dee2e6; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; }
        .page-title { font-size: 24px; font-weight: 600; }
        .user-button { border: none; background: transparent; display: flex; align-items: center; gap: 10px; padding: 5px 10px; }
        .user-button:hover { background-color: #f1f3f5; border-radius: 8px; }
        .user-name { font-weight: 600; }
        .user-role { font-size: 13px; color: #6c757d; }
        .content { padding: 30px; }
    </style>
</head>
<body>

<?= view('layout/sidebar', ['active' => 'reportes']) ?>

<div class="main-content">
    <?= view('layout/topbar', ['pageTitle' => 'Métricas y Reportes']) ?>

    <div class="content">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fw-bold mb-3">Tickets Creados (Mes)</h6>
                        <h2 class="fw-bold mb-0">124</h2>
                        <span class="text-success small fw-bold"><i class="bi bi-arrow-up"></i> 12% vs mes anterior</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fw-bold mb-3">Tasa de Resolución</h6>
                        <h2 class="fw-bold mb-0 text-success">89%</h2>
                        <span class="text-muted small">Objetivo: 95%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fw-bold mb-3">Tiempo Promedio (SLA)</h6>
                        <h2 class="fw-bold mb-0">3.2h</h2>
                        <span class="text-danger small fw-bold"><i class="bi bi-arrow-up"></i> 0.5h vs mes anterior</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fw-bold mb-3">Satisfacción (CSAT)</h6>
                        <h2 class="fw-bold mb-0 text-primary">4.8/5</h2>
                        <span class="text-muted small">Basado en 85 encuestas</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Tickets por Área Afectada</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Sistemas ERP
                                <span class="badge bg-primary rounded-pill">45</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Redes y Conectividad
                                <span class="badge bg-primary rounded-pill">32</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Soporte TI (Equipos)
                                <span class="badge bg-primary rounded-pill">28</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Telefonía IP
                                <span class="badge bg-primary rounded-pill">19</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Desempeño de Técnicos</h5>
                        <table class="table table-sm table-borderless align-middle">
                            <thead class="text-muted">
                                <tr>
                                    <th>Técnico</th>
                                    <th class="text-center">Resueltos</th>
                                    <th class="text-center">SLA Cumplido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Luis Técnico</td>
                                    <td class="text-center">45</td>
                                    <td class="text-center"><span class="text-success fw-bold">98%</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">María Soporte</td>
                                    <td class="text-center">38</td>
                                    <td class="text-center"><span class="text-warning text-dark fw-bold">85%</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jorge Redes</td>
                                    <td class="text-center">26</td>
                                    <td class="text-center"><span class="text-success fw-bold">92%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
