<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Ticket - HelpDesk TI</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
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
    </style>
</head>
<body>

<?= view('layout/sidebar', ['active' => 'tickets']) ?>

<div class="main-content">

    <?= view('layout/topbar', ['pageTitle' => 'Detalles del Ticket ' . esc($ticket_id ?? '#TK-1050')]) ?>

    <div class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <!-- Columna Izquierda: Detalles del Ticket -->
                <div class="col-md-7">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white pt-4 pb-3 border-bottom text-start">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="fw-bold mb-0">No enciende el computador</h4>
                                <?php if (isset($_GET['id']) && $_GET['id'] === 'TK-1021'): ?>
                                <span class="badge bg-success fs-6">Resuelto</span>
                                <?php else: ?>
                                <span class="badge bg-warning text-dark fs-6">En Proceso</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-muted mb-0"><i class="bi bi-clock"></i> Creado el 04/10/2026 por <strong>Ana Usuario</strong></p>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Descripción del Problema:</h6>
                            <p class="text-secondary" style="line-height: 1.6;">
                                Hoy en la mañana llegué a la oficina y el computador no quiere dar imagen. La luz del botón de encendido prende y los ventiladores suenan, pero la pantalla se queda completamente en negro y dice "No Signal". Ya revisé los cables de atrás.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Información Adicional y Solución -->
                <div class="col-md-5">
                    <div class="card shadow-sm border-0 mb-4 bg-light">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Información del Ticket</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><strong>ID del Ticket:</strong> <?= esc($ticket_id ?? '#TK-1050') ?></li>
                                <li class="mb-2"><strong>Área Afectada:</strong> Soporte TI (Equipos y Hardware)</li>
                                <li class="mb-2"><strong>Prioridad:</strong> <span class="badge bg-danger">Alta (2h SLA)</span></li>
                                <li class="mb-2"><strong>Asignado a:</strong> Luis Técnico</li>
                            </ul>
                            
                            <?php if ($rol === 'Técnico' || $rol === 'Administrador'): ?>
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold mb-2">Control de SLA</h6>
                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                    <span>Tiempo restante: 30 min de 2 hrs</span>
                                    <span>75% consumido</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Formulario de Solución (Para el Técnico) -->
                    <?php if ($rol === 'Técnico'): ?>
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white pt-3 pb-2 border-bottom text-start">
                            <h6 class="fw-bold mb-0 text-success"><i class="bi bi-tools me-2"></i>Aplicar Solución</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="#" method="get" onsubmit="alert('Simulación: Solución registrada. El ticket cambiará a estado Resuelto.'); return false;">
                                <small class="text-muted mb-3 d-block"><i class="bi bi-info-circle"></i> El tiempo de resolución (OLA) se calculará y registrará automáticamente utilizando las marcas de tiempo del sistema al enviar este formulario.</small>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Detalle la solución aplicada <span class="text-danger">*</span></label>
                                    <textarea class="form-control" rows="4" placeholder="Ej: Se reemplazó el cable de poder y se reajustó la memoria RAM..." required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success fw-bold">
                                        <i class="bi bi-check-circle me-1"></i> Marcar como Resuelto
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Estado de la Solución (Para el Admin o Solicitante) -->
                    <?php if ($rol !== 'Técnico'): ?>
                        <?php if (isset($_GET['id']) && $_GET['id'] === 'TK-1021'): ?>
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white pt-3 pb-2 border-bottom text-start">
                                    <h6 class="fw-bold mb-0 text-success"><i class="bi bi-check-circle-fill me-2"></i>Validación de Solución</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="alert alert-success border-0 mb-4 shadow-sm">
                                        <h6 class="fw-bold mb-2">Solución aplicada por el técnico:</h6>
                                        <p class="mb-0 small">Se reinició el router principal y se actualizó el firmware para restablecer la conexión WiFi de tu área.</p>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-success fw-bold" onclick="alert('Simulación: Ticket cerrado exitosamente.')">
                                            <i class="bi bi-check2-circle me-1"></i> Confirmar y cerrar ticket
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info border-0 shadow-sm">
                                <i class="bi bi-info-circle-fill me-2"></i> El ticket está en proceso de atención por el equipo técnico.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>
            
            <div class="mt-2">
                <a href="<?= base_url('/tickets/mis-tickets') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a Tickets
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
