<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Ticket <?= esc($ticket['codigo_ticket'] ?? '#TK-1050') ?> - HelpDesk TI</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
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
        .timeline { position: relative; padding-left: 20px; border-left: 2px solid #dee2e6; margin-left: 10px; }
        .timeline-item { position: relative; margin-bottom: 20px; }
        .timeline-item::before { content: ''; position: absolute; left: -26px; top: 4px; width: 12px; height: 12px; border-radius: 50%; background-color: #0d6efd; border: 2px solid white; }
    </style>
</head>
<body>

<?= view('layout/sidebar', ['active' => 'tickets']) ?>

<div class="main-content">

    <?= view('layout/topbar', ['pageTitle' => 'Detalles del Ticket #' . esc($ticket['codigo_ticket'] ?? 'TK-' . $ticket['id_ticket'])]) ?>

    <div class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <!-- Columna Izquierda: Información del Problema y Trazabilidad -->
                <div class="col-md-7">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white pt-4 pb-3 border-bottom text-start">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="fw-bold mb-0"><?= esc($ticket['asunto']) ?></h4>
                                <?php if ($ticket['estado_nombre'] === 'Pendiente'): ?>
                                    <span class="badge bg-danger fs-6">Pendiente</span>
                                <?php elseif ($ticket['estado_nombre'] === 'En Proceso'): ?>
                                    <span class="badge bg-warning text-dark fs-6">En Proceso</span>
                                <?php elseif ($ticket['estado_nombre'] === 'Resuelto'): ?>
                                    <span class="badge bg-success fs-6">Resuelto</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary fs-6">Cerrado</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bi bi-clock"></i> Creado el <?= esc(fecha_texto($ticket['fecha_creacion'])) ?> por <strong><?= esc($ticket['solicitante_nombre']) ?></strong> (<?= esc($ticket['solicitante_rol']) ?>)
                            </p>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Descripción del Problema:</h6>
                            <p class="text-secondary" style="line-height: 1.6; white-space: pre-line;">
                                <?= esc($ticket['descripcion']) ?>
                            </p>

                            <?php if (!empty($ticket['solucion_aplicada'])): ?>
                            <hr class="my-4">
                            <div class="alert alert-success border-0 shadow-sm mb-0">
                                <h6 class="fw-bold mb-2"><i class="bi bi-check-circle-fill me-2 text-success"></i>Solución Técnica Aplicada:</h6>
                                <p class="mb-0 text-dark" style="white-space: pre-line;"><?= esc($ticket['solucion_aplicada']) ?></p>
                                <?php if (!empty($ticket['fecha_resolucion'])): ?>
                                <small class="text-muted d-block mt-2">
                                    Registrada el <?= esc(fecha_texto($ticket['fecha_resolucion'])) ?>
                                    <?php if (!empty($ticket['tecnico_nombre'])): ?>
                                        por <?= esc(nombre_con_rol($ticket['tecnico_nombre'], 'Técnico')) ?>
                                    <?php endif; ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Trazabilidad / Historial ITIL -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white pt-3 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Historial de Acciones (Trazabilidad)</h6>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($historial)): ?>
                                <div class="timeline">
                                    <?php foreach ($historial as $h): ?>
                                    <div class="timeline-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong><?= esc($h['accion']) ?></strong>
                                            <small class="text-muted"><?= esc(fecha_hora($h['fecha_registro'])) ?></small>
                                        </div>
                                        <p class="mb-1 text-secondary small"><?= esc($h['descripcion']) ?></p>
                                        <small class="text-muted">Por: <?= esc(nombre_con_rol($h['usuario_nombre'], $h['usuario_rol'])) ?></small>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">No hay registros de historial.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Información Adicional y Flujo de Solución -->
                <div class="col-md-5">
                    <div class="card shadow-sm border-0 mb-4 bg-light">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Información del Ticket</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><strong>Código:</strong> #<?= esc($ticket['codigo_ticket']) ?></li>
                                <li class="mb-2"><strong>Área Afectada:</strong> <?= esc($ticket['area_nombre']) ?></li>
                                <li class="mb-2"><strong>Impacto / Urgencia:</strong> <?= ucfirst(esc($ticket['nivel_impacto'])) ?> / <?= ucfirst(esc($ticket['nivel_urgencia'])) ?></li>
                                <?php $verSla = ($rol !== 'Solicitante'); // El solicitante no ve información de SLA ?>
                                <li class="mb-2"><strong>Prioridad:</strong>
                                    <?php // El solicitante ve la prioridad (con su color) pero no el plazo SLA ?>
                                    <?= badge_prioridad($ticket['prioridad_nombre'], $verSla ? (int)$ticket['sla_minutos'] : null, 'SLA ') ?>
                                </li>
                                <li class="mb-2"><strong>Técnico Asignado:</strong>
                                    <?= !empty($ticket['tecnico_nombre']) ? esc($ticket['tecnico_nombre']) : '<span class="text-danger fw-semibold">Sin asignar (En cola)</span>' ?>
                                </li>
                                <?php if ($verSla && !empty($ticket['fecha_vencimiento_sla'])): ?>
                                <li class="mb-2"><strong>Vencimiento SLA:</strong> <?= esc(fecha_hora($ticket['fecha_vencimiento_sla'])) ?></li>
                                <?php endif; ?>
                            </ul>

                            <?php // Control de SLA (solo Técnico y Administrador)
                                  $sla = $verSla ? sla_estado($ticket) : null; ?>
                            <?php if ($sla !== null): ?>
                                <?php $colorBarra = $sla['vencido'] ? 'bg-danger' : ($sla['porcentaje'] > 75 ? 'bg-warning' : 'bg-success'); ?>
                                <div class="mt-4 pt-3 border-top" id="slaControl"
                                     data-vence="<?= esc(strtotime($ticket['fecha_vencimiento_sla']) * 1000) ?>"
                                     data-creado="<?= esc(strtotime($ticket['fecha_creacion']) * 1000) ?>"
                                     data-detenido="<?= $sla['detenido'] ? '1' : '0' ?>">
                                    <h6 class="fw-bold mb-2">Control de SLA</h6>
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span id="slaRestante">
                                            <?php if ($sla['detenido']): ?>
                                                <?= $sla['vencido']
                                                    ? '<strong class="text-danger">Resuelto fuera de SLA</strong>'
                                                    : '<strong class="text-success">Resuelto dentro de SLA</strong>' ?>
                                            <?php elseif ($sla['vencido']): ?>
                                                <strong class="text-danger">¡SLA vencido hace <?= formato_minutos(-$sla['minutos_restantes']) ?>!</strong>
                                            <?php else: ?>
                                                Tiempo restante: <strong><?= formato_minutos($sla['minutos_restantes']) ?></strong>
                                            <?php endif; ?>
                                        </span>
                                        <span class="text-muted" id="slaPorcentaje"><?= $sla['porcentaje'] ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar <?= $colorBarra ?>" id="slaBarra" role="progressbar" style="width: <?= $sla['porcentaje'] ?>%;"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1 small text-muted">
                                        <span>Transcurrido: <span id="slaTranscurrido"><?= formato_minutos($sla['minutos_transcurridos']) ?></span></span>
                                        <span>Total: <?= formato_minutos($sla['minutos_totales']) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Botón de Asignación / Reasignación para Administrador -->
                            <?php if ($rol === 'Administrador' && in_array((int)$ticket['id_estado'], [1, 2], true)): ?>
                                <div class="mt-3 pt-3 border-top">
                                    <button class="btn btn-outline-primary btn-sm w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalReasignar">
                                        <i class="bi bi-person-gear me-1"></i> <?= empty($ticket['id_tecnico_asignado']) ? 'Asignar Técnico' : 'Reasignar Técnico' ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ACCIÓN: Formulario de Solución (solo el técnico asignado, cuando el ticket está En Proceso) -->
                    <?php
                        $puedeResolver = (int)$ticket['id_estado'] === 2
                            && $rol === 'Técnico' && (int)$ticket['id_tecnico_asignado'] === (int)$id_usuario;
                    ?>
                    <?php if ((int)$ticket['id_estado'] === 2 && !$puedeResolver): ?>
                    <div class="alert alert-info border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-tools fs-3 text-info me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">En Proceso</h6>
                                <p class="small mb-0 text-dark">
                                    <?= esc(nombre_con_rol($ticket['tecnico_nombre'], 'Técnico')) ?> está trabajando en la solución.
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($puedeResolver): ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white pt-3 pb-2 border-bottom text-start">
                            <h6 class="fw-bold mb-0 text-success"><i class="bi bi-tools me-2"></i>Aplicar Solución Técnica</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?= base_url('/tickets/resolver') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_ticket" value="<?= esc($ticket['id_ticket']) ?>">
                                
                                <small class="text-muted mb-3 d-block">
                                    <i class="bi bi-info-circle"></i> Al registrar la solución, el ticket pasará al estado <strong>Resuelto</strong> y se notificará al solicitante para su validación final.
                                </small>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="solucion_aplicada">Detalle la solución aplicada <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="solucion_aplicada" name="solucion_aplicada" rows="4" placeholder="Ej: Se reemplazó el cable de red y se reconfiguró la IP estática del equipo..." required></textarea>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success fw-bold">
                                        <i class="bi bi-check-circle me-1"></i> Marcar como Resuelto
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ACCIÓN: Validación y Cierre (Para el Solicitante o Admin cuando el ticket está Resuelto) -->
                    <?php if ((int)$ticket['id_estado'] === 3): ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white pt-3 pb-2 border-bottom text-start">
                            <h6 class="fw-bold mb-0 text-success"><i class="bi bi-check2-circle me-2"></i>Confirmación de Cierre del Ticket</h6>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-3">
                                El técnico ha indicado que el problema fue solucionado. Si confirmas que todo funciona correctamente, procede a cerrar el ticket.
                            </p>
                            
                            <?php // Solo quien creó el ticket puede confirmar y cerrarlo ?>
                            <?php if ((int)$ticket['id_solicitante'] === (int)$id_usuario): ?>
                                <form action="<?= base_url('/tickets/cerrar') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_ticket" value="<?= esc($ticket['id_ticket']) ?>">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success fw-bold btn-lg">
                                            <i class="bi bi-check-all me-1"></i> Confirmar y Cerrar Ticket
                                        </button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-info small mb-0">
                                    <i class="bi bi-hourglass-split me-1"></i> Esperando que el solicitante (<strong><?= esc($ticket['solicitante_nombre']) ?></strong>) confirme y cierre el ticket.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ESTADO FINAL: Ticket Cerrado -->
                    <?php if ((int)$ticket['id_estado'] === 4): ?>
                    <div class="alert alert-secondary border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check fs-2 text-success me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Ticket Finalizado y Cerrado</h6>
                                <p class="small mb-0 text-muted">Cerrado el <?= esc(fecha_texto($ticket['fecha_cierre'])) ?> por <?= esc(nombre_con_rol($ticket['solicitante_nombre'], $ticket['solicitante_rol'])) ?>. La solución ha sido archivada en la base de conocimientos.</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ESTADO INICIAL: Ticket Pendiente -->
                    <?php if ((int)$ticket['id_estado'] === 1): ?>
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hourglass-top fs-3 text-warning me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">En Cola de Espera</h6>
                                <p class="small mb-0 text-dark">El ticket está pendiente de asignación por parte del administrador.</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            
            <div class="mt-2">
                <a href="<?= base_url('/tickets/mis-tickets') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a Mis Tickets
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Modal Asignar / Reasignar (Admin) -->
<?php if ($rol === 'Administrador'): ?>
<div class="modal fade" id="modalReasignar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('/tickets/asignar') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_ticket" value="<?= esc($ticket['id_ticket']) ?>">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Asignar / Reasignar Técnico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Seleccionar Técnico</label>
                        <select class="form-select" name="id_tecnico" required>
                            <option value="" disabled selected>Selecciona un técnico...</option>
                            <?php if (!empty($tecnicos)): ?>
                                <?php foreach ($tecnicos as $tec): ?>
                                    <option value="<?= esc($tec['id_usuario']) ?>" <?= ((int)($ticket['id_tecnico_asignado'] ?? 0) === (int)$tec['id_usuario']) ? 'selected' : '' ?>>
                                        <?= esc($tec['nombre_completo']) ?> (<?= esc($tec['area'] ?? 'Soporte TI') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Asignación</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($sla !== null && !$sla['detenido']): ?>
<!-- Refresco del SLA cada minuto sin recargar (el cálculo oficial se hace en el servidor en cada F5) -->
<script>
(function () {
    var box = document.getElementById('slaControl');
    if (!box || box.dataset.detenido === '1') return;

    var vence = parseInt(box.dataset.vence, 10);
    var creado = parseInt(box.dataset.creado, 10);

    function formatoMinutos(min) {
        min = Math.max(0, Math.floor(min));
        var dias = Math.floor(min / 1440), horas = Math.floor((min % 1440) / 60), mins = min % 60;
        var partes = [];
        if (horas > 0) partes.push(horas + ' h');
        if (mins > 0 || (dias === 0 && horas === 0)) partes.push(mins + ' min');
        var texto = partes.join(' ');
        if (dias > 0) {
            var d = dias + (dias === 1 ? ' día' : ' días');
            texto = texto === '' ? d : d + ', ' + texto;
        }
        return texto;
    }

    function actualizar() {
        var ahora = Date.now();
        var total = Math.max(1, Math.floor((vence - creado) / 60000));
        var transcurrido = Math.max(0, Math.floor((ahora - creado) / 60000));
        var restante = Math.trunc((vence - ahora) / 60000);
        var porcentaje = Math.min(100, Math.round(transcurrido / total * 100));
        var vencido = ahora > vence;

        document.getElementById('slaRestante').innerHTML = vencido
            ? '<strong class="text-danger">¡SLA vencido hace ' + formatoMinutos(-restante) + '!</strong>'
            : 'Tiempo restante: <strong>' + formatoMinutos(restante) + '</strong>';
        document.getElementById('slaTranscurrido').textContent = formatoMinutos(transcurrido);
        document.getElementById('slaPorcentaje').textContent = porcentaje + '%';

        var barra = document.getElementById('slaBarra');
        barra.style.width = porcentaje + '%';
        barra.className = 'progress-bar ' + (vencido ? 'bg-danger' : (porcentaje > 75 ? 'bg-warning' : 'bg-success'));
    }

    setInterval(actualizar, 60000);
})();
</script>
<?php endif; ?>
</body>
</html>
