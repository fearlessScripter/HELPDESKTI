<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= tema_bootstrap() ?>">
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

    <?= view('layout/tema') ?>

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
                </div>
            </div>

            <?php
                $numFiltrosPanel     = ($filtros['id_autor'] ? 1 : 0) + ($filtros['origen'] !== '' ? 1 : 0) + ($filtros['desde'] ? 1 : 0) + ($filtros['hasta'] ? 1 : 0);
                $hayFiltrosAvanzados = $numFiltrosPanel > 0;
            ?>
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <form id="filtrosForm" method="get" action="<?= current_url() ?>">
                        <input type="hidden" name="por_pagina" value="<?= esc($filtros['por_pagina']) ?>">
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-search" data-busqueda-icono></i></span>
                                <input type="search" name="q" value="<?= esc($filtros['q']) ?>" class="form-control" autocomplete="off"
                                       data-busqueda-en-vivo aria-label="Buscar soluciones"
                                       placeholder="Buscar por error, software, hardware, código SOL o ticket...">
                            </div>
                            <button class="btn btn-outline-secondary position-relative text-nowrap shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosKb">
                                <i class="bi bi-funnel"></i> Filtros <i class="bi bi-chevron-down"></i>
                                <?php if ($numFiltrosPanel): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"><?= $numFiltrosPanel ?></span>
                                <?php endif; ?>
                            </button>
                        </div>

                        <div class="collapse <?= $hayFiltrosAvanzados ? 'show' : '' ?>" id="filtrosKb">
                            <div class="card card-body border-0 shadow-sm mt-2">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted mb-1">Autor</label>
                                        <select name="id_autor" class="form-select form-select-sm">
                                            <option value="">Todos</option>
                                            <?php foreach ($autores ?? [] as $au): ?>
                                                <option value="<?= esc($au['id_usuario']) ?>" <?= $filtros['id_autor'] === (int)$au['id_usuario'] ? 'selected' : '' ?>><?= esc($au['nombre_completo']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-muted mb-1">Origen</label>
                                        <select name="origen" class="form-select form-select-sm">
                                            <option value="">Todos</option>
                                            <option value="ticket" <?= $filtros['origen'] === 'ticket' ? 'selected' : '' ?>>Desde un ticket cerrado</option>
                                            <option value="manual" <?= $filtros['origen'] === 'manual' ? 'selected' : '' ?>>Documentación manual</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-semibold text-muted mb-1">Fecha de creación</label>
                                        <div class="input-group input-group-sm">
                                            <input type="date" name="desde" value="<?= esc($filtros['desde']) ?>" class="form-control" title="Desde">
                                            <span class="input-group-text">a</span>
                                            <input type="date" name="hasta" value="<?= esc($filtros['hasta']) ?>" class="form-control" title="Hasta">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-2">
                                    <a href="<?= current_url() ?>" class="btn btn-light btn-sm fw-semibold"><i class="bi bi-x-circle me-1"></i> Limpiar</a>
                                    <button type="submit" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-funnel-fill me-1"></i> Aplicar filtros</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?= view('layout/busqueda_en_vivo') ?>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small" data-region="conteo">
                            <?= $pager->getTotal() ?> solución(es) encontradas
                            <?php if ($filtros['q'] !== '' || $hayFiltrosAvanzados): ?>
                                · <a href="<?= current_url() ?>" class="text-decoration-none">ver todas</a>
                            <?php endif; ?>
                        </span>
                        <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#nuevaSolucionModal">
                            <i class="bi bi-plus-lg me-1"></i> Nueva Solución
                        </button>
                    </div>

                    <div class="accordion shadow-sm" id="accordionConocimientos" data-region="resultados" data-resaltable>
                        <?php if (!empty($articulos)): ?>
                            <?php foreach ($articulos as $index => $art): ?>
                            <div class="accordion-item border-0 mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?> fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#colapso<?= $art['id_solucion'] ?>">
                                        <span class="badge bg-primary me-2"><?= esc($art['codigo_solucion']) ?></span>
                                        <?= esc($art['titulo']) ?>
                                    </button>
                                </h2>
                                <div id="colapso<?= $art['id_solucion'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#accordionConocimientos">
                                    <div class="accordion-body text-secondary" style="white-space: pre-line;">
                                        <?= esc($art['contenido']) ?>
                                        <div class="mt-3 pt-2 border-top d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted" style="white-space: normal;">
                                            <span><i class="bi bi-person me-1"></i> Autor: <?= esc(nombre_con_rol($art['autor_nombre'], $art['autor_rol'])) ?></span>
                                            <?php if (!empty($art['ticket_codigo'])): ?>
                                                <?php if (\App\Models\TicketModel::usuarioPuedeVer($art, (int)$id_usuario, (string)$rol)): ?>
                                                    <a href="<?= base_url('/tickets/detalles/' . $art['id_ticket_referencia']) ?>" class="text-decoration-none">
                                                        <i class="bi bi-ticket-detailed me-1"></i> Ticket origen #<?= esc($art['ticket_codigo']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span><i class="bi bi-ticket-detailed me-1"></i> Ticket origen #<?= esc($art['ticket_codigo']) ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <span>
                                                <i class="bi bi-calendar3 me-1"></i> <?= esc(fecha_hora($art['fecha_creacion'])) ?>
                                                <?php if (!empty($art['fecha_actualizacion']) && $art['fecha_actualizacion'] !== $art['fecha_creacion']): ?>
                                                    · Actualizado: <?= esc(fecha_hora($art['fecha_actualizacion'])) ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <?php if (isset($rol) && $rol === 'Administrador'): ?>
                                        <div class="mt-2 text-end" style="white-space: normal;">
                                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold btn-modificar"
                                                    data-bs-toggle="modal" data-bs-target="#modificarSolucionModal"
                                                    data-id="<?= esc($art['id_solucion']) ?>"
                                                    data-codigo="<?= esc($art['codigo_solucion']) ?>"
                                                    data-titulo="<?= esc($art['titulo']) ?>"
                                                    data-contenido="<?= esc($art['contenido']) ?>">
                                                <i class="bi bi-pencil me-1"></i> Modificar
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-light text-center py-4 text-muted">
                                <?= ($filtros['q'] !== '' || $hayFiltrosAvanzados)
                                    ? 'No se encontraron soluciones con esos criterios de búsqueda.'
                                    : 'No hay artículos documentados todavía. ¡Crea el primero!' ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card border-0 shadow-sm mt-3 overflow-hidden" data-region="paginacion">
                        <?= view('layout/paginacion') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Solución -->
<div class="modal fade" id="nuevaSolucionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('/base-conocimientos/crear') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Documentar Nueva Solución Técnica</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Título del Problema / Falla <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="titulo" placeholder="Ej: Configuración de VPN en Windows 11 o Restablecimiento de Token" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Procedimiento de Solución <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="contenido" rows="6" placeholder="Detalla paso a paso la solución comprobada..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Publicar en Base de Conocimientos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (isset($rol) && $rol === 'Administrador'): ?>
<!-- Modal Modificar Solución (solo Administrador) -->
<div class="modal fade" id="modificarSolucionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('/base-conocimientos/actualizar') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_solucion" id="editIdSolucion">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Modificar Solución <span class="badge bg-primary" id="editCodigo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="editTitulo">Título del Problema / Falla <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="titulo" id="editTitulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="editContenido">Procedimiento de Solución <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="contenido" id="editContenido" rows="8" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Rellena el modal de edición con los datos de la solución seleccionada.
// Delegación en document: la búsqueda en vivo reemplaza los artículos.
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-modificar');
    if (!btn) return;
    document.getElementById('editIdSolucion').value = btn.dataset.id;
    document.getElementById('editCodigo').textContent = btn.dataset.codigo;
    document.getElementById('editTitulo').value = btn.dataset.titulo;
    document.getElementById('editContenido').value = btn.dataset.contenido;
});
</script>
</body>
</html>
