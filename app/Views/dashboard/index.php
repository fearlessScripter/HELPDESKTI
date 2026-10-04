<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - HelpDesk TI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">HelpDesk TI</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-block">Hola, <strong><?= esc($nombre) ?></strong> (<?= esc($rol) ?>)</span>
                <a href="<?= base_url('/logout') ?>" class="btn btn-light btn-sm text-danger fw-bold">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">Bienvenido al Panel de Control</h2>
                <p class="text-muted">Selecciona una opción del menú principal según tu rol.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <!-- Opciones exclusivas según el rol -->
            <?php if($rol == 'Solicitante'): ?>
                <div class="col-md-5 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold">Registrar Nuevo Ticket</h5>
                            <p class="card-text text-muted">Abre una nueva incidencia para ser atendida por soporte.</p>
                            <a href="<?= base_url('/tickets/nuevo') ?>" class="btn btn-primary mt-2 w-100">Crear Ticket</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold">Mis Tickets</h5>
                            <p class="card-text text-muted">Revisa el estado de los tickets que has reportado.</p>
                            <a href="<?= base_url('/tickets/mis-tickets') ?>" class="btn btn-outline-primary mt-2 w-100">Ver Mis Tickets</a>
                        </div>
                    </div>
                </div>
                
            <?php elseif($rol == 'Técnico'): ?>
                <div class="col-md-5 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold">Tickets Asignados</h5>
                            <p class="card-text text-muted">Revisa y resuelve los tickets que te han sido asignados.</p>
                            <a href="<?= base_url('/tickets/asignados') ?>" class="btn btn-primary mt-2 w-100">Ver Asignados</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold">Base de Conocimientos</h5>
                            <p class="card-text text-muted">Consulta soluciones a problemas comunes.</p>
                            <a href="<?= base_url('/base-conocimientos') ?>" class="btn btn-outline-primary mt-2 w-100">Acceder</a>
                        </div>
                    </div>
                </div>
                
            <?php elseif($rol == 'Administrador'): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold text-primary">Cola de Tickets</h5>
                            <p class="card-text text-muted">Asigna tickets a los técnicos disponibles.</p>
                            <a href="<?= base_url('/tickets/cola') ?>" class="btn btn-primary mt-2 w-100">Ir a la Cola</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold text-success">Reportes y SLA</h5>
                            <p class="card-text text-muted">Genera informes y verifica métricas de atención.</p>
                            <a href="<?= base_url('/reportes') ?>" class="btn btn-success mt-2 w-100">Ver Reportes</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold text-dark">Gestión de Usuarios</h5>
                            <p class="card-text text-muted">Administra accesos y roles del sistema.</p>
                            <a href="<?= base_url('/usuarios') ?>" class="btn btn-dark mt-2 w-100">Gestionar</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>