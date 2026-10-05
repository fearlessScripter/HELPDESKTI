<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - HelpDesk TI</title>
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
        .ticket-table { background-color: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .ticket-table table { margin-bottom: 0; }
        .ticket-table th { font-size: 13px; color: #6c757d; font-weight: 600; }
        .ticket-table td { vertical-align: middle; }
    </style>
</head>
<body>

<?= view('layout/sidebar', ['active' => 'usuarios']) ?>

<div class="main-content">
    <?= view('layout/topbar', ['pageTitle' => 'Gestión de Accesos']) ?>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group" style="max-width: 450px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" placeholder="Buscar por nombre o correo...">
            </div>
            <button class="btn btn-primary fw-bold" onclick="alert('Simulación: Modal para crear nuevo usuario')">
                <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
            </button>
        </div>

        <div class="ticket-table">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                        <th>Rol del Sistema</th>
                        <th>Área / Departamento</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">Juan Administrador</td>
                        <td class="text-muted">admin@empresa.cl</td>
                        <td><span class="badge bg-dark">Administrador</span></td>
                        <td>Gerencia TI</td>
                        <td class="text-center"><span class="badge bg-success">Activo</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Luis Técnico</td>
                        <td class="text-muted">tecnico@empresa.cl</td>
                        <td><span class="badge bg-info text-dark">Técnico</span></td>
                        <td>Soporte Nivel 2</td>
                        <td class="text-center"><span class="badge bg-success">Activo</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">María Solicitante</td>
                        <td class="text-muted">usuario@empresa.cl</td>
                        <td><span class="badge bg-secondary">Solicitante</span></td>
                        <td>Recursos Humanos</td>
                        <td class="text-center"><span class="badge bg-success">Activo</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Pedro Ventas</td>
                        <td class="text-muted">pedro.v@empresa.cl</td>
                        <td><span class="badge bg-secondary">Solicitante</span></td>
                        <td>Comercial</td>
                        <td class="text-center"><span class="badge bg-secondary">Inactivo</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
