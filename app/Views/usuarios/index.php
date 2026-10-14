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
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
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
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="fw-bold"><?= esc($u['nombre_completo']) ?></td>
                            <td class="text-muted"><?= esc($u['correo']) ?></td>
                            <td>
                                <?php if ($u['id_rol'] == 1): ?>
                                    <span class="badge bg-dark">Administrador</span>
                                <?php elseif ($u['id_rol'] == 2): ?>
                                    <span class="badge bg-info text-dark">Técnico</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Solicitante</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($u['area_nombre'] ?? 'Sin asignar') ?></td>
                            <td class="text-center">
                                <span class="badge <?= $u['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst(esc($u['estado'])) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary" title="Usuario activo"><i class="bi bi-shield-check"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No se encontraron usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-labelledby="nuevoUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="nuevoUsuarioModalLabel">Registrar Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= base_url('/usuarios/crear') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nuevoNombre" class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nuevoNombre" name="nombre_completo" placeholder="Ej: Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevoCorreo" class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="nuevoCorreo" name="correo" placeholder="ejemplo@empresa.cl" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevaClave" class="form-label fw-semibold">Contraseña Inicial <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="nuevaClave" name="password" placeholder="Asigne una contraseña" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevoRol" class="form-label fw-semibold">Rol del Sistema <span class="text-danger">*</span></label>
                        <select class="form-select" id="nuevoRol" name="id_rol" required>
                            <option value="" selected disabled>Seleccione un rol...</option>
                            <option value="1">Administrador</option>
                            <option value="2">Técnico</option>
                            <option value="3">Solicitante</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="nuevoArea" class="form-label fw-semibold">Área / Departamento</label>
                        <select class="form-select" id="nuevoArea" name="id_area">
                            <option value="">Sin área específica</option>
                            <?php if (!empty($areas)): ?>
                                <?php foreach ($areas as $a): ?>
                                    <option value="<?= esc($a['id_area']) ?>"><?= esc($a['nombre']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Make sure jQuery is available for the simulation if needed, or stick to vanilla alert -->
</body>
</html>
