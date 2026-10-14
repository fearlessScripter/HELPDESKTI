<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;

/**
 * Consultas agregadas para el módulo de Reportes.
 *
 * Todas respetan los mismos filtros: rango de fecha de creación, área, técnico y prioridad.
 *
 * Definiciones:
 *  - SLA cumplido: el técnico registró la solución antes de fecha_vencimiento_sla.
 *    Un ticket sin solución y con el plazo vencido cuenta como incumplido.
 *  - OLA (acuerdo operativo interno): el administrador debe asignar el ticket dentro del
 *    OLA_PORCENTAJE % del tiempo de SLA de su prioridad (Urgente 24 h -> 6 h, Alta 2 días -> 12 h,
 *    Media 3 días -> 18 h, Baja 5 días -> 30 h).
 *  - Escalados: tickets que fueron reasignados a otro técnico al menos una vez.
 */
class ReporteModel
{
    public const OLA_PORCENTAJE = 25;

    protected $db;
    protected string $ahora;

    public function __construct()
    {
        $this->db    = \Config\Database::connect();
        $this->ahora = date('Y-m-d H:i:s'); // hora de PHP (America/Santiago), no la del servidor MySQL
    }

    /**
     * Aplica los filtros del reporte a una consulta sobre `tickets t`.
     */
    public function aplicarFiltros(BaseBuilder $b, array $f): BaseBuilder
    {
        if (!empty($f['desde'])) {
            $b->where('t.fecha_creacion >=', $f['desde'] . ' 00:00:00');
        }
        if (!empty($f['hasta'])) {
            $b->where('t.fecha_creacion <=', $f['hasta'] . ' 23:59:59');
        }
        if (!empty($f['id_area'])) {
            $b->where('t.id_area_afectada', $f['id_area']);
        }
        if (!empty($f['id_tecnico'])) {
            $b->where('t.id_tecnico_asignado', $f['id_tecnico']);
        }
        if (!empty($f['id_prioridad'])) {
            $b->where('t.id_prioridad', $f['id_prioridad']);
        }
        return $b;
    }

    /** Minutos de OLA de la prioridad del ticket (expresión SQL). */
    private function sqlMinutosOla(string $tablaPrioridades = 'p'): string
    {
        return "({$tablaPrioridades}.tiempo_sla_minutos * " . self::OLA_PORCENTAJE . ' / 100)';
    }

    /**
     * Indicadores principales del período.
     */
    public function resumen(array $f): array
    {
        $ahora = $this->db->escape($this->ahora);
        $ola   = $this->sqlMinutosOla();

        $b = $this->db->table('tickets t')
            ->join('prioridades p', 'p.id_prioridad = t.id_prioridad', 'left')
            ->select("
                COUNT(*) AS total,
                SUM(t.id_estado = 1) AS pendientes,
                SUM(t.id_estado = 2) AS en_proceso,
                SUM(t.id_estado = 3) AS resueltos,
                SUM(t.id_estado = 4) AS cerrados,
                AVG(TIMESTAMPDIFF(MINUTE, t.fecha_creacion, t.fecha_asignacion)) AS min_asignacion,
                AVG(TIMESTAMPDIFF(MINUTE, t.fecha_creacion, t.fecha_resolucion)) AS min_resolucion,
                SUM(t.fecha_resolucion IS NOT NULL AND t.fecha_resolucion <= t.fecha_vencimiento_sla) AS sla_cumplidos,
                SUM(t.fecha_resolucion IS NOT NULL AND t.fecha_resolucion > t.fecha_vencimiento_sla) AS sla_resueltos_tarde,
                SUM(t.fecha_resolucion IS NULL AND t.fecha_vencimiento_sla < {$ahora}) AS sla_vencidos_abiertos,
                SUM(t.fecha_asignacion IS NOT NULL AND TIMESTAMPDIFF(MINUTE, t.fecha_creacion, t.fecha_asignacion) <= {$ola}) AS ola_cumplidos,
                SUM(t.fecha_asignacion IS NOT NULL) AS asignados
            ", false);

        $r = $this->aplicarFiltros($b, $f)->get()->getRowArray() ?? [];
        $r = array_map(static fn ($v) => $v === null ? null : (float)$v, $r);

        $evaluadosSla = ($r['sla_cumplidos'] ?? 0) + ($r['sla_resueltos_tarde'] ?? 0) + ($r['sla_vencidos_abiertos'] ?? 0);
        $total        = (int)($r['total'] ?? 0);

        return [
            'total'              => $total,
            'pendientes'         => (int)$r['pendientes'],
            'en_proceso'         => (int)$r['en_proceso'],
            'resueltos'          => (int)$r['resueltos'],
            'cerrados'           => (int)$r['cerrados'],
            'tasa_resolucion'    => $total ? round((($r['resueltos'] + $r['cerrados']) / $total) * 100) : null,
            'min_asignacion'     => $r['min_asignacion'] !== null ? (int)round($r['min_asignacion']) : null,
            'min_resolucion'     => $r['min_resolucion'] !== null ? (int)round($r['min_resolucion']) : null,
            'sla_evaluados'      => (int)$evaluadosSla,
            'sla_cumplimiento'   => $evaluadosSla ? round(($r['sla_cumplidos'] / $evaluadosSla) * 100) : null,
            'sla_incumplidos'    => (int)(($r['sla_resueltos_tarde'] ?? 0) + ($r['sla_vencidos_abiertos'] ?? 0)),
            'ola_cumplimiento'   => !empty($r['asignados']) ? round(($r['ola_cumplidos'] / $r['asignados']) * 100) : null,
            'asignados'          => (int)$r['asignados'],
            'escalados'          => $this->escalados($f),
        ];
    }

    /**
     * Cantidad de tickets creados en un rango (para comparar con el período anterior).
     */
    public function contarCreados(array $f): int
    {
        return (int)$this->aplicarFiltros($this->db->table('tickets t'), $f)->countAllResults();
    }

    /**
     * Tickets reasignados al menos una vez (escalados).
     */
    public function escalados(array $f): int
    {
        $b = $this->db->table('tickets t')
            ->select('COUNT(DISTINCT t.id_ticket) AS n', false)
            ->join('historial_tickets h', "h.id_ticket = t.id_ticket AND h.accion = 'Ticket Reasignado'");

        return (int)($this->aplicarFiltros($b, $f)->get()->getRow()->n ?? 0);
    }

    /**
     * Tickets por área afectada (incluye áreas activas sin tickets).
     */
    public function porArea(array $f): array
    {
        $b = $this->db->table('tickets t')
            ->select('t.id_area_afectada AS id_area, COUNT(*) AS total')
            ->groupBy('t.id_area_afectada');
        $conteos = array_column($this->aplicarFiltros($b, $f)->get()->getResultArray(), 'total', 'id_area');

        $areas = $this->db->table('areas_departamentos')->where('estado', 'activo')->orderBy('nombre')->get()->getResultArray();
        $filas = array_map(static fn ($a) => [
            'nombre' => $a['nombre'],
            'total'  => (int)($conteos[$a['id_area']] ?? 0),
        ], $areas);

        usort($filas, static fn ($x, $y) => $y['total'] <=> $x['total']);
        return $filas;
    }

    /**
     * Tickets por prioridad (Alta, Media, Baja).
     */
    public function porPrioridad(array $f): array
    {
        $b = $this->db->table('tickets t')
            ->select('t.id_prioridad, COUNT(*) AS total')
            ->groupBy('t.id_prioridad');
        $conteos = array_column($this->aplicarFiltros($b, $f)->get()->getResultArray(), 'total', 'id_prioridad');

        $prioridades = $this->db->table('prioridades')->orderBy('id_prioridad', 'DESC')->get()->getResultArray();
        return array_map(static fn ($p) => [
            'nombre' => $p['nombre'],
            'sla'    => (int)$p['tiempo_sla_minutos'],
            'total'  => (int)($conteos[$p['id_prioridad']] ?? 0),
        ], $prioridades);
    }

    /**
     * Desempeño por técnico (incluye técnicos activos sin tickets en el período).
     */
    public function desempenoTecnicos(array $f): array
    {
        $ahora = $this->db->escape($this->ahora);

        $b = $this->db->table('tickets t')
            ->select("
                t.id_tecnico_asignado AS id_tecnico,
                COUNT(*) AS asignados,
                SUM(t.id_estado = 2) AS en_proceso,
                SUM(t.id_estado IN (3, 4)) AS resueltos,
                SUM(t.fecha_resolucion IS NOT NULL AND t.fecha_resolucion <= t.fecha_vencimiento_sla) AS sla_ok,
                SUM(t.fecha_resolucion IS NOT NULL AND t.fecha_resolucion > t.fecha_vencimiento_sla)
                  + SUM(t.fecha_resolucion IS NULL AND t.fecha_vencimiento_sla < {$ahora}) AS sla_mal,
                AVG(TIMESTAMPDIFF(MINUTE, t.fecha_asignacion, t.fecha_resolucion)) AS min_resolucion
            ", false)
            ->where('t.id_tecnico_asignado IS NOT NULL')
            ->groupBy('t.id_tecnico_asignado');
        $datos = [];
        foreach ($this->aplicarFiltros($b, $f)->get()->getResultArray() as $fila) {
            $datos[$fila['id_tecnico']] = $fila;
        }

        $tecnicos = $this->db->table('usuarios')->select('id_usuario, nombre_completo, estado')
            ->where('id_rol', 2)->orderBy('nombre_completo')->get()->getResultArray();

        $filas = [];
        foreach ($tecnicos as $tec) {
            $d = $datos[$tec['id_usuario']] ?? null;
            // Técnicos inactivos solo aparecen si tienen tickets en el período
            if (!$d && $tec['estado'] !== 'activo') {
                continue;
            }
            if (!empty($f['id_tecnico']) && (int)$f['id_tecnico'] !== (int)$tec['id_usuario']) {
                continue;
            }
            $evaluados = $d ? (int)$d['sla_ok'] + (int)$d['sla_mal'] : 0;
            $filas[] = [
                'nombre'         => $tec['nombre_completo'],
                'asignados'      => (int)($d['asignados'] ?? 0),
                'en_proceso'     => (int)($d['en_proceso'] ?? 0),
                'resueltos'      => (int)($d['resueltos'] ?? 0),
                'sla'            => $evaluados ? (int)round($d['sla_ok'] / $evaluados * 100) : null,
                'min_resolucion' => ($d['min_resolucion'] ?? null) !== null ? (int)round($d['min_resolucion']) : null,
            ];
        }

        usort($filas, static fn ($x, $y) => $y['resueltos'] <=> $x['resueltos'] ?: $y['asignados'] <=> $x['asignados']);
        return $filas;
    }

    /**
     * Condición SQL de "ticket con incumplimiento" (SLA o OLA), para la auditoría.
     * Se usa sobre TicketModel::getTicketsQuery() (tablas sin alias: tickets, prioridades).
     */
    public function sqlIncumplimiento(): string
    {
        $ahora = $this->db->escape($this->ahora);
        $ola   = $this->sqlMinutosOla('prioridades');

        return "(
            (tickets.fecha_resolucion IS NOT NULL AND tickets.fecha_resolucion > tickets.fecha_vencimiento_sla)
            OR (tickets.fecha_resolucion IS NULL AND tickets.fecha_vencimiento_sla < {$ahora})
            OR (tickets.fecha_asignacion IS NOT NULL AND TIMESTAMPDIFF(MINUTE, tickets.fecha_creacion, tickets.fecha_asignacion) > {$ola})
            OR (tickets.fecha_asignacion IS NULL AND TIMESTAMPDIFF(MINUTE, tickets.fecha_creacion, {$ahora}) > {$ola})
        )";
    }

    /**
     * Detalle de incumplimientos de un ticket: lista de ['tipo' => 'SLA'|'OLA', 'detalle', 'minutos'].
     */
    public static function brechas(array $t): array
    {
        $ahora    = time();
        $creado   = strtotime($t['fecha_creacion']);
        $minSla   = (int)($t['sla_minutos'] ?? 0);
        $minOla   = (int)floor($minSla * self::OLA_PORCENTAJE / 100);
        $brechas  = [];

        // SLA: resolución tardía o vencido sin resolver
        if (!empty($t['fecha_vencimiento_sla'])) {
            $vence = strtotime($t['fecha_vencimiento_sla']);
            $fin   = !empty($t['fecha_resolucion']) ? strtotime($t['fecha_resolucion']) : $ahora;
            if ($fin > $vence) {
                $brechas[] = [
                    'tipo'    => 'SLA',
                    'detalle' => !empty($t['fecha_resolucion']) ? 'Resuelto fuera de plazo' : 'Vencido sin resolver',
                    'minutos' => intdiv($fin - $vence, 60),
                ];
            }
        }

        // OLA: asignación tardía (o sigue sin asignar pasado el plazo)
        if ($minOla > 0) {
            $asignado = !empty($t['fecha_asignacion']) ? strtotime($t['fecha_asignacion']) : $ahora;
            $limite   = $creado + $minOla * 60;
            if ($asignado > $limite) {
                $brechas[] = [
                    'tipo'    => 'OLA',
                    'detalle' => !empty($t['fecha_asignacion']) ? 'Asignado fuera de plazo' : 'Sin asignar fuera de plazo',
                    'minutos' => intdiv($asignado - $limite, 60),
                ];
            }
        }

        return $brechas;
    }
}
