<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cola de Tickets - HelpDesk TI</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">HelpDesk TI</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Operaciones (Administrador)</span>
                <a href="<?= base_url('/dashboard') ?>" class="btn btn-sm btn-light text-primary fw-bold">Volver al Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <div class="row mb-3 align-items-center">
            <div class="col">
                <h2 class="fw-bold">Cola Operativa: Tickets Sin Asignar</h2>
                <p class="text-muted">Revisa las incidencias reportadas y asígnalas a los técnicos disponibles.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-hover align-middle mb-0">
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
                        <tr>
                            <td class="fw-bold">#TK-1042</td>
                            <td>Alejandro Silva</td>
                            <td>Redes y Conectividad</td>
                            <td><span class="badge bg-danger">Alta (2h)</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-success fw-bold" onclick="alert('Simulación: Abriendo modal para asignar técnico')">Asignar Técnico</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">#TK-1043</td>
                            <td>Beatriz Fuentes</td>
                            <td>Sistemas ERP</td>
                            <td><span class="badge bg-warning text-dark">Media (4h)</span></td>
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