<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Conocimientos - HelpDesk TI</title>
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

<?= view('layout/sidebar', ['active' => 'base_conocimientos']) ?>

<div class="main-content">
    <?= view('layout/topbar', ['pageTitle' => 'Documentación Técnica']) ?>

    <div class="content">
        <div class="container-fluid p-0">
            <div class="row justify-content-center mb-4">
                <div class="col-md-8 text-center">
                    <h2 class="fw-bold">Base de Conocimientos</h2>
                    <p class="text-muted">Encuentra soluciones rápidas a problemas comunes (Procedimientos Estándar de Operación).</p>
                    <div class="input-group mt-3 shadow-sm">
                        <input type="text" class="form-control form-control-lg" placeholder="Buscar por error, software o hardware...">
                        <button class="btn btn-primary px-4" type="button">Buscar</button>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="accordion shadow-sm" id="accordionConocimientos">
                        <!-- Articulo 1 -->
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#colapsouno">
                                    PEO-01: Pasos para restablecer credenciales del ERP
                                </button>
                            </h2>
                            <div id="colapsouno" class="accordion-collapse collapse show" data-bs-parent="#accordionConocimientos">
                                <div class="accordion-body text-muted">
                                    <strong>1.</strong> Ingresar al panel de administración de Active Directory.<br>
                                    <strong>2.</strong> Buscar el correo del usuario solicitante.<br>
                                    <strong>3.</strong> Hacer clic derecho > Restablecer contraseña y marcar la opción "El usuario debe cambiar la contraseña en el siguiente inicio de sesión".
                                </div>
                            </div>
                        </div>
                        <!-- Articulo 2 -->
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#colapsodos">
                                    PEO-02: Solución a pantalla azul (Error código 0x0000007B)
                                </button>
                            </h2>
                            <div id="colapsodos" class="accordion-collapse collapse" data-bs-parent="#accordionConocimientos">
                                <div class="accordion-body text-muted">
                                    Este error indica un problema con el controlador de almacenamiento SATA. Ingresa a la BIOS del equipo (presionando F2 o DEL), ve a la pestaña "Advanced", cambia la configuración SATA de IDE a AHCI y guarda los cambios.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
