<?= $this->extend('layout/principal') ?>
<?php
$this->setVar('menuActivo', 'usuarios');
$this->setVar('topbar', ['pageTitle' => 'Gestión de Accesos']);
?>

<?= $this->section('titulo') ?>Usuarios - HelpDesk TI<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<style>
    /* overflow visible para que el menú de Acciones no quede recortado en las últimas filas */
    .ticket-table { overflow: visible; }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

    <div class="content">
        <?php $numFiltrosPanel = ($filtros['id_rol'] ? 1 : 0) + ($filtros['id_area'] !== '' ? 1 : 0) + ($filtros['estado'] !== '' ? 1 : 0); ?>
        <form id="filtrosForm" method="get" action="<?= current_url() ?>" class="mb-4">
            <input type="hidden" name="por_pagina" value="<?= esc($filtros['por_pagina']) ?>">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="input-group" style="max-width: 450px;">
                    <span class="input-group-text bg-white"><i class="bi bi-search" data-busqueda-icono></i></span>
                    <input type="search" name="q" value="<?= esc($filtros['q']) ?>" class="form-control" autocomplete="off"
                           data-busqueda-en-vivo aria-label="Buscar usuarios" placeholder="Buscar por nombre, correo, rol o área...">
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary position-relative" data-bs-toggle="collapse"
                            data-bs-target="#panelFiltros" aria-expanded="<?= $numFiltrosPanel ? 'true' : 'false' ?>">
                        <i class="bi bi-funnel"></i> Filtros <i class="bi bi-chevron-down"></i>
                        <?php if ($numFiltrosPanel): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"><?= $numFiltrosPanel ?></span>
                        <?php endif; ?>
                    </button>
                    <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                        <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
                    </button>
                </div>
            </div>

            <div class="collapse <?= $numFiltrosPanel ? 'show' : '' ?>" id="panelFiltros">
            <div class="card card-body border-0 shadow-sm mt-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Rol</label>
                    <select name="id_rol" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach ([1 => 'Administrador', 2 => 'Técnico', 3 => 'Solicitante'] as $idR => $nomR): ?>
                            <option value="<?= $idR ?>" <?= $filtros['id_rol'] === $idR ? 'selected' : '' ?>><?= $nomR ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Área</label>
                    <select name="id_area" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="sin" <?= $filtros['id_area'] === 'sin' ? 'selected' : '' ?>>Sin área</option>
                        <?php foreach ($areas ?? [] as $a): ?>
                            <option value="<?= esc($a['id_area']) ?>" <?= $filtros['id_area'] === (string)$a['id_area'] ? 'selected' : '' ?>><?= esc($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="activo" <?= $filtros['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $filtros['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="<?= current_url() ?>" class="btn btn-light btn-sm fw-semibold"><i class="bi bi-x-circle me-1"></i> Limpiar</a>
                <button type="submit" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-funnel-fill me-1"></i> Aplicar filtros</button>
            </div>
            </div>
            </div>
        </form>

        <?= view('layout/busqueda_en_vivo') ?>

        <div class="ticket-table" data-region="resultados">
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
                <tbody data-resaltable>
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
<?= $this->endSection() ?>

<?= $this->section('modales') ?>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Rellena los modales de acciones con los datos del usuario seleccionado.
// Se usa delegación en document porque la búsqueda en vivo reemplaza las filas de la tabla.
function alHacerClic(selector, accion) {
    document.addEventListener('click', function (e) {
        var btn = e.target.closest(selector);
        if (btn) accion(btn);
    });
}

alHacerClic('.btn-editar', function (btn) {
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

alHacerClic('.btn-password', function (btn) {
    document.getElementById('formPassword').reset();
    document.getElementById('passConfirmar').classList.remove('is-invalid');
    document.getElementById('passId').value = btn.dataset.id;
    document.getElementById('passNombre').textContent = btn.dataset.nombre;
});

document.getElementById('formPassword').addEventListener('submit', function (e) {
    var confirmar = document.getElementById('passConfirmar');
    if (document.getElementById('passNueva').value !== confirmar.value) {
        e.preventDefault();
        confirmar.classList.add('is-invalid');
    }
});

alHacerClic('.btn-estado', function (btn) {
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
</script>
<?= $this->endSection() ?>
