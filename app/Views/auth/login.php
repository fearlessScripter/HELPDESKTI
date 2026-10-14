<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= tema_bootstrap() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HelpDesk TI</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('layout/tema') ?>
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4 text-primary fw-bold">HelpDesk TI</h2>
                        
                        <!-- Mostrar errores si los hay -->
                        <?php if(session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('/auth/login') ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo" required placeholder="usuario@empresa.com">
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="text-end mb-4">
                                <!-- TODO: conectar con el flujo de recuperación de contraseña -->
                                <a href="#" class="link-primary small fw-semibold text-decoration-none">¿Olvidaste tu contraseña?</a>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Iniciar Sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>