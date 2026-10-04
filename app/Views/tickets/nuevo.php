<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ticket - HelpDesk TI</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mt-2">
                    <div class="card-header bg-white pt-4 pb-3 border-bottom-0 text-center">
                        <h3 class="fw-bold mb-1">Registrar Nuevo Ticket</h3>
                        <p class="text-muted mb-0">Por favor, detalla tu problema para que el equipo técnico pueda ayudarte.</p>
                    </div>
                    <div class="card-body p-4">
                        <!-- Simulación de formulario -->
                        <form action="#" method="get" onsubmit="alert('Simulación: ¡Ticket registrado con éxito! Redirigiendo a Mis Tickets...'); return false;">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Área Afectada <span class="text-danger">*</span></label>
                                <select class="form-select" required>
                                    <option value="" selected disabled>Selecciona el área del problema...</option>
                                    <option value="1">Soporte TI (Equipos y Hardware)</option>
                                    <option value="2">Redes y Conectividad (Internet/WiFi)</option>
                                    <option value="3">Sistemas ERP (Cuentas y Software)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Asunto breve <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Ej: No me enciende el computador o no tengo internet" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Descripción detallada del problema <span class="text-danger">*</span></label>
                                <textarea class="form-control" rows="5" placeholder="Describe qué estabas haciendo y qué error apareció en pantalla..." required></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">Enviar Ticket de Soporte</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>