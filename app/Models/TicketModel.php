<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table            = 'tickets';
    protected $primaryKey       = 'id_ticket';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'codigo_ticket',
        'id_solicitante',
        'id_tecnico_asignado',
        'id_area_afectada',
        'nivel_impacto',
        'nivel_urgencia',
        'id_prioridad',
        'id_estado',
        'asunto',
        'descripcion',
        'solucion_aplicada',
        'fecha_creacion',
        'fecha_asignacion',
        'fecha_resolucion',
        'fecha_cierre',
        'fecha_vencimiento_sla',
    ];

    /**
     * Calcula la prioridad y minutos de SLA basándose en Impacto, Urgencia y Área afectada.
     * Retorna id_prioridad, nombre y minutos de SLA (leídos de la tabla prioridades).
     */
    public function calcularPrioridad(string $impacto, string $urgencia, int $idArea): array
    {
        $valoresImpacto = ['bajo' => 1, 'medio' => 2, 'alto' => 3];
        $valoresUrgencia = ['baja' => 1, 'media' => 2, 'alta' => 3];
        
        $pesosArea = [
            1 => 1, // Soporte TI
            2 => 2, // Redes
            3 => 3, // ERP
        ];

        $vImpacto = $valoresImpacto[strtolower($impacto)] ?? 1;
        $vUrgencia = $valoresUrgencia[strtolower($urgencia)] ?? 1;
        $vArea    = $pesosArea[$idArea] ?? 1;

        $puntaje = $vImpacto + $vUrgencia + $vArea; // Rango 3 - 9

        // Impacto alto + urgencia alta (toda la empresa detenida) siempre es Urgente
        if ($vImpacto === 3 && $vUrgencia === 3) {
            $idPrioridad = 4; // Urgente (24 h)
        } elseif ($puntaje >= 8) {
            $idPrioridad = 4; // Urgente (24 h)
        } elseif ($puntaje >= 7) {
            $idPrioridad = 3; // Alta (2 días)
        } elseif ($puntaje >= 5) {
            $idPrioridad = 2; // Media (3 días)
        } else {
            $idPrioridad = 1; // Baja (5 días)
        }

        $prioridad = (new PrioridadModel())->find($idPrioridad);

        return [
            'id_prioridad' => $idPrioridad,
            'nombre'       => $prioridad['nombre'] ?? 'Baja',
            'minutos_sla'  => (int)($prioridad['tiempo_sla_minutos'] ?? 7200),
        ];
    }

    /**
     * Genera un código autoincremental tipo TK-1001, TK-1002...
     */
    public function generarCodigo(): string
    {
        $ultimo = $this->orderBy('id_ticket', 'DESC')->first();
        $siguiente = 1001;
        if ($ultimo && !empty($ultimo['codigo_ticket']) && preg_match('/TK-(\d+)/', $ultimo['codigo_ticket'], $matches)) {
            $siguiente = (int)$matches[1] + 1;
        } elseif ($ultimo && !empty($ultimo['id_ticket'])) {
            $siguiente = 1000 + (int)$ultimo['id_ticket'] + 1;
        }
        return 'TK-' . $siguiente;
    }

    /**
     * Consulta con todas las relaciones (solicitante, técnico, área, prioridad, estado)
     */
    public function getTicketsQuery()
    {
        return $this->select('
                tickets.*,
                sol.nombre_completo as solicitante_nombre,
                sol.correo as solicitante_correo,
                rol_sol.nombre as solicitante_rol,
                tec.nombre_completo as tecnico_nombre,
                tec.correo as tecnico_correo,
                areas_departamentos.nombre as area_nombre,
                prioridades.nombre as prioridad_nombre,
                prioridades.tiempo_sla_minutos as sla_minutos,
                estados_ticket.nombre as estado_nombre
            ')
            ->join('usuarios as sol', 'sol.id_usuario = tickets.id_solicitante')
            ->join('roles as rol_sol', 'rol_sol.id_rol = sol.id_rol')
            ->join('usuarios as tec', 'tec.id_usuario = tickets.id_tecnico_asignado', 'left')
            ->join('areas_departamentos', 'areas_departamentos.id_area = tickets.id_area_afectada')
            ->join('prioridades', 'prioridades.id_prioridad = tickets.id_prioridad', 'left')
            ->join('estados_ticket', 'estados_ticket.id_estado = tickets.id_estado');
    }

    /**
     * Obtiene un ticket completo por su ID o código
     */
    public function getTicketCompleto($idOrCodigo)
    {
        $query = $this->getTicketsQuery();
        if (is_numeric($idOrCodigo)) {
            return $query->where('tickets.id_ticket', (int)$idOrCodigo)->first();
        }
        return $query->where('tickets.codigo_ticket', $idOrCodigo)->first();
    }

    /**
     * ¿Puede este usuario ver el ticket?
     * Administrador: todos. Resto: solo si lo creó o si es el técnico asignado ACTUALMENTE
     * (un técnico al que se le reasignó el ticket pierde el acceso).
     */
    public static function usuarioPuedeVer(array $ticket, int $idUsuario, string $rol): bool
    {
        if ($rol === 'Administrador') {
            return true;
        }
        return (int)$ticket['id_solicitante'] === $idUsuario
            || (int)($ticket['id_tecnico_asignado'] ?? 0) === $idUsuario;
    }

    /**
     * Opciones de orden disponibles en los listados (clave => [etiqueta, columnas]).
     */
    public const ORDENES = [
        'recientes' => ['Más recientes primero', [['tickets.fecha_creacion', 'DESC']]],
        'antiguos'  => ['Más antiguos primero', [['tickets.fecha_creacion', 'ASC']]],
        'prioridad' => ['Mayor prioridad primero', [['tickets.id_prioridad', 'DESC'], ['tickets.fecha_creacion', 'ASC']]],
        'sla'       => ['Próximos a vencer (SLA)', [['tickets.fecha_vencimiento_sla', 'ASC']]],
    ];

    /**
     * Tickets en cola (Pendientes y sin asignar). Devuelve la consulta para filtrar/paginar.
     */
    public function queryCola()
    {
        return $this->getTicketsQuery()
                    ->where('tickets.id_estado', 1) // Pendiente
                    ->where('tickets.id_tecnico_asignado IS NULL');
    }

    /**
     * Mis tickets según usuario y rol:
     * Solicitante y Administrador -> los que creó; Técnico -> los que tiene asignados.
     */
    public function queryMisTickets(int $idUsuario, string $rol)
    {
        $query = $this->getTicketsQuery();
        if ($rol === 'Técnico') {
            $query->where('tickets.id_tecnico_asignado', $idUsuario);
        } else {
            $query->where('tickets.id_solicitante', $idUsuario);
        }
        return $query;
    }

    /**
     * Tickets asignados (En Proceso), opcionalmente de un técnico.
     */
    public function queryAsignados(?int $idTecnico = null)
    {
        $query = $this->getTicketsQuery()->where('tickets.id_estado', 2); // En Proceso
        if ($idTecnico !== null) {
            $query->where('tickets.id_tecnico_asignado', $idTecnico);
        }
        return $query;
    }

    /**
     * Aplica los filtros de búsqueda y el orden sobre la consulta actual.
     *
     * @param array  $f            Filtros normalizados (ver Tickets::leerFiltros)
     * @param string $ordenDefecto Clave de ORDENES a usar si no viene ninguna
     */
    public function aplicarFiltros(array $f, string $ordenDefecto = 'recientes')
    {
        if (!empty($f['q'])) {
            helper('busqueda');
            aplicar_busqueda($this, $f['q'], [
                'tickets.codigo_ticket',
                'tickets.asunto',
                'tickets.descripcion',
                'sol.nombre_completo',
                'tec.nombre_completo',
                'areas_departamentos.nombre',
            ]);
        }
        if (!empty($f['id_estado'])) {
            $this->where('tickets.id_estado', $f['id_estado']);
        }
        if (!empty($f['id_area'])) {
            $this->where('tickets.id_area_afectada', $f['id_area']);
        }
        if (!empty($f['id_prioridad'])) {
            $this->where('tickets.id_prioridad', $f['id_prioridad']);
        }
        if ($f['id_tecnico'] === 'sin') {
            $this->where('tickets.id_tecnico_asignado IS NULL');
        } elseif (!empty($f['id_tecnico'])) {
            $this->where('tickets.id_tecnico_asignado', (int)$f['id_tecnico']);
        }
        if (!empty($f['sla'])) {
            $ahora = date('Y-m-d H:i:s');
            $this->where('tickets.fecha_resolucion IS NULL')
                 ->where('tickets.fecha_vencimiento_sla ' . ($f['sla'] === 'vencido' ? '<' : '>='), $ahora);
        }
        if (!empty($f['desde'])) {
            $this->where('tickets.fecha_creacion >=', $f['desde'] . ' 00:00:00');
        }
        if (!empty($f['hasta'])) {
            $this->where('tickets.fecha_creacion <=', $f['hasta'] . ' 23:59:59');
        }

        $orden = self::ORDENES[$f['orden'] ?? ''] ?? self::ORDENES[$ordenDefecto];
        foreach ($orden[1] as [$columna, $direccion]) {
            $this->orderBy($columna, $direccion);
        }
        $this->orderBy('tickets.id_ticket', 'DESC'); // desempate estable para la paginación

        return $this;
    }

    /**
     * Estadísticas para las tarjetas del Dashboard
     */
    public function getMetricasDashboard(int $idUsuario, string $rol): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tickets');

        if ($rol === 'Solicitante') {
            $builder->where('id_solicitante', $idUsuario);
        } elseif ($rol === 'Técnico') {
            $builder->where('id_tecnico_asignado', $idUsuario);
        }

        $total = (clone $builder)->countAllResults();
        $pendientes = (clone $builder)->where('id_estado', 1)->countAllResults();
        $enProceso = (clone $builder)->where('id_estado', 2)->countAllResults();
        $resueltos = (clone $builder)->where('id_estado', 3)->countAllResults();
        $cerrados = (clone $builder)->where('id_estado', 4)->countAllResults();

        // SLA Críticos (fecha_vencimiento_sla menor a ahora y no cerrados)
        $slaCriticos = (clone $builder)
            ->whereNotIn('id_estado', [3, 4])
            ->where('fecha_vencimiento_sla <', date('Y-m-d H:i:s'))
            ->countAllResults();

        return [
            'total'        => $total,
            'pendientes'   => $pendientes,
            'en_proceso'   => $enProceso,
            'resueltos'    => $resueltos,
            'cerrados'     => $cerrados,
            'sla_criticos' => $slaCriticos,
        ];
    }
}
