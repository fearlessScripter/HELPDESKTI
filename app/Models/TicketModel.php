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

        if ($puntaje >= 7) {
            $idPrioridad = 3; // Alta
        } elseif ($puntaje >= 5) {
            $idPrioridad = 2; // Media
        } else {
            $idPrioridad = 1; // Baja
        }

        $prioridad = (new PrioridadModel())->find($idPrioridad);

        return [
            'id_prioridad' => $idPrioridad,
            'nombre'       => $prioridad['nombre'] ?? 'Baja',
            'minutos_sla'  => (int)($prioridad['tiempo_sla_minutos'] ?? 1440),
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
                tec.nombre_completo as tecnico_nombre,
                tec.correo as tecnico_correo,
                areas_departamentos.nombre as area_nombre,
                prioridades.nombre as prioridad_nombre,
                prioridades.tiempo_sla_minutos as sla_minutos,
                estados_ticket.nombre as estado_nombre
            ')
            ->join('usuarios as sol', 'sol.id_usuario = tickets.id_solicitante')
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
     * Tickets en cola (Pendientes y sin asignar)
     */
    public function getColaPendientes()
    {
        return $this->getTicketsQuery()
                    ->where('tickets.id_estado', 1) // Pendiente
                    ->where('tickets.id_tecnico_asignado IS NULL')
                    ->orderBy('tickets.id_prioridad', 'DESC')
                    ->orderBy('tickets.fecha_creacion', 'ASC')
                    ->findAll();
    }

    /**
     * Mis tickets según usuario y rol
     */
    public function getMisTickets(int $idUsuario, string $rol)
    {
        $query = $this->getTicketsQuery();
        if ($rol === 'Solicitante') {
            $query->where('tickets.id_solicitante', $idUsuario);
        } elseif ($rol === 'Técnico') {
            $query->where('tickets.id_tecnico_asignado', $idUsuario);
        }
        return $query->orderBy('tickets.fecha_creacion', 'DESC')->findAll();
    }

    /**
     * Tickets asignados (En Proceso)
     */
    public function getAsignados(?int $idTecnico = null)
    {
        $query = $this->getTicketsQuery()->where('tickets.id_estado', 2); // En Proceso
        if ($idTecnico !== null) {
            $query->where('tickets.id_tecnico_asignado', $idTecnico);
        }
        return $query->orderBy('tickets.fecha_asignacion', 'DESC')->findAll();
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
