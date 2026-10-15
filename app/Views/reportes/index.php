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
<?= $this->extend('layout/principal') ?>
<?php
$this->setVar('menuActivo', 'reportes');
$this->setVar('topbar', [
        'pageTitle'    => 'Reportes y Rendimiento',
        // Abre el diálogo de impresión del navegador -> "Guardar como PDF" (usa los estilos @media print)
        'topbarButton' => '
            <button type="button" class="btn btn-primary fw-semibold" onclick="window.print()" title="Guardar el reporte como PDF">
                <i class="bi bi-file-earmark-pdf me-1"></i> Exportar a PDF
            </button>',
    ]);
?>

<?= $this->section('titulo') ?>Reportes - HelpDesk TI<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<style>
    .kpi-valor { font-size: 2rem; font-weight: 700; line-height: 1.1; }

    .kpi-titulo { font-size: .75rem; letter-spacing: .03em; }

    /* Barras de una sola serie: un solo tono, extremo redondeado, valor en texto neutro */
    .barra-pista { height: 8px; background: #e9ecef; border-radius: 4px; }

    .barra-valor { height: 8px; background: #0d6efd; border-radius: 4px; min-width: 2px; }

    .barra-fila:hover .barra-valor { background: #0a58ca; }

    .solo-impresion { display: none; }
    .auditoria-impresion { display: none; }

    /* ── Impresión / Guardar como PDF (A4 horizontal) ── */
    @media print {
        @page { size: A4 landscape; margin: 12mm; }
        body { background: #fff !important; }

        /* Barras, badges y colores de prioridad se imprimen aunque "Gráficos de fondo" esté desactivado */
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .sidebar, .topbar, .no-imprimir, .pagination, .alert-dismissible,
        #debug-bar, #debug-icon, #toolbarContainer { display: none !important; }
        .main-content { margin-left: 0; }
        .content { padding: 0; }
        .solo-impresion { display: block; }
        a { text-decoration: none; color: inherit; }

        /* Grilla fija en papel: el ancho A4 activaría 2 columnas; se fuerzan 4 indicadores y 2 tarjetas por fila */
        .row > .col-xl-3 { flex: 0 0 25%; max-width: 25%; }
        .row > .col-lg-6 { flex: 0 0 50%; max-width: 50%; }
        .row.g-4 { --bs-gutter-x: .75rem; --bs-gutter-y: .75rem; }

        /* Alturas naturales: "h-100" + salto de página hacía que el contenido se saliera de su recuadro */
        .h-100 { height: auto !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .card-body.p-4 { padding: 1rem !important; }
        .kpi-valor { font-size: 1.6rem; }

        /* Cortes de página: cada fila de tarjetas va completa y los títulos no quedan solos al final */
        .row, .card { break-inside: avoid; page-break-inside: avoid; }
        h5 { break-after: avoid; page-break-after: avoid; }
        .mb-5 { margin-bottom: 1.25rem !important; }

        /* Tablas largas (técnicos, auditoría): pueden continuar en la página siguiente, sin cortar filas.
           Se imprimen como sección (título + tabla) sin recuadro: así un salto de página nunca deja un
           recuadro vacío al final de una hoja. */
        .card:has(table) { break-inside: auto; page-break-inside: auto; border: none !important; }
        .card:has(table) > .card-body { padding: 0 !important; }
        .card:has(table) h5 { border-bottom: 2px solid #212529; padding-bottom: .35rem; }
        tr { break-inside: avoid; page-break-inside: avoid; }
        thead { display: table-header-group; }

        /* Auditoría: en papel se imprime la lista completa, no solo la página visible */
        .auditoria-pantalla { display: none; }
        .auditoria-impresion { display: table-row-group; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

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
                            <div class="kpi-titulo text-muted text-uppercase fw-bold mb-1">Tiempo Total de Resolución</div>
                            <h3 class="fw-bold mb-0"><?= $r['min_resolucion'] !== null ? formato_minutos($r['min_resolucion']) : '—' ?></h3>
                            <div class="small text-muted">Promedio desde que se crea el ticket hasta su solución</div>
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
                            <div class="small text-muted"><?= $r['ola_incumplidos'] ?> fuera de plazo de <?= $r['ola_evaluados'] ?> evaluados</div>
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
                                <th class="text-center">Tiempo de trabajo del técnico</th>
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
                <div class="small text-muted mt-2"><strong>Tiempo de trabajo del técnico:</strong> promedio desde que se le asigna el ticket hasta que registra la solución. (El <em>tiempo total de resolución</em> de arriba cuenta desde la creación del ticket, incluyendo la espera de asignación.)</div>
            </div>
        </div>

        <!-- Auditoría de incumplimientos -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Auditoría de Incumplimientos (SLA / OLA)</h5>
                    <span class="small text-muted">
                        <?= $pager->getTotal('auditoria') ?> ticket(s) con incumplimiento
                        <?php if ($pager->getTotal('auditoria') > $maxAuditoriaImpresion): ?>
                            <span class="solo-impresion">(se incluyen los primeros <?= $maxAuditoriaImpresion ?>)</span>
                        <?php endif; ?>
                    </span>
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
                        <!-- En pantalla: página actual. En impresión / PDF: la lista completa -->
                        <tbody class="auditoria-pantalla">
                            <?= view('reportes/_filas_auditoria', ['ticketsAuditoria' => $auditoria]) ?>
                        </tbody>
                        <tbody class="auditoria-impresion">
                            <?= view('reportes/_filas_auditoria', ['ticketsAuditoria' => $auditoriaCompleta]) ?>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
<?= $this->endSection() ?>
