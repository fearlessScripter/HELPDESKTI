<?php

/**
 * Helpers de SLA. Los tiempos se manejan internamente en minutos enteros
 * y se formatean como "1 día, 3 h 20 min" solo al mostrarlos.
 */

if (!function_exists('formato_minutos')) {
    /**
     * 30 -> "30 min", 135 -> "2 h 15 min", 240 -> "4 h", 1640 -> "1 día, 3 h 20 min"
     */
    function formato_minutos(int $minutos): string
    {
        $minutos = max(0, $minutos);
        $dias  = intdiv($minutos, 1440);
        $horas = intdiv($minutos % 1440, 60);
        $mins  = $minutos % 60;

        $tiempo = [];
        if ($horas > 0) {
            $tiempo[] = $horas . ' h';
        }
        if ($mins > 0 || ($dias === 0 && $horas === 0)) {
            $tiempo[] = $mins . ' min';
        }

        $texto = implode(' ', $tiempo);
        if ($dias > 0) {
            $textoDias = $dias . ($dias === 1 ? ' día' : ' días');
            $texto = $texto === '' ? $textoDias : $textoDias . ', ' . $texto;
        }

        return $texto;
    }
}

if (!function_exists('sla_estado')) {
    /**
     * Calcula el estado del SLA de un ticket (en minutos).
     * El reloj se detiene cuando el técnico registra la solución (fecha_resolucion).
     *
     * @return array{minutos_totales:int, minutos_transcurridos:int, minutos_restantes:int, porcentaje:int, vencido:bool, detenido:bool}|null
     */
    function sla_estado(array $ticket): ?array
    {
        if (empty($ticket['fecha_vencimiento_sla']) || empty($ticket['fecha_creacion'])) {
            return null;
        }

        $creado   = strtotime($ticket['fecha_creacion']);
        $vence    = strtotime($ticket['fecha_vencimiento_sla']);
        $detenido = !empty($ticket['fecha_resolucion']);
        $ahora    = $detenido ? strtotime($ticket['fecha_resolucion']) : time();

        $totales       = max(1, intdiv($vence - $creado, 60));
        $transcurridos = max(0, intdiv($ahora - $creado, 60));
        $restantes     = intdiv($vence - $ahora, 60); // negativo si está vencido

        return [
            'minutos_totales'       => $totales,
            'minutos_transcurridos' => $transcurridos,
            'minutos_restantes'     => $restantes,
            'porcentaje'            => (int)min(100, round($transcurridos / $totales * 100)),
            'vencido'               => $ahora > $vence,
            'detenido'              => $detenido,
        ];
    }
}

if (!function_exists('sla_restante_html')) {
    /**
     * Texto corto para tablas: "Quedan 3 h 10 min" / "Vencido hace 25 min" / estado final.
     */
    function sla_restante_html(array $ticket): string
    {
        $sla = sla_estado($ticket);
        if ($sla === null) {
            return '<span class="text-muted small">—</span>';
        }

        if ($sla['detenido']) {
            return $sla['vencido']
                ? '<span class="text-danger small"><i class="bi bi-check-circle"></i> Resuelto fuera de SLA</span>'
                : '<span class="text-success small"><i class="bi bi-check-circle"></i> Resuelto dentro de SLA</span>';
        }

        if ($sla['vencido']) {
            return '<span class="text-danger fw-bold small"><i class="bi bi-exclamation-triangle"></i> Vencido hace '
                . formato_minutos(-$sla['minutos_restantes']) . '</span>';
        }

        $clase = $sla['porcentaje'] > 75 ? 'text-warning-emphasis fw-semibold' : 'text-success';
        return '<span class="' . $clase . ' small"><i class="bi bi-clock"></i> Quedan '
            . formato_minutos($sla['minutos_restantes']) . '</span>';
    }
}
