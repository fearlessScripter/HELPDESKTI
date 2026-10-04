<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - HelpDesk TI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-5 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">HelpDesk TI</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Hola, <strong><?= $nombre ?></strong> (<?= $rol ?>)</span>
                <a href="<?= base_url('/logout') ?>" class="btn btn-sm btn-light text-danger fw-bold">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container text-center">
        <h2 class="fw-bold">Bienvenido al Panel de Control</h2>
        <p class="text-muted mb-5">Selecciona una opción del menú principal según tu rol.</p>

        <div class="row justify-content-center g-4">
            
            <?php if($rol === 'Administrador'): ?>
                <!-- Menú Administrador -->
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column pt-4">
                            <h5 class="fw-bold text-primary">Cola de Tickets</h5>
                            <p class="text-muted small mb-4">Asigna tickets a los técnicos disponibles.</p>
                            <a href="<?= base_url('/tickets/cola') ?>" class="btn btn-primary mt-auto">Ir a la Cola</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column pt-4">
                            <h5 class="fw-bold text-success">Reportes y SLA</h5>
                            <p class="text-muted small mb-4">Genera informes y verifica métricas de atención.</p>
                            <a href="<?= base_url('/reportes') ?>" class="btn btn-success mt-auto">Ver Reportes</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column pt-4">
                            <h5 class="fw-bold text-dark">Gestión de Usuarios</h5>
                            <p class="text-muted small mb-4">Administra accesos y roles del sistema.</p>
                            <a href="<?= base_url('/usuarios') ?>" class="btn btn-dark mt-auto">Gestionar</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($rol === 'Técnico'): ?>
                <!-- Menú Técnico -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column pt-4">
                            <h5 class="fw-bold text-primary">Tickets Asignados</h5>
                            <p class="text-muted small mb-4">Revisa y resuelve los tickets en tu bandeja.</p>
                            <a href="<?= base_url('/tickets/asignados') ?>" class="btn btn-primary mt-auto">Ver Mis Tickets</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($rol === 'Solicitante'): ?>
                <!-- Menú Solicitante -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column pt-4">
                            <h5 class="fw-bold text-success">Registrar Ticket</h5>
                            <p class="text-muted small mb-4">Reporta un nuevo problema o solicitud.</p>
                            <a href="<?= base_url('/tickets/nuevo') ?>" class="btn btn-success mt-auto">Crear Ticket</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column pt-4">
                            <h5 class="fw-bold text-primary">Mis Tickets</h5>
                            <p class="text-muted small mb-4">Revisa el estado de tus solicitudes.</p>
                            <a href="<?= base_url('/tickets/mis-tickets') ?>" class="btn btn-primary mt-auto">Ver Estado</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Base de Conocimientos (Visible para Admin y Técnicos) -->
            <?php if($rol === 'Administrador' || $rol === 'Técnico'): ?>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0 border-bottom border-info border-4">
                        <div class="card-body d-flex flex-column pt-4">
                            <h5 class="fw-bold text-info text-dark">Base de Conocimientos</h5>
                            <p class="text-muted small mb-4">Manuales y soluciones a problemas frecuentes.</p>
                            <a href="<?= base_url('/base-conocimientos') ?>" class="btn btn-outline-info text-dark fw-bold mt-auto">Consultar</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>