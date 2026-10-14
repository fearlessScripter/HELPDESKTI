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
        /* overflow visible para que el menú de Acciones no quede recortado en las últimas filas */
        .ticket-table { background-color: white; border-radius: 10px; overflow: visible; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
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
        <?php $hayFiltros = $filtros['q'] !== '' || $filtros['id_rol'] || $filtros['id_area'] !== '' || $filtros['estado'] !== ''; ?>
        <form id="filtrosForm" method="get" action="<?= current_url() ?>" class="card card-body border-0 shadow-sm mb-4">
            <input type="hidden" name="por_pagina" value="<?= esc($filtros['por_pagina']) ?>">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Buscar</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="<?= esc($filtros['q']) ?>" class="form-control" placeholder="Nombre o correo...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Rol</label>
                    <select name="id_rol" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach ([1 => 'Administrador', 2 => 'Técnico', 3 => 'Solicitante'] as $idR => $nomR): ?>
                            <option value="<?= $idR ?>" <?= $filtros['id_rol'] === $idR ? 'selected' : '' ?>><?= $nomR ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Área</label>
                    <select name="id_area" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="sin" <?= $filtros['id_area'] === 'sin' ? 'selected' : '' ?>>Sin área</option>
                        <?php foreach ($areas ?? [] as $a): ?>
                            <option value="<?= esc($a['id_area']) ?>" <?= $filtros['id_area'] === (string)$a['id_area'] ? 'selected' : '' ?>><?= esc($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="activo" <?= $filtros['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $filtros['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold flex-fill"><i class="bi bi-funnel-fill"></i> Filtrar</button>
                    <?php if ($hayFiltros): ?>
                        <a href="<?= current_url() ?>" class="btn btn-light btn-sm" title="Limpiar filtros"><i class="bi bi-x-circle"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">Usuarios del sistema</h5>
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
                                <?php
                                    $esYo   = (int)$u['id_usuario'] === (int)$id_usuario;
                                    $activo = $u['estado'] === 'activo';
                                    $datos  = 'data-id="' . esc($u['id_usuario']) . '" data-nombre="' . esc($u['nombre_completo']) . '"';
                                ?>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear"></i> Acciones
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <button type="button" class="dropdown-item btn-editar" <?= $datos ?>
                                                    data-correo="<?= esc($u['correo']) ?>" data-rol="<?= esc($u['id_rol']) ?>"
                                                    data-area="<?= esc($u['id_area'] ?? '') ?>" data-yo="<?= $esYo ? '1' : '0' ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editarUsuarioModal">
                                                <i class="bi bi-pencil me-2 text-primary"></i> Editar datos
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item btn-password" <?= $datos ?>
                                                    data-bs-toggle="modal" data-bs-target="#passwordUsuarioModal">
                                                <i class="bi bi-key me-2 text-warning"></i> Restablecer contraseña
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <?php if ($esYo): ?>
                                                <span class="dropdown-item text-muted small" title="No puedes deshabilitar tu propia cuenta">
                                                    <i class="bi bi-lock me-2"></i> Tu cuenta (no se puede deshabilitar)
                                                </span>
                                            <?php else: ?>
                                                <button type="button" class="dropdown-item btn-estado <?= $activo ? 'text-danger' : 'text-success' ?>" <?= $datos ?>
                                                        data-activo="<?= $activo ? '1' : '0' ?>"
                                                        data-bs-toggle="modal" data-bs-target="#estadoUsuarioModal">
                                                    <?php if ($activo): ?>
                                                        <i class="bi bi-person-x me-2"></i> Deshabilitar usuario
                                                    <?php else: ?>
                                                        <i class="bi bi-person-check me-2"></i> Habilitar usuario
                                                    <?php endif; ?>
                                                </button>
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No se encontraron usuarios con los filtros aplicados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?= view('layout/paginacion') ?>
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

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('/usuarios/actualizar') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_usuario" id="editId">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editNombre" class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editNombre" name="nombre_completo" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCorreo" class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="editCorreo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRol" class="form-label fw-semibold">Rol del Sistema <span class="text-danger">*</span></label>
                        <select class="form-select" id="editRol" name="id_rol" required>
                            <option value="1">Administrador</option>
                            <option value="2">Técnico</option>
                            <option value="3">Solicitante</option>
                        </select>
                        <div class="form-text d-none" id="editRolAviso">No puedes cambiar tu propio rol.</div>
                    </div>
                    <div class="mb-2">
                        <label for="editArea" class="form-label fw-semibold">Área / Departamento</label>
                        <select class="form-select" id="editArea" name="id_area">
                            <option value="">Sin área específica</option>
                            <?php foreach ($areas ?? [] as $a): ?>
                                <option value="<?= esc($a['id_area']) ?>"><?= esc($a['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Restablecer Contraseña -->
<div class="modal fade" id="passwordUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('/usuarios/password') ?>" method="post" id="formPassword">
                <?= csrf_field() ?>
                <input type="hidden" name="id_usuario" id="passId">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Restablecer Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small">Asigna una nueva contraseña a <strong id="passNombre"></strong>. Comunícasela al usuario por un medio seguro.</p>
                    <div class="mb-3">
                        <label for="passNueva" class="form-label fw-semibold">Nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="passNueva" name="password" minlength="6" required>
                        <div class="form-text">Mínimo 6 caracteres.</div>
                    </div>
                    <div class="mb-2">
                        <label for="passConfirmar" class="form-label fw-semibold">Confirmar contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="passConfirmar" name="password_confirmar" minlength="6" required>
                        <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold">Restablecer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Habilitar / Deshabilitar -->
<div class="modal fade" id="estadoUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('/usuarios/estado') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_usuario" id="estadoId">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="estadoTitulo">Deshabilitar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4" id="estadoTexto"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn fw-bold" id="estadoBoton">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Rellena los modales de acciones con los datos del usuario seleccionado
document.querySelectorAll('.btn-editar').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var esYo = btn.dataset.yo === '1';
        document.getElementById('editId').value = btn.dataset.id;
        document.getElementById('editNombre').value = btn.dataset.nombre;
        document.getElementById('editCorreo').value = btn.dataset.correo;
        document.getElementById('editRol').value = btn.dataset.rol;
        document.getElementById('editArea').value = btn.dataset.area;
        // Un select deshabilitado no se envía, así que se bloquea con estilo + aviso y el servidor lo valida
        document.getElementById('editRol').style.pointerEvents = esYo ? 'none' : '';
        document.getElementById('editRol').classList.toggle('bg-light', esYo);
        document.getElementById('editRolAviso').classList.toggle('d-none', !esYo);
    });
});

document.querySelectorAll('.btn-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('formPassword').reset();
        document.getElementById('passConfirmar').classList.remove('is-invalid');
        document.getElementById('passId').value = btn.dataset.id;
        document.getElementById('passNombre').textContent = btn.dataset.nombre;
    });
});

document.getElementById('formPassword').addEventListener('submit', function (e) {
    var confirmar = document.getElementById('passConfirmar');
    if (document.getElementById('passNueva').value !== confirmar.value) {
        e.preventDefault();
        confirmar.classList.add('is-invalid');
    }
});

document.querySelectorAll('.btn-estado').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var activo = btn.dataset.activo === '1';
        var nombre = document.createElement('strong');
        nombre.textContent = btn.dataset.nombre;
        document.getElementById('estadoId').value = btn.dataset.id;
        document.getElementById('estadoTitulo').textContent = activo ? 'Deshabilitar Usuario' : 'Habilitar Usuario';
        var texto = document.getElementById('estadoTexto');
        texto.innerHTML = '';
        texto.append(activo ? '¿Deseas deshabilitar a ' : '¿Deseas habilitar nuevamente a ', nombre, '?');
        var detalle = document.createElement('p');
        detalle.className = 'text-muted small mt-2 mb-0';
        detalle.textContent = activo
            ? 'El usuario no podrá iniciar sesión hasta que lo vuelvas a habilitar. Sus tickets e historial se conservan.'
            : 'El usuario podrá volver a iniciar sesión con su contraseña actual.';
        texto.append(detalle);
        var boton = document.getElementById('estadoBoton');
        boton.textContent = activo ? 'Deshabilitar' : 'Habilitar';
        boton.className = 'btn fw-bold ' + (activo ? 'btn-danger' : 'btn-success');
    });
});
</script>
</body>
</html>
