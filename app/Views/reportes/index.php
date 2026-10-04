<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y SLA - HelpDesk TI</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar igual a la del Dashboard -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">HelpDesk TI</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Módulo de Reportes</span>
                <a href="<?= base_url('/dashboard') ?>" class="btn btn-sm btn-light text-primary fw-bold">Volver al Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <!-- Cabecera -->
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h2 class="fw-bold">Reportes y Cumplimiento SLA</h2>
                <p class="text-muted">Indicadores de rendimiento del equipo técnico durante el mes actual.</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-danger" onclick="alert('Simulación: Generando archivo PDF...')">
                    📄 Exportar a PDF
                </button>
            </div>
        </div>

        <!-- Tarjetas de Métricas (KPIs) -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted fw-bold">TICKETS RESUELTOS</h6>
                        <h3 class="fw-bold mb-0">142</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted fw-bold">CUMPLIMIENTO SLA</h6>
                        <h3 class="fw-bold mb-0 text-success">94.5%</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="text-muted fw-bold">TIEMPO MEDIO RESPUESTA</h6>
                        <h3 class="fw-bold mb-0">1h 45m</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Detalles -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3">
                Desglose por Técnico
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Técnico Asignado</th>
                            <th>Tickets Asignados</th>
                            <th>Resueltos a Tiempo</th>
                            <th>Infracciones SLA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Luis Técnico</td>
                            <td>45</td>
                            <td>43</td>
                            <td><span class="badge bg-danger">2</span></td>
                        </tr>
                        <tr>
                            <td>Miguel Soporte</td>
                            <td>52</td>
                            <td>52</td>
                            <td><span class="badge bg-success">0</span></td>
                        </tr>
                        <tr>
                            <td>Andrea Redes</td>
                            <td>45</td>
                            <td>39</td>
                            <td><span class="badge bg-warning text-dark">6</span></td>
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