<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= tema_bootstrap() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - <?= esc(APP_NOMBRE) ?></title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f5f6f8; min-height: 100vh; }

        .login-wrapper { min-height: 100vh; }

        /* Panel de marca: mismo gris oscuro del menú lateral del sistema */
        .login-marca {
            background-color: #212529;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .login-marca::after { /* detalle decorativo sutil */
            content: '';
            position: absolute;
            width: 420px; height: 420px;
            right: -140px; bottom: -140px;
            border-radius: 50%;
            border: 60px solid rgba(13, 110, 253, .12);
        }
        .marca-icono {
            width: 56px; height: 56px;
            display: flex; align-items: center; justify-content: center;
            background-color: #0d6efd; border-radius: 14px; font-size: 1.75rem;
        }
        .marca-titulo { font-size: 2rem; font-weight: 700; letter-spacing: -.02em; }
        .marca-texto { color: #adb5bd; max-width: 380px; }
        .marca-item { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 22px; position: relative; z-index: 1; }
        .marca-item i {
            flex-shrink: 0;
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            background-color: #343a40; border-radius: 10px; color: #6ea8fe; font-size: 1.1rem;
        }
        .marca-item strong { display: block; font-weight: 600; }
        .marca-item span { color: #adb5bd; font-size: .9rem; }

        /* Formulario */
        .login-card { width: 100%; max-width: 420px; border-radius: 14px; }
        .login-card .input-group-text { background-color: var(--bs-body-bg); color: var(--bs-secondary-color); min-width: 44px; justify-content: center; }
        .login-card .form-control { border-left: 0; padding-left: .25rem; }
        .login-card .input-group-text:first-child { border-right: 0; }
        .login-card .input-group:focus-within .input-group-text,
        .login-card .input-group:focus-within .form-control,
        .login-card .input-group:focus-within .btn-ver-clave { border-color: #86b7fe; }
        .login-card .input-group:focus-within { box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .2); border-radius: var(--bs-border-radius-lg); }
        .login-card .form-control:focus { box-shadow: none; }
        .btn-ver-clave { border-color: var(--bs-border-color); border-left: 0; color: var(--bs-secondary-color); background-color: var(--bs-body-bg); }
        .btn-ver-clave:hover { color: var(--bs-body-color); background-color: var(--bs-body-bg); border-color: var(--bs-border-color); }
        .login-card .password-input { border-right: 0; }
        /* Microsoft Edge agrega su propio ojo (y una "X") a los campos de contraseña:
           se ocultan para que solo exista nuestro botón de la derecha */
        .password-input::-ms-reveal,
        .password-input::-ms-clear { display: none; }

        [data-bs-theme="dark"] .login-marca { background-color: #0f1114; border-right: 1px solid #23272c; }
        [data-bs-theme="dark"] .login-card { box-shadow: 0 0 0 1px rgba(255, 255, 255, .07) !important; }
    </style>
    <?= view('layout/tema') ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row login-wrapper">

            <!-- Panel de marca (en pantallas pequeñas se oculta y queda solo el encabezado del formulario) -->
            <div class="col-lg-5 col-xl-4 login-marca d-none d-lg-flex flex-column justify-content-between p-5">
                <div class="d-flex align-items-center gap-3">
                    <div class="marca-icono"><i class="bi bi-headset"></i></div>
                    <div class="marca-titulo"><?= esc(APP_NOMBRE) ?></div>
                </div>

                <div>
                    <h2 class="fw-bold mb-3">Mesa de ayuda y gestión de incidencias</h2>
                    <p class="marca-texto mb-5">Centraliza las solicitudes de soporte tecnológico de la organización y haz seguimiento de cada caso hasta su solución.</p>

                    <div class="marca-item">
                        <i class="bi bi-ticket-detailed"></i>
                        <div><strong>Registra y sigue tus tickets</strong><span>Conoce el estado de tu solicitud en todo momento.</span></div>
                    </div>
                    <div class="marca-item">
                        <i class="bi bi-speedometer2"></i>
                        <div><strong>Atención según prioridad</strong><span>Cada incidencia se atiende dentro de su plazo de servicio.</span></div>
                    </div>
                    <div class="marca-item mb-0">
                        <i class="bi bi-book"></i>
                        <div><strong>Base de conocimientos</strong><span>Las soluciones aplicadas quedan documentadas.</span></div>
                    </div>
                </div>

                <div class="small" style="color: #8f969d; position: relative; z-index: 1;">
                    &copy; <?= date('Y') ?> <?= esc(APP_NOMBRE) ?> · Versión <?= esc(APP_VERSION) ?>
                </div>
            </div>

            <!-- Formulario -->
            <div class="col-lg-7 col-xl-8 d-flex flex-column align-items-center justify-content-center p-4">

                <!-- Marca compacta (solo pantallas pequeñas) -->
                <div class="d-flex d-lg-none align-items-center gap-2 mb-4">
                    <div class="marca-icono text-white" style="width: 44px; height: 44px; font-size: 1.35rem;"><i class="bi bi-headset"></i></div>
                    <div class="fs-3 fw-bold"><?= esc(APP_NOMBRE) ?></div>
                </div>

                <div class="card login-card border-0 shadow-sm">
                    <div class="card-body p-4 p-sm-5">
                        <h3 class="fw-bold mb-1">Iniciar sesión</h3>
                        <p class="text-muted mb-4">Ingresa con tu cuenta corporativa para continuar.</p>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger d-flex align-items-center gap-2 py-2 small" role="alert">
                                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                                <div><?= session()->getFlashdata('error') ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('/auth/login') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="correo">Correo electrónico</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control fs-6" id="correo" name="correo" required autofocus
                                           autocomplete="username" placeholder="usuario@empresa.cl">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold" for="password">Contraseña</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control fs-6 password-input" id="password" name="password" required
                                           autocomplete="current-password" placeholder="Ingresa tu contraseña">
                                    <button type="button" class="btn btn-ver-clave" id="verClave" title="Mostrar contraseña" aria-label="Mostrar contraseña">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="text-end mb-4">
                                <!-- TODO: conectar con el flujo de recuperación de contraseña -->
                                <a href="#" class="link-primary small fw-semibold text-decoration-none">¿Olvidaste tu contraseña?</a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold fs-6 py-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center small text-muted mt-4">
                    <i class="bi bi-shield-lock me-1"></i> Acceso exclusivo para usuarios autorizados.
                    <div class="d-lg-none mt-1">&copy; <?= date('Y') ?> <?= esc(APP_NOMBRE) ?> · Versión <?= esc(APP_VERSION) ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Mostrar / ocultar la contraseña
    document.getElementById('verClave').addEventListener('click', function () {
        var campo = document.getElementById('password');
        var visible = campo.type === 'text';
        campo.type = visible ? 'password' : 'text';
        this.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
        this.title = visible ? 'Mostrar contraseña' : 'Ocultar contraseña';
        this.setAttribute('aria-label', this.title);
        campo.focus();
    });
    </script>
</body>
</html>
