<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Base de Conocimientos - HelpDesk TI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">HelpDesk TI</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Documentación Técnica</span>
                <a href="<?= base_url('/dashboard') ?>" class="btn btn-sm btn-light text-primary fw-bold">Volver al Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>