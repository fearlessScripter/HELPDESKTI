<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ticket - HelpDesk TI</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f5f6f8;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #212529;
            color: white;
            padding: 20px 15px;
        }
        .sidebar-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 35px;
            padding-left: 10px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ced4da;
            text-decoration: none;
            padding: 12px 14px;
            margin-bottom: 5px;
            border-radius: 8px;
            transition: 0.2s;
        }
        .sidebar a:hover {
            background-color: #343a40;
            color: white;
        }
        .sidebar a.active {
            background-color: #0d6efd;
            color: white;
        }
        .ticket-count {
            margin-left: auto;
            background-color: #dc3545;
            color: white;
            font-size: 12px;
            padding: 2px 7px;
            border-radius: 10px;
        }
        /* Contenido */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }
        /* Header */
        .topbar {
            height: 75px;
            background-color: white;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }
        .page-title {
            font-size: 24px;
            font-weight: 600;
        }
        /* Usuario */
        .user-button {
            border: none;
            background: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px;
        }
        .user-button:hover {
            background-color: #f1f3f5;
            border-radius: 8px;
        }
        .user-name {
            font-weight: 600;
        }
        .user-role {
            font-size: 13px;
            color: #6c757d;
        }
        /* Contenido */
        .content {
            padding: 30px;
        }
    </style>
</head>
<body>

<!-- ===================================================== -->
<!-- SIDEBAR -->
<!-- ===================================================== -->
<?= view('layout/sidebar', ['active' => 'tickets']) ?>

<!-- ===================================================== -->
<!-- CONTENIDO PRINCIPAL -->
<!-- ===================================================== -->
<div class="main-content">

    <!-- ================================================= -->
    <!-- HEADER -->
    <!-- ================================================= -->
    <?= view('layout/topbar', ['pageTitle' => 'Nuevo Ticket']) ?>

    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->
    <div class="content">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 mt-2">
                        <div class="card-header bg-white pt-4 pb-3 border-bottom-0 text-center">
                            <h3 class="fw-bold mb-1">Registrar Nuevo Ticket</h3>
                            <p class="text-muted mb-0">Por favor, detalla tu problema para que el equipo técnico pueda ayudarte.</p>
                        </div>
                        <div class="card-body p-4">
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

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nivel de Impacto (Alcance del problema) <span class="text-danger">*</span></label>
                                        <select class="form-select" required>
                                            <option value="" selected disabled>Selecciona el impacto...</option>
                                            <option value="bajo">Bajo (Afecta solo a mí)</option>
                                            <option value="medio">Medio (Afecta a mi departamento)</option>
                                            <option value="alto">Alto (Afecta a toda la empresa)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nivel de Urgencia (Rapidez necesaria) <span class="text-danger">*</span></label>
                                        <select class="form-select" required>
                                            <option value="" selected disabled>Selecciona la urgencia...</option>
                                            <option value="baja">Baja (Puedo seguir trabajando)</option>
                                            <option value="media">Media (Dificulta mi trabajo)</option>
                                            <option value="alta">Alta (Trabajo totalmente detenido)</option>
                                        </select>
                                    </div>
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
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
