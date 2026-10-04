<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - HelpDesk TI</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .menu { background: #f4f4f4; padding: 15px; border-radius: 5px; }
        .logout { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Bienvenido al HelpDesk TI</h1>
    <p>Hola, <strong><?= esc($nombre) ?></strong>.</p>
    <p>Tu rol en el sistema es: <strong><?= esc($rol) ?></strong>.</p>
    
    <div class="menu">
        <h3>Menú Principal</h3>
        <ul>
            <!-- Opciones exclusivas según el rol -->
            <?php if($rol == 'Solicitante'): ?>
                <li><a href="<?= base_url('/tickets/nuevo') ?>">Registrar Nuevo Ticket</a></li>
                <li><a href="<?= base_url('/tickets/mis-tickets') ?>">Mis Tickets</a></li>
                
            <?php elseif($rol == 'Técnico'): ?>
                <li><a href="<?= base_url('/tickets/asignados') ?>">Tickets Asignados</a></li>
                <li><a href="<?= base_url('/base-conocimientos') ?>">Base de Conocimientos</a></li>
                
            <?php elseif($rol == 'Administrador'): ?>
                <li><a href="<?= base_url('/tickets/cola') ?>">Cola de Tickets (Asignar)</a></li>
                <li><a href="<?= base_url('/reportes') ?>">Reportes y SLA</a></li>
                <li><a href="<?= base_url('/usuarios') ?>">Gestión de Usuarios</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <br><br>
    <a href="<?= base_url('/logout') ?>" class="logout">Cerrar Sesión</a>
</body>
</html>