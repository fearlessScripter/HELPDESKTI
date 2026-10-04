<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - HelpDesk TI</title>
</head>
<body>
    <div class="login-container">
        <h2>Acceso HelpDesk TI</h2>
        
        <!-- Mostrar errores si los hay -->
        <?php if(session()->getFlashdata('error')): ?>
            <div style="color: red;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/auth/login') ?>" method="post">
            <div>
                <label>Correo Electrónico</label>
                <input type="email" name="correo" required placeholder="usuario@empresa.com">
            </div>
            <div>
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>