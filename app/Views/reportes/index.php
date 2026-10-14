<?php
/**
 * Reportes y rendimiento (solo Administrador).
 * Todos los indicadores se calculan con los filtros aplicados (ver ReporteModel).
 */
$f = $filtros;
$r = $resumen;

// Texto del período
if ($f['desde'] && $f['hasta']) {
    $dias = (int)((strtotime($f['hasta']) - strtotime($f['desde'])) / 86400) + 1;
    $textoPeriodo = fecha_corta($f['desde']) . ' al ' . fecha_corta($f['hasta']) . " ({$dias} " . ($dias === 1 ? 'día' : 'días') . ')';
} elseif ($f['desde']) {
    $textoPeriodo = 'Desde el ' . fecha_corta($f['desde']);
} elseif ($f['hasta']) {
    $textoPeriodo = 'Hasta el ' . fecha_corta($f['hasta']);
} else {
    $textoPeriodo = 'Todo el historial';
}

// Variación de tickets creados vs período anterior
$variacion = null;
if ($creadosAnterior !== null) {
    $variacion = $creadosAnterior > 0 ? (int)round(($r['total'] - $creadosAnterior) / $creadosAnterior * 100) : null;
}

/** Estado de un porcentaje de cumplimiento: [clase, icono, etiqueta] */
$estadoCumplimiento = static function (?float $pct, int $meta): array {
    if ($pct === null) return ['text-muted', 'bi-dash-circle', 'Sin datos'];
    if ($pct >= $meta) return ['text-success', 'bi-check-circle-fill', 'Cumple meta'];
    if ($pct >= $meta - 15) return ['text-warning-emphasis', 'bi-exclamation-circle-fill', 'Bajo la meta'];
    return ['text-danger', 'bi-x-circle-fill', 'Crítico'];
};
$metaSla = 90;
$metaOla = 90;
[$claseSla, $iconoSla, $textoSla] = $estadoCumplimiento($r['sla_cumplimiento'], $metaSla);
[$claseOla, $iconoOla, $textoOla] = $estadoCumplimiento($r['ola_cumplimiento'], $metaOla);

$maxArea      = max(1, ...array_column($porArea, 'total') ?: [1]);
$maxPrioridad = max(1, ...array_column($porPrioridad, 'total') ?: [1]);
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= tema_bootstrap() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - HelpDesk TI</title>
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
        .kpi-valor { font-size: 2rem; font-weight: 700; line-height: 1.1; }
        .kpi-titulo { font-size: .75rem; letter-spacing: .03em; }
        /* Barras de una sola serie: un solo tono, extremo redondeado, valor en texto neutro */
        .barra-pista { height: 8px; background: #e9ecef; border-radius: 4px; }
        .barra-valor { height: 8px; background: #0d6efd; border-radius: 4px; min-width: 2px; }
        .barra-fila:hover .barra-valor { background: #0a58ca; }
        .solo-impresion { display: none; }

        /* Impresión / Guardar como PDF */
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { background: #fff; }
            .sidebar, .topbar, .no-imprimir, .pagination, .alert-dismissible { display: none !important; }
            .main-content { margin-left: 0; }
            .content { padding: 0; }
            .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; break-inside: avoid; }
            .solo-impresion { display: block; }
            a { text-decoration: none; color: inherit; }
        }
    </style>
    <?= view('layout/tema') ?>
</head>
<body>

<?= view('layout/sidebar', ['active' => 'reportes']) ?>

<div class="main-content">
    <?= view('layout/topbar', [
        'pageTitle'    => 'Reportes y Rendimiento',
        // Abre el diálogo de impresión del navegador -> "Guardar como PDF" (usa los estilos @media print)
        'topbarButton' => '
            <button type="button" class="btn btn-primary fw-semibold" onclick="window.print()" title="Guardar el reporte como PDF">
                <i class="bi bi-file-earmark-pdf me-1"></i> Exportar a PDF
            </button>',
    ]) ?>

    <div class="content">

        <!-- Encabezado visible solo al imprimir / PDF -->
        <div class="solo-impresion mb-3">
            <h3 class="fw-bold mb-0">HelpDesk TI — Reporte de Rendimiento</h3>
            <div class="text-muted small">Período: <?= esc($textoPeriodo) ?> · Generado el <?= esc(fecha_texto(date('Y-m-d H:i:s'))) ?> por <?= esc(nombre_con_rol($nombre, $rol)) ?></div>
        </div>

        <!-- Filtros -->
        <form id="filtrosReporte" method="get" action="<?= current_url() ?>" class="card border-0 shadow-sm mb-4 no-imprimir">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Período (fecha de creación)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Desde</span>
                            <input type="date" name="desde" id="repDesde" value="<?= esc($f['desde']) ?>" class="form-control">
                            <span class="input-group-text">Hasta</span>
                            <input type="date" name="hasta" id="repHasta" value="<?= esc($f['hasta']) ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Área</label>
                        <select name="id_area" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <?php foreach ($areas as $a): ?>
                                <option value="<?= esc($a['id_area']) ?>" <?= $f['id_area'] === (int)$a['id_area'] ? 'selected' : '' ?>><?= esc($a['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Técnico</label>
                        <select name="id_tecnico" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <?php foreach ($tecnicos as $tec): ?>
                                <option value="<?= esc($tec['id_usuario']) ?>" <?= $f['id_tecnico'] === (int)$tec['id_usuario'] ? 'selected' : '' ?>>
                                    <?= esc($tec['nombre_completo']) ?><?= $tec['estado'] !== 'activo' ? ' (inactivo)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Prioridad</label>
                        <select name="id_prioridad" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <?php foreach ($prioridades as $p): ?>
                                <option value="<?= esc($p['id_prioridad']) ?>" <?= $f['id_prioridad'] === (int)$p['id_prioridad'] ? 'selected' : '' ?>><?= esc($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2">
                        <a href="<?= current_url() ?>" class="btn btn-light btn-sm fw-semibold flex-fill" title="Volver al mes actual sin filtros"><i class="bi bi-x-circle me-1"></i> Limpiar</a>
                        <button type="submit" class="btn btn-primary btn-sm fw-semibold flex-fill"><i class="bi bi-funnel-fill me-1"></i> Aplicar</button>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-1 mt-2 small">
                    <span class="text-muted me-1">Rápido:</span>
                    <?php foreach (['hoy' => 'Hoy', '7' => 'Últimos 7 días', '30' => 'Últimos 30 días', 'mes' => 'Este mes', 'mes_anterior' => 'Mes anterior', 'anio' => 'Este año', 'todo' => 'Todo el historial'] as $clave => $texto): ?>
                        <button type="button" class="btn btn-link btn-sm p-0 me-2 text-decoration-none rango-reporte" data-rango="<?= $clave ?>"><?= $texto ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="text-muted small">
                <i class="bi bi-calendar-range me-1"></i> Período: <strong class="text-dark"><?= esc($textoPeriodo) ?></strong>
                · <?= $r['total'] ?> ticket(s) en el período
            </div>
        </div>

        <!-- Métricas generales -->
        <h5 class="fw-bold mb-3">Métricas Generales</h5>
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="kpi-titulo text-muted text-uppercase fw-bold mb-2">Tickets Creados</div>
                        <div class="kpi-valor"><?= $r['total'] ?></div>
                        <div class="small text-muted mt-1">
                            <?php if ($creadosAnterior === null): ?>
                                Sin período de comparación
                            <?php elseif ($variacion === null): ?>
                                Período anterior: <?= $creadosAnterior ?> tickets
                            <?php else: ?>
                                <i class="bi <?= $variacion > 0 ? 'bi-arrow-up' : ($variacion < 0 ? 'bi-arrow-down' : 'bi-dash') ?>"></i>
                                <?= abs($variacion) ?>% vs período anterior (<?= $creadosAnterior ?>)
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="kpi-titulo text-muted text-uppercase fw-bold mb-2">Tasa de Resolución</div>
                        <div class="kpi-valor"><?= $r['tasa_resolucion'] !== null ? $r['tasa_resolucion'] . '%' : '—' ?></div>
                        <div class="small text-muted mt-1"><?= $r['resueltos'] + $r['cerrados'] ?> resueltos o cerrados de <?= $r['total'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="kpi-titulo text-muted text-uppercase fw-bold mb-2">Cumplimiento SLA</div>
                        <div class="kpi-valor"><?= $r['sla_cumplimiento'] !== null ? $r['sla_cumplimiento'] . '%' : '—' ?></div>
                        <div class="small mt-1 <?= $claseSla ?>">
                            <i class="bi <?= $iconoSla ?>"></i> <?= $textoSla ?> <span class="text-muted">(meta <?= $metaSla ?>%)</span>
                        </div>
                        <div class="small text-muted"><?= $r['sla_incumplidos'] ?> incumplido(s) de <?= $r['sla_evaluados'] ?> evaluados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="kpi-titulo text-muted text-uppercase fw-bold mb-2">Tickets Abiertos</div>
                        <div class="kpi-valor"><?= $r['pendientes'] + $r['en_proceso'] ?></div>
                        <div class="small text-muted mt-1"><?= $r['pendientes'] ?> pendientes · <?= $r['en_proceso'] ?> en proceso · <?= $r['resueltos'] ?> por confirmar</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicadores OLA -->
        <h5 class="fw-bold mb-3">Indicadores OLA (Operativa Interna)</h5>
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded"><i class="bi bi-stopwatch fs-4 text-primary"></i></div>
                        <div>
                            <div class="kpi-titulo text-muted text-uppercase fw-bold mb-1">Tiempo Medio de Asignación</div>
                            <h3 class="fw-bold mb-0"><?= $r['min_asignacion'] !== null ? formato_minutos($r['min_asignacion']) : '—' ?></h3>
                            <div class="small text-muted">Desde la creación hasta tener técnico</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded"><i class="bi bi-check-circle fs-4 text-success"></i></div>
                        <div>
                            <div class="kpi-titulo text-muted text-uppercase fw-bold mb-1">Tiempo Medio de Resolución</div>
                            <h3 class="fw-bold mb-0"><?= $r['min_resolucion'] !== null ? formato_minutos($r['min_resolucion']) : '—' ?></h3>
                            <div class="small text-muted">Desde la creación hasta la solución</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded"><i class="bi bi-diagram-3 fs-4 text-info"></i></div>
                        <div>
                            <div class="kpi-titulo text-muted text-uppercase fw-bold mb-1">Cumplimiento OLA (Asignación)</div>
                            <h3 class="fw-bold mb-0"><?= $r['ola_cumplimiento'] !== null ? $r['ola_cumplimiento'] . '%' : '—' ?></h3>
                            <div class="small <?= $claseOla ?>"><i class="bi <?= $iconoOla ?>"></i> <?= $textoOla ?> <span class="text-muted">(meta <?= $metaOla ?>%)</span></div>
                            <?php
                                // Plazo OLA por prioridad, calculado desde el catálogo (ej: Urgente 6 h · Alta 12 h ...)
                                $plazosOla = implode(' · ', array_map(
                                    static fn ($p) => $p['nombre'] . ': ' . formato_minutos(intdiv((int)$p['tiempo_sla_minutos'] * $olaPorcentaje, 100)),
                                    $prioridades
                                ));
                            ?>
                            <div class="small text-muted" title="<?= esc($plazosOla) ?>">Asignar dentro del <?= $olaPorcentaje ?>% del SLA <i class="bi bi-info-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded"><i class="bi bi-arrow-left-right fs-4 text-warning"></i></div>
                        <div>
                            <div class="kpi-titulo text-muted text-uppercase fw-bold mb-1">Tickets Escalados</div>
                            <h3 class="fw-bold mb-0"><?= $r['escalados'] ?></h3>
                            <div class="small text-muted">Reasignados a otro técnico</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Tickets por Área Afectada</h5>
                        <?php foreach ($porArea as $a): ?>
                            <?php $pct = $r['total'] ? round($a['total'] / $r['total'] * 100) : 0; ?>
                            <div class="barra-fila mb-3" title="<?= esc($a['nombre']) ?>: <?= $a['total'] ?> ticket(s) · <?= $pct ?>% del total">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span><?= esc($a['nombre']) ?></span>
                                    <span class="fw-semibold"><?= $a['total'] ?> <span class="text-muted fw-normal">(<?= $pct ?>%)</span></span>
                                </div>
                                <div class="barra-pista"><div class="barra-valor" style="width: <?= round($a['total'] / $maxArea * 100) ?>%"></div></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Tickets por Prioridad</h5>
                        <?php foreach ($porPrioridad as $p): ?>
                            <?php $pct = $r['total'] ? round($p['total'] / $r['total'] * 100) : 0; ?>
                            <div class="barra-fila mb-3" title="Prioridad <?= esc($p['nombre']) ?> (SLA <?= formato_minutos((int)$p['sla']) ?>): <?= $p['total'] ?> ticket(s) · <?= $pct ?>% del total">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span><?= badge_prioridad($p['nombre']) ?> <span class="text-muted">SLA <?= formato_minutos((int)$p['sla']) ?></span></span>
                                    <span class="fw-semibold"><?= $p['total'] ?> <span class="text-muted fw-normal">(<?= $pct ?>%)</span></span>
                                </div>
                                <div class="barra-pista"><div class="barra-valor" style="width: <?= round($p['total'] / $maxPrioridad * 100) ?>%"></div></div>
                            </div>
                        <?php endforeach; ?>
                        <div class="small text-muted border-top pt-2 mt-2">
                            Estado actual: <?= $r['pendientes'] ?> pendientes · <?= $r['en_proceso'] ?> en proceso · <?= $r['resueltos'] ?> resueltos · <?= $r['cerrados'] ?> cerrados
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desempeño de técnicos -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Desempeño de Técnicos</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="text-muted small">
                            <tr>
                                <th>Técnico</th>
                                <th class="text-center">Asignados</th>
                                <th class="text-center">En proceso</th>
                                <th class="text-center">Resueltos</th>
                                <th class="text-center">SLA cumplido</th>
                                <th class="text-center">T. medio de resolución</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tecnicosDesempeno)): ?>
                                <?php foreach ($tecnicosDesempeno as $tec): ?>
                                    <?php [$cls, $ico, $txt] = $estadoCumplimiento($tec['sla'] !== null ? (float)$tec['sla'] : null, $metaSla); ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc($tec['nombre']) ?></td>
                                        <td class="text-center"><?= $tec['asignados'] ?></td>
                                        <td class="text-center"><?= $tec['en_proceso'] ?></td>
                                        <td class="text-center"><?= $tec['resueltos'] ?></td>
                                        <td class="text-center <?= $cls ?>" title="<?= $txt ?>">
                                            <i class="bi <?= $ico ?>"></i> <?= $tec['sla'] !== null ? $tec['sla'] . '%' : 'Sin datos' ?>
                                        </td>
                                        <td class="text-center"><?= $tec['min_resolucion'] !== null ? formato_minutos($tec['min_resolucion']) : '—' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center text-muted py-3">No hay técnicos para mostrar.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="small text-muted mt-2">El tiempo de resolución del técnico se mide desde la asignación hasta la solución.</div>
            </div>
        </div>

        <!-- Auditoría de incumplimientos -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Auditoría de Incumplimientos (SLA / OLA)</h5>
                    <span class="small text-muted"><?= $pager->getTotal('auditoria') ?> ticket(s) con incumplimiento</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-muted small">
                            <tr>
                                <th>Ticket</th>
                                <th>Técnico responsable</th>
                                <th>Área</th>
                                <th>Prioridad</th>
                                <th>Incumplimiento</th>
                                <th>Retraso</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($auditoria)): ?>
                                <?php foreach ($auditoria as $t): ?>
                                    <?php foreach (\App\Models\ReporteModel::brechas($t) as $i => $b): ?>
                                    <tr>
                                        <?php if ($i === 0): ?>
                                            <?php $filas = count(\App\Models\ReporteModel::brechas($t)); ?>
                                            <td class="fw-bold" rowspan="<?= $filas ?>">
                                                <a href="<?= base_url('/tickets/detalles/' . $t['id_ticket']) ?>" class="text-decoration-none">#<?= esc($t['codigo_ticket']) ?></a>
                                                <div class="small text-muted fw-normal"><?= esc(fecha_hora($t['fecha_creacion'])) ?></div>
                                            </td>
                                            <td rowspan="<?= $filas ?>"><?= !empty($t['tecnico_nombre']) ? esc($t['tecnico_nombre']) : '<span class="text-muted fst-italic">Sin asignar</span>' ?></td>
                                            <td rowspan="<?= $filas ?>" class="small"><?= esc($t['area_nombre']) ?></td>
                                            <td rowspan="<?= $filas ?>"><?= badge_prioridad($t['prioridad_nombre'], (int)$t['sla_minutos']) ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <span class="badge <?= $b['tipo'] === 'SLA' ? 'text-bg-secondary' : 'text-bg-info' ?>"><?= $b['tipo'] ?></span>
                                            <span class="small"><?= esc($b['detalle']) ?></span>
                                        </td>
                                        <td><span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-2">+<?= formato_minutos($b['minutos']) ?></span></td>
                                        <?php if ($i === 0): ?>
                                            <td rowspan="<?= $filas ?>"><?= esc($t['estado_nombre']) ?></td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-shield-check fs-2 d-block mb-2 text-success"></i>
                                        No hay incumplimientos de SLA ni OLA en el período seleccionado.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($pager->getPageCount('auditoria') > 1): ?>
                    <div class="d-flex justify-content-end mt-3 no-imprimir">
                        <?= $pager->links('auditoria', 'bootstrap') ?>
                    </div>
                <?php endif; ?>
                <div class="small text-muted mt-2">
                    <strong>SLA:</strong> plazo de solución según la prioridad. <strong>OLA:</strong> plazo interno de asignación (<?= $olaPorcentaje ?>% del SLA).
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Rangos rápidos: rellenan las fechas y aplican el filtro
document.querySelectorAll('.rango-reporte').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var hoy = new Date(), desde = new Date(hoy), hasta = new Date(hoy);
        switch (btn.dataset.rango) {
            case 'hoy': break;
            case '7': desde.setDate(hoy.getDate() - 6); break;
            case '30': desde.setDate(hoy.getDate() - 29); break;
            case 'mes': desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1); break;
            case 'mes_anterior':
                desde = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
                hasta = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
                break;
            case 'anio': desde = new Date(hoy.getFullYear(), 0, 1); break;
            case 'todo': desde = null; hasta = null; break;
        }
        var iso = function (d) {
            return d ? d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0') : '';
        };
        document.getElementById('repDesde').value = iso(desde);
        document.getElementById('repHasta').value = iso(hasta);
        document.getElementById('filtrosReporte').submit();
    });
});
</script>
</body>
</html>
