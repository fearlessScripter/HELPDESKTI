<?php
/**
 * Barra de búsqueda + panel de filtros para los listados de tickets (GET).
 *
 * Variables:
 *  - $campos       array  Campos a mostrar: estado, area, prioridad, tecnico, sla, fechas
 *  - $ordenes      array  Claves de TicketModel::ORDENES permitidas en este listado
 *  - $filtros      array  Valores actuales (Tickets::leerFiltros)
 *  - $ordenDefecto string
 *  - $areas, $estados, $prioridades, $tecnicos (catálogos)
 */
use App\Models\TicketModel;

$campos   = $campos ?? [];
$ordenes  = $ordenes ?? array_keys(TicketModel::ORDENES);
$f        = $filtros ?? [];
$accion   = current_url();

// Etiquetas legibles para los filtros activos (chips)
$buscarNombre = static function (array $lista, string $clave, $valor): string {
    foreach ($lista as $item) {
        if ((string)$item[$clave] === (string)$valor) {
            return $item['nombre'] ?? $item['nombre_completo'] ?? '';
        }
    }
    return '';
};

$activos = [];
if (!empty($f['q']))            $activos['q'] = 'Búsqueda: "' . $f['q'] . '"';
if (!empty($f['id_estado']))    $activos['id_estado'] = 'Estado: ' . $buscarNombre($estados ?? [], 'id_estado', $f['id_estado']);
if (!empty($f['id_area']))      $activos['id_area'] = 'Área: ' . $buscarNombre($areas ?? [], 'id_area', $f['id_area']);
if (!empty($f['id_prioridad'])) $activos['id_prioridad'] = 'Prioridad: ' . $buscarNombre($prioridades ?? [], 'id_prioridad', $f['id_prioridad']);
if (($f['id_tecnico'] ?? '') === 'sin') {
    $activos['id_tecnico'] = 'Técnico: Sin asignar';
} elseif (!empty($f['id_tecnico'])) {
    $activos['id_tecnico'] = 'Técnico: ' . $buscarNombre($tecnicos ?? [], 'id_usuario', $f['id_tecnico']);
}
if (!empty($f['sla']))          $activos['sla'] = $f['sla'] === 'vencido' ? 'SLA: Vencido' : 'SLA: En plazo';
if (!empty($f['desde']))        $activos['desde'] = 'Desde: ' . date('d/m/Y', strtotime($f['desde']));
if (!empty($f['hasta']))        $activos['hasta'] = 'Hasta: ' . date('d/m/Y', strtotime($f['hasta']));

// Cantidad de filtros del panel (sin contar la búsqueda de texto)
$numFiltrosPanel = count(array_diff_key($activos, ['q' => true]));

// URL actual sin un parámetro (para quitar un chip)
$sinParametro = static function (string $param) use ($accion): string {
    $query = service('request')->getGet();
    unset($query[$param], $query['page']);
    return $accion . ($query ? '?' . http_build_query($query) : '');
};
?>
<form id="filtrosForm" method="get" action="<?= esc($accion) ?>" class="mb-4">
    <input type="hidden" name="por_pagina" value="<?= esc($f['por_pagina'] ?? 10) ?>">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="input-group" style="max-width: 450px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="search" name="q" value="<?= esc($f['q'] ?? '') ?>" class="form-control"
                   placeholder="Buscar por código, asunto, descripción o solicitante...">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>

        <div class="d-flex gap-2">
            <?php if (!empty($campos)): ?>
            <button type="button" class="btn btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#panelFiltros" aria-expanded="<?= $numFiltrosPanel ? 'true' : 'false' ?>">
                <i class="bi bi-funnel"></i> Filtros <i class="bi bi-chevron-down"></i>
                <?php if ($numFiltrosPanel): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"><?= $numFiltrosPanel ?></span>
                <?php endif; ?>
            </button>
            <?php endif; ?>
            <?php if (isset($rol) && $rol !== 'Técnico'): ?>
            <a href="<?= base_url('/tickets/nuevo') ?>" class="btn btn-primary fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Ticket
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($campos)): ?>
    <div class="collapse <?= $numFiltrosPanel ? 'show' : '' ?>" id="panelFiltros">
        <div class="card card-body border-0 shadow-sm mt-3">
            <div class="row g-3">
                <?php if (in_array('estado', $campos, true)): ?>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Estado</label>
                    <select name="id_estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach ($estados ?? [] as $e): ?>
                            <option value="<?= esc($e['id_estado']) ?>" <?= (int)($f['id_estado'] ?? 0) === (int)$e['id_estado'] ? 'selected' : '' ?>><?= esc($e['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (in_array('area', $campos, true)): ?>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Área afectada</label>
                    <select name="id_area" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <?php foreach ($areas ?? [] as $a): ?>
                            <option value="<?= esc($a['id_area']) ?>" <?= (int)($f['id_area'] ?? 0) === (int)$a['id_area'] ? 'selected' : '' ?>><?= esc($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (in_array('prioridad', $campos, true)): ?>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Prioridad</label>
                    <select name="id_prioridad" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <?php foreach ($prioridades ?? [] as $p): ?>
                            <option value="<?= esc($p['id_prioridad']) ?>" <?= (int)($f['id_prioridad'] ?? 0) === (int)$p['id_prioridad'] ? 'selected' : '' ?>><?= esc($p['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (in_array('tecnico', $campos, true)): ?>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Técnico</label>
                    <select name="id_tecnico" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php if (in_array('tecnico_sin', $campos, true)): ?>
                            <option value="sin" <?= ($f['id_tecnico'] ?? '') === 'sin' ? 'selected' : '' ?>>Sin asignar</option>
                        <?php endif; ?>
                        <?php foreach ($tecnicos ?? [] as $tec): ?>
                            <option value="<?= esc($tec['id_usuario']) ?>" <?= ($f['id_tecnico'] ?? '') !== 'sin' && (int)($f['id_tecnico'] ?? 0) === (int)$tec['id_usuario'] ? 'selected' : '' ?>><?= esc($tec['nombre_completo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (in_array('sla', $campos, true)): ?>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Estado del SLA</label>
                    <select name="sla" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="vencido" <?= ($f['sla'] ?? '') === 'vencido' ? 'selected' : '' ?>>Vencido</option>
                        <option value="en_plazo" <?= ($f['sla'] ?? '') === 'en_plazo' ? 'selected' : '' ?>>En plazo</option>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (in_array('fechas', $campos, true)): ?>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Fecha de creación</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Desde</span>
                        <input type="date" name="desde" id="filtroDesde" value="<?= esc($f['desde'] ?? '') ?>" class="form-control">
                        <span class="input-group-text">Hasta</span>
                        <input type="date" name="hasta" id="filtroHasta" value="<?= esc($f['hasta'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        <button type="button" class="btn btn-link btn-sm p-0 me-2 text-decoration-none rango-rapido" data-dias="0">Hoy</button>
                        <button type="button" class="btn btn-link btn-sm p-0 me-2 text-decoration-none rango-rapido" data-dias="7">Últimos 7 días</button>
                        <button type="button" class="btn btn-link btn-sm p-0 me-2 text-decoration-none rango-rapido" data-dias="30">Últimos 30 días</button>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none rango-rapido" data-dias="mes">Este mes</button>
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Ordenar por</label>
                    <select name="orden" class="form-select form-select-sm">
                        <?php foreach ($ordenes as $clave): ?>
                            <?php $seleccionado = ($f['orden'] ?? '') !== '' ? $f['orden'] : ($ordenDefecto ?? 'recientes'); ?>
                            <option value="<?= esc($clave) ?>" <?= $seleccionado === $clave ? 'selected' : '' ?>><?= esc(TicketModel::ORDENES[$clave][0]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="<?= esc($accion) ?>" class="btn btn-light btn-sm fw-semibold"><i class="bi bi-x-circle me-1"></i> Limpiar</a>
                <button type="submit" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-funnel-fill me-1"></i> Aplicar filtros</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($activos)): ?>
    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
        <span class="small text-muted">Filtros activos:</span>
        <?php foreach ($activos as $param => $texto): ?>
            <a href="<?= esc($sinParametro($param)) ?>" class="badge rounded-pill text-bg-light border text-decoration-none fw-normal py-2 px-3" title="Quitar filtro">
                <?= esc($texto) ?> <i class="bi bi-x ms-1"></i>
            </a>
        <?php endforeach; ?>
        <a href="<?= esc($accion) ?>" class="small text-decoration-none ms-1">Limpiar todo</a>
    </div>
    <?php endif; ?>
</form>

<script>
// Atajos de rango de fechas (Hoy, Últimos 7/30 días, Este mes)
document.querySelectorAll('.rango-rapido').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var hoy = new Date();
        var desde = new Date(hoy);
        if (btn.dataset.dias === 'mes') {
            desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        } else {
            desde.setDate(hoy.getDate() - parseInt(btn.dataset.dias, 10));
        }
        var iso = function (d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        };
        document.getElementById('filtroDesde').value = iso(desde);
        document.getElementById('filtroHasta').value = iso(hoy);
    });
});
</script>
