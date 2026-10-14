<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\UsuarioModel;
use App\Models\AreaModel;
use App\Models\HistorialTicketModel;
use App\Models\NotificacionModel;
use App\Models\BaseConocimientoModel;
use App\Models\EstadoTicketModel;
use App\Models\PrioridadModel;

class Tickets extends BaseController
{
    protected TicketModel $ticketModel;
    protected UsuarioModel $usuarioModel;
    protected AreaModel $areaModel;
    protected HistorialTicketModel $historialModel;
    protected NotificacionModel $notificacionModel;
    protected BaseConocimientoModel $kbModel;

    public function __construct()
    {
        $this->ticketModel       = new TicketModel();
        $this->usuarioModel      = new UsuarioModel();
        $this->areaModel         = new AreaModel();
        $this->historialModel    = new HistorialTicketModel();
        $this->notificacionModel = new NotificacionModel();
        $this->kbModel           = new BaseConocimientoModel();
    }

    private function getSessionData()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return null;
        }

        $idUsuario = (int)$session->get('id_usuario');
        $notificaciones = $this->notificacionModel->getPorUsuario($idUsuario, 5);
        $conteoNoLeidas = $this->notificacionModel->getConteoNoLeidas($idUsuario);

        return [
            'id_usuario'     => $idUsuario,
            'nombre'         => $session->get('nombre_completo'),
            'rol'            => $session->get('rol'),
            'id_rol'         => (int)$session->get('id_rol'),
            'correo'         => $session->get('correo'),
            'area'           => $session->get('area'),
            'notificaciones' => $notificaciones,
            'conteoNoLeidas' => $conteoNoLeidas,
        ];
    }

    /**
     * Lee y normaliza los filtros de los listados de tickets desde la URL (GET).
     */
    private function leerFiltros(): array
    {
        $tecnico = (string)$this->request->getGet('id_tecnico');
        $sla     = (string)$this->request->getGet('sla');
        $orden   = (string)$this->request->getGet('orden');

        $f = [
            'q'            => trim((string)$this->request->getGet('q')),
            'id_estado'    => (int)$this->request->getGet('id_estado'),
            'id_area'      => (int)$this->request->getGet('id_area'),
            'id_prioridad' => (int)$this->request->getGet('id_prioridad'),
            'id_tecnico'   => $tecnico === 'sin' ? 'sin' : (int)$tecnico,
            'sla'          => in_array($sla, ['vencido', 'en_plazo'], true) ? $sla : '',
            'desde'        => $this->fechaGet('desde'),
            'hasta'        => $this->fechaGet('hasta'),
            'orden'        => array_key_exists($orden, TicketModel::ORDENES) ? $orden : '',
            'por_pagina'   => $this->porPaginaGet(),
        ];

        // Si el rango viene invertido, se corrige
        if ($f['desde'] && $f['hasta'] && $f['desde'] > $f['hasta']) {
            [$f['desde'], $f['hasta']] = [$f['hasta'], $f['desde']];
        }

        return $f;
    }

    /**
     * Ejecuta la consulta filtrada y paginada, y agrega los catálogos para el formulario de filtros.
     */
    private function prepararListado(array $data, $query, array $filtros, string $ordenDefecto): array
    {
        $data['tickets']      = $query->aplicarFiltros($filtros, $ordenDefecto)->paginate($filtros['por_pagina']);
        $data['pager']        = $this->ticketModel->pager;
        $data['filtros']      = $filtros;
        $data['ordenDefecto'] = $ordenDefecto;
        $data['areas']        = $this->areaModel->getActivas();
        $data['estados']      = (new EstadoTicketModel())->findAll();
        $data['prioridades']  = (new PrioridadModel())->orderBy('id_prioridad', 'DESC')->findAll();
        $data['tecnicos']     = $this->usuarioModel->getTecnicos();
        return $data;
    }

    public function nuevo()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');

        $data['areas'] = $this->areaModel->getActivas();
        return view('tickets/nuevo', $data);
    }

    public function crear()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');

        $idArea    = (int)$this->request->getPost('id_area_afectada');
        $asunto    = trim((string)$this->request->getPost('asunto'));
        $impacto   = (string)$this->request->getPost('nivel_impacto');
        $urgencia  = (string)$this->request->getPost('nivel_urgencia');
        $desc      = trim((string)$this->request->getPost('descripcion'));

        if (empty($idArea) || empty($asunto) || empty($impacto) || empty($urgencia) || empty($desc)) {
            session()->setFlashdata('error', 'Todos los campos marcados con * son obligatorios.');
            return redirect()->back()->withInput();
        }

        // 1. Calcular prioridad y SLA
        $prioInfo = $this->ticketModel->calcularPrioridad($impacto, $urgencia, $idArea);
        $idPrioridad = $prioInfo['id_prioridad'];
        $minutosSla = $prioInfo['minutos_sla'];
        $ahora = time();
        $now = date('Y-m-d H:i:s', $ahora);
        $fechaVencimientoSla = date('Y-m-d H:i:s', $ahora + $minutosSla * 60);

        // 2. Generar código
        $codigo = $this->ticketModel->generarCodigo();

        // 3. Insertar ticket
        $idTicket = $this->ticketModel->insert([
            'codigo_ticket'         => $codigo,
            'id_solicitante'        => $data['id_usuario'],
            'id_tecnico_asignado'   => null,
            'id_area_afectada'      => $idArea,
            'nivel_impacto'         => $impacto,
            'nivel_urgencia'        => $urgencia,
            'id_prioridad'          => $idPrioridad,
            'id_estado'             => 1, // Pendiente
            'asunto'                => $asunto,
            'descripcion'           => $desc,
            'solucion_aplicada'     => null,
            'fecha_creacion'        => $now,
            'fecha_asignacion'      => null,
            'fecha_resolucion'      => null,
            'fecha_cierre'          => null,
            'fecha_vencimiento_sla' => $fechaVencimientoSla,
        ]);

        // 4. Registrar en historial
        $this->historialModel->registrarAccion(
            $idTicket,
            $data['id_usuario'],
            'Ticket Creado',
            "Ticket creado con prioridad {$prioInfo['nombre']}. Estado inicial: Pendiente."
        );

        // 5. Notificar administradores
        $this->notificacionModel->notificarAdministradores(
            "Nuevo ticket #{$codigo} en cola pendiente: {$asunto}",
            'alerta',
            $idTicket
        );

        session()->setFlashdata('success', "¡Ticket #{$codigo} registrado exitosamente con prioridad {$prioInfo['nombre']}!");
        return redirect()->to('/tickets/mis-tickets');
    }

    public function todos()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        if (!in_array($data['rol'], ['Administrador', 'Técnico'])) return redirect()->to('/dashboard');

        $query = $this->ticketModel->getTicketsQuery();
        $data  = $this->prepararListado($data, $query, $this->leerFiltros(), 'recientes');
        return view('tickets/todos', $data);
    }

    public function cola()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        if (!in_array($data['rol'], ['Administrador', 'Técnico'])) return redirect()->to('/dashboard');

        $data = $this->prepararListado($data, $this->ticketModel->queryCola(), $this->leerFiltros(), 'prioridad');
        return view('tickets/cola', $data);
    }

    public function misTickets()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');

        $filtros = $this->leerFiltros();
        if ($data['rol'] === 'Solicitante') {
            $filtros['sla'] = ''; // el solicitante no trabaja con SLA
        }

        $query = $this->ticketModel->queryMisTickets($data['id_usuario'], $data['rol']);
        $data  = $this->prepararListado($data, $query, $filtros, 'recientes');
        return view('tickets/mis_tickets', $data);
    }

    public function asignados()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        if (!in_array($data['rol'], ['Administrador', 'Técnico'])) return redirect()->to('/dashboard');

        $idTecnico = ($data['rol'] === 'Técnico') ? $data['id_usuario'] : null;
        $data = $this->prepararListado($data, $this->ticketModel->queryAsignados($idTecnico), $this->leerFiltros(), 'sla');
        return view('tickets/asignados', $data);
    }

    public function detalles($id = null)
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');

        $idBusqueda = $id ?? $this->request->getGet('id') ?? '1';
        $ticket = $this->ticketModel->getTicketCompleto($idBusqueda);

        if (!$ticket) {
            session()->setFlashdata('error', 'El ticket solicitado no existe.');
            return redirect()->to('/tickets/mis-tickets');
        }

        // Un solicitante solo puede ver sus propios tickets
        if ($data['rol'] === 'Solicitante' && (int)$ticket['id_solicitante'] !== $data['id_usuario']) {
            session()->setFlashdata('error', 'No tienes permiso para ver este ticket.');
            return redirect()->to('/tickets/mis-tickets');
        }

        // Al abrir el ticket, sus notificaciones pendientes quedan leídas
        $this->notificacionModel->marcarLeidasPorTicket($data['id_usuario'], (int)$ticket['id_ticket']);
        $data['notificaciones'] = $this->notificacionModel->getPorUsuario($data['id_usuario'], 5);
        $data['conteoNoLeidas'] = $this->notificacionModel->getConteoNoLeidas($data['id_usuario']);

        $data['ticket'] = $ticket;
        $data['ticket_id'] = $ticket['codigo_ticket'];
        $data['historial'] = $this->historialModel->getHistorialPorTicket((int)$ticket['id_ticket']);
        $data['tecnicos']  = $this->usuarioModel->getTecnicos();

        return view('tickets/detalles', $data);
    }

    public function asignar()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        if ($data['rol'] !== 'Administrador') {
            session()->setFlashdata('error', 'Solo los administradores pueden asignar tickets.');
            return redirect()->to('/dashboard');
        }

        $idTicket  = (int)$this->request->getPost('id_ticket');
        $idTecnico = (int)$this->request->getPost('id_tecnico');

        $ticket  = $this->ticketModel->find($idTicket);
        $tecnico = $this->getTecnicoValido($idTecnico);

        if (!$ticket || !$tecnico) {
            session()->setFlashdata('error', 'Ticket o técnico no válido.');
            return redirect()->back();
        }

        if (!in_array((int)$ticket['id_estado'], [1, 2], true)) {
            session()->setFlashdata('error', 'Solo se pueden asignar tickets en estado Pendiente o En Proceso.');
            return redirect()->back();
        }

        // Si ya tenía técnico, se trata como una reasignación
        if (!empty($ticket['id_tecnico_asignado'])) {
            return $this->aplicarReasignacion($data, $ticket, $tecnico, '');
        }

        $now = date('Y-m-d H:i:s');
        $this->ticketModel->update($idTicket, [
            'id_tecnico_asignado' => $idTecnico,
            'id_estado'           => 2, // En Proceso
            'fecha_asignacion'    => $now,
        ]);

        $this->historialModel->registrarAccion(
            $idTicket,
            $data['id_usuario'],
            'Ticket Asignado',
            "Asignado al técnico {$tecnico['nombre_completo']}. Estado cambiado a En Proceso."
        );

        $this->notificacionModel->crear(
            $idTecnico,
            "Se te asignó el ticket #{$ticket['codigo_ticket']}: {$ticket['asunto']}",
            'asignacion',
            $idTicket
        );

        $this->notificacionModel->crear(
            (int)$ticket['id_solicitante'],
            "Tu ticket #{$ticket['codigo_ticket']} ahora está En Proceso, asignado a {$tecnico['nombre_completo']}.",
            'info',
            $idTicket
        );

        session()->setFlashdata('success', "Ticket asignado exitosamente a {$tecnico['nombre_completo']}.");
        return redirect()->back();
    }

    /**
     * Devuelve el usuario solo si es un técnico activo.
     */
    private function getTecnicoValido(int $idTecnico): ?array
    {
        $tecnico = $this->usuarioModel->find($idTecnico);
        if (!$tecnico || (int)$tecnico['id_rol'] !== 2 || $tecnico['estado'] !== 'activo') {
            return null;
        }
        return $tecnico;
    }

    private function aplicarReasignacion(array $data, array $ticket, array $tecnico, string $motivo)
    {
        $idTicket  = (int)$ticket['id_ticket'];
        $idTecnico = (int)$tecnico['id_usuario'];

        if ((int)$ticket['id_tecnico_asignado'] === $idTecnico) {
            session()->setFlashdata('error', "El ticket ya está asignado a {$tecnico['nombre_completo']}.");
            return redirect()->back();
        }

        $this->ticketModel->update($idTicket, [
            'id_tecnico_asignado' => $idTecnico,
        ]);

        $this->historialModel->registrarAccion(
            $idTicket,
            $data['id_usuario'],
            'Ticket Reasignado',
            "Reasignado al técnico {$tecnico['nombre_completo']}." . ($motivo ? " Motivo: {$motivo}" : '')
        );

        $this->notificacionModel->crear(
            $idTecnico,
            "Reasignación: Se te transfirió el ticket #{$ticket['codigo_ticket']}: {$ticket['asunto']}",
            'asignacion',
            $idTicket
        );

        $this->notificacionModel->crear(
            (int)$ticket['id_tecnico_asignado'],
            "El ticket #{$ticket['codigo_ticket']} fue reasignado a otro técnico.",
            'info',
            $idTicket
        );

        session()->setFlashdata('success', "Ticket reasignado a {$tecnico['nombre_completo']}.");
        return redirect()->back();
    }

    public function reasignar()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        if ($data['rol'] !== 'Administrador') {
            session()->setFlashdata('error', 'Solo los administradores pueden reasignar tickets.');
            return redirect()->to('/dashboard');
        }

        $idTicket  = (int)$this->request->getPost('id_ticket');
        $idTecnico = (int)$this->request->getPost('id_tecnico');
        $motivo    = trim((string)$this->request->getPost('motivo'));

        $ticket  = $this->ticketModel->find($idTicket);
        $tecnico = $this->getTecnicoValido($idTecnico);

        if (!$ticket || !$tecnico) {
            session()->setFlashdata('error', 'Ticket o técnico no válido.');
            return redirect()->back();
        }

        if ((int)$ticket['id_estado'] !== 2 || empty($ticket['id_tecnico_asignado'])) {
            session()->setFlashdata('error', 'Solo se pueden reasignar tickets En Proceso que ya tengan técnico.');
            return redirect()->back();
        }

        return $this->aplicarReasignacion($data, $ticket, $tecnico, $motivo);
    }

    public function resolver()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');
        if (!in_array($data['rol'], ['Técnico', 'Administrador'])) {
            session()->setFlashdata('error', 'No tienes permisos para registrar solución técnica.');
            return redirect()->to('/dashboard');
        }

        $idTicket = (int)$this->request->getPost('id_ticket');
        $solucion = trim((string)$this->request->getPost('solucion_aplicada'));

        if (empty($solucion)) {
            session()->setFlashdata('error', 'Debes detallar la solución técnica aplicada.');
            return redirect()->back();
        }

        $ticket = $this->ticketModel->find($idTicket);
        if (!$ticket) {
            session()->setFlashdata('error', 'Ticket no encontrado.');
            return redirect()->back();
        }

        if ((int)$ticket['id_estado'] !== 2) {
            session()->setFlashdata('error', 'Solo se puede registrar la solución de un ticket En Proceso.');
            return redirect()->back();
        }

        // El técnico solo puede resolver los tickets que tiene asignados
        if ($data['rol'] === 'Técnico' && (int)$ticket['id_tecnico_asignado'] !== $data['id_usuario']) {
            session()->setFlashdata('error', 'Solo el técnico asignado puede resolver este ticket.');
            return redirect()->back();
        }

        $now = date('Y-m-d H:i:s');
        $this->ticketModel->update($idTicket, [
            'solucion_aplicada' => $solucion,
            'id_estado'         => 3, // Resuelto (esperando confirmación del solicitante)
            'fecha_resolucion'  => $now,
        ]);

        $this->historialModel->registrarAccion(
            $idTicket,
            $data['id_usuario'],
            'Solución Registrada',
            "El técnico registró la solución y marcó el ticket como Resuelto. Solución: {$solucion}"
        );

        // Notificar al solicitante para que revise y cierre
        $this->notificacionModel->crear(
            (int)$ticket['id_solicitante'],
            "El técnico resolvió tu ticket #{$ticket['codigo_ticket']}. Por favor confirma y cierra la solicitud.",
            'resolucion',
            $idTicket
        );

        session()->setFlashdata('success', 'Solución registrada con éxito. El ticket quedó en estado Resuelto a la espera del cierre del solicitante.');
        return redirect()->to('/tickets/detalles/' . $idTicket);
    }

    public function cerrar()
    {
        $data = $this->getSessionData();
        if (!$data) return redirect()->to('/auth');

        $idTicket = (int)$this->request->getPost('id_ticket');
        $ticket = $this->ticketModel->find($idTicket);

        if (!$ticket) {
            session()->setFlashdata('error', 'Ticket no encontrado.');
            return redirect()->back();
        }

        // Solo quien creó el ticket (solicitante, o el admin si él lo creó) puede finalizarlo
        if ((int)$ticket['id_solicitante'] !== $data['id_usuario']) {
            session()->setFlashdata('error', 'Solo quien creó el ticket puede confirmar y cerrarlo.');
            return redirect()->back();
        }

        if ((int)$ticket['id_estado'] !== 3) {
            session()->setFlashdata('error', 'Solo se puede cerrar un ticket que esté Resuelto.');
            return redirect()->back();
        }

        $now = date('Y-m-d H:i:s');
        $this->ticketModel->update($idTicket, [
            'id_estado'    => 4, // Cerrado
            'fecha_cierre' => $now,
        ]);

        $this->historialModel->registrarAccion(
            $idTicket,
            $data['id_usuario'],
            'Ticket Cerrado',
            "El ticket fue confirmado y cerrado definitivamente por {$data['nombre']}."
        );

        // Archivar la solución aplicada en la Base de Conocimientos (una sola vez por ticket)
        if (!empty($ticket['solucion_aplicada']) && !$this->kbModel->existePorTicket($idTicket)) {
            $codigoSol = $this->kbModel->generarCodigo();
            $this->kbModel->insert([
                'codigo_solucion'      => $codigoSol,
                'titulo'               => $ticket['asunto'],
                'contenido'            => $ticket['solucion_aplicada'],
                'id_autor'             => $ticket['id_tecnico_asignado'] ?? $data['id_usuario'],
                'id_ticket_referencia' => $idTicket,
                'fecha_creacion'       => $now,
                'fecha_actualizacion'  => $now,
            ]);
        }

        // Notificar al técnico asignado si existe
        if (!empty($ticket['id_tecnico_asignado'])) {
            $this->notificacionModel->crear(
                (int)$ticket['id_tecnico_asignado'],
                "El ticket #{$ticket['codigo_ticket']} ha sido validado y cerrado exitosamente por el solicitante.",
                'info',
                $idTicket
            );
        }

        session()->setFlashdata('success', "Ticket #{$ticket['codigo_ticket']} cerrado y finalizado exitosamente.");
        return redirect()->to('/tickets/mis-tickets');
    }
}
