<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Asignados - HelpDesk TI</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">HelpDesk TI</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Portal del Técnico</span>
                <a href="<?= base_url('/dashboard') ?>" class="btn btn-sm btn-light text-primary fw-bold">Volver al Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <div class="row mb-3 align-items-center">
            <div class="col">
                <h2 class="fw-bold">Mis Tickets Asignados</h2>
                <p class="text-muted">Gestiona y resuelve las incidencias operativas.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Solicitante</th>
                            <th>Asunto</th>
                            <th>Prioridad (SLA)</th>
                            <th>Estado</th>
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
                            <td class="text-center"><button class="btn btn-sm btn-primary fw-bold">Atender</button></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">#TK-1048</td>
                            <td>Carlos Ramos</td>
                            <td>Configuración de impresora</td>
                            <td><span class="badge bg-info text-dark">Baja (24h)</span></td>
                            <td><span class="badge bg-warning text-dark">En Proceso</span></td>
                            <td class="text-center"><button class="btn btn-sm btn-primary fw-bold">Atender</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>