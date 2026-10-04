<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets - HelpDesk TI</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">HelpDesk TI</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Portal del Solicitante</span>
                <a href="<?= base_url('/dashboard') ?>" class="btn btn-sm btn-light text-primary fw-bold">Volver al Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <div class="row mb-3 align-items-center">
            <div class="col">
                <h2 class="fw-bold">Mis Tickets Reportados</h2>
                <p class="text-muted">Revisa el estado de tus solicitudes de soporte.</p>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/tickets/nuevo') ?>" class="btn btn-primary fw-bold">+ Nuevo Ticket</a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID Ticket</th>
                            <th>Asunto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">#TK-1050</td>
                            <td>No enciende el computador</td>
                            <td>04/10/2026</td>
                            <td><span class="badge bg-warning text-dark">En Proceso</span></td>
                            <td class="text-center"><button class="btn btn-sm btn-outline-secondary">Ver Detalles</button></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">#TK-1021</td>
                            <td>Fallo en conexión WiFi</td>
                            <td>01/10/2026</td>
                            <td><span class="badge bg-success">Resuelto</span></td>
                            <td class="text-center"><button class="btn btn-sm btn-outline-secondary">Ver Detalles</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>