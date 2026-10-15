<?php

namespace App\Controllers;

use App\Models\AreaModel;
use App\Models\PrioridadModel;
use App\Models\ReporteModel;
use App\Models\TicketModel;
use App\Models\UsuarioModel;

class Reportes extends BaseController
{
    /** Máximo de tickets de la auditoría que se incluyen al imprimir / exportar a PDF. */
    private const MAX_AUDITORIA_IMPRESION = 500;

    private function soloAdmin()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if ($session->get('rol') !== 'Administrador') return redirect()->to('/dashboard');
        return null;
    }

    /**
     * Filtros del reporte (GET). Por defecto: el mes en curso.
     */
    private function leerFiltros(): array
    {
        $desde = $this->fechaGet('desde');
        $hasta = $this->fechaGet('hasta');

        // Si no viene ningún rango, se usa el mes actual
        if ($desde === '' && $hasta === '' && $this->request->getGet('desde') === null) {
            $desde = date('Y-m-01');
            $hasta = date('Y-m-d');
        }
        if ($desde && $hasta && $desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        return [
            'desde'        => $desde,
            'hasta'        => $hasta,
            'id_area'      => (int)$this->request->getGet('id_area'),
            'id_tecnico'   => (int)$this->request->getGet('id_tecnico'),
            'id_prioridad' => (int)$this->request->getGet('id_prioridad'),
        ];
    }

    /**
     * Mismo rango de días, inmediatamente anterior (para la comparación).
     */
    private function periodoAnterior(array $f): ?array
    {
        if (!$f['desde'] || !$f['hasta']) {
            return null;
        }
        $dias = (int)((strtotime($f['hasta']) - strtotime($f['desde'])) / 86400) + 1;
        return array_merge($f, [
            'desde' => date('Y-m-d', strtotime($f['desde'] . " -{$dias} days")),
            'hasta' => date('Y-m-d', strtotime($f['desde'] . ' -1 day')),
            'dias'  => $dias,
        ]);
    }

    /**
     * Consulta de tickets (con sus relaciones) aplicando los filtros del reporte.
     */
    private function queryTickets(TicketModel $model, array $f)
    {
        $q = $model->getTicketsQuery();
        if ($f['desde'])        $q->where('tickets.fecha_creacion >=', $f['desde'] . ' 00:00:00');
        if ($f['hasta'])        $q->where('tickets.fecha_creacion <=', $f['hasta'] . ' 23:59:59');
        if ($f['id_area'])      $q->where('tickets.id_area_afectada', $f['id_area']);
        if ($f['id_tecnico'])   $q->where('tickets.id_tecnico_asignado', $f['id_tecnico']);
        if ($f['id_prioridad']) $q->where('tickets.id_prioridad', $f['id_prioridad']);
        return $q;
    }

    public function index()
    {
        if ($r = $this->soloAdmin()) return $r;

        $session  = session();
        $reporte  = new ReporteModel();
        $filtros  = $this->leerFiltros();
        $anterior = $this->periodoAnterior($filtros);

        // Auditoría de incumplimientos (paginada)
        $ticketModel = new TicketModel();
        $auditoria   = $this->queryTickets($ticketModel, $filtros)
            ->where($reporte->sqlIncumplimiento(), null, false)
            ->orderBy('tickets.fecha_creacion', 'DESC')
            ->paginate(10, 'auditoria');

        // Para imprimir / PDF: la auditoría completa (con tope para no generar documentos gigantes)
        $auditoriaCompleta = $this->queryTickets(new TicketModel(), $filtros)
            ->where($reporte->sqlIncumplimiento(), null, false)
            ->orderBy('tickets.fecha_creacion', 'DESC')
            ->findAll(self::MAX_AUDITORIA_IMPRESION);

        $data = [
            'id_usuario'      => (int)$session->get('id_usuario'),
            'nombre'          => $session->get('nombre_completo'),
            'rol'             => $session->get('rol'),
            'filtros'         => $filtros,
            'resumen'         => $reporte->resumen($filtros),
            'creadosAnterior' => $anterior ? $reporte->contarCreados($anterior) : null,
            'periodoAnterior' => $anterior,
            'porArea'         => $reporte->porArea($filtros),
            'porPrioridad'    => $reporte->porPrioridad($filtros),
            'tecnicosDesempeno' => $reporte->desempenoTecnicos($filtros),
            'auditoria'       => $auditoria,
            'auditoriaCompleta' => $auditoriaCompleta,
            'maxAuditoriaImpresion' => self::MAX_AUDITORIA_IMPRESION,
            'pager'           => $ticketModel->pager,
            'areas'           => (new AreaModel())->getActivas(),
            'tecnicos'        => (new UsuarioModel())->where('id_rol', 2)->orderBy('nombre_completo')->findAll(),
            'prioridades'     => (new PrioridadModel())->orderBy('id_prioridad', 'DESC')->findAll(),
            'olaPorcentaje'   => ReporteModel::OLA_PORCENTAJE,
        ];

        return view('reportes/index', $data);
    }
}
