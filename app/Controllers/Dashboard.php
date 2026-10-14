<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\NotificacionModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $idUsuario = (int)$session->get('id_usuario');
        $rol       = (string)$session->get('rol');

        $ticketModel = new TicketModel();
        $notifModel  = new NotificacionModel();

        $metricas = $ticketModel->getMetricasDashboard($idUsuario, $rol);
        $notificaciones = $notifModel->getPorUsuario($idUsuario, 5);
        $conteoNoLeidas = $notifModel->getConteoNoLeidas($idUsuario);

        $data = [
            'id_usuario'     => $idUsuario,
            'nombre'         => $session->get('nombre_completo'),
            'rol'            => $rol,
            'area'           => $session->get('area'),
            'metricas'       => $metricas,
            'notificaciones' => $notificaciones,
            'conteoNoLeidas' => $conteoNoLeidas,
        ];

        if ($rol === 'Solicitante') {
            $data['misTickets'] = $ticketModel->getMisTickets($idUsuario, $rol);
            return view('dashboard/solicitante', $data);
        } elseif ($rol === 'Técnico') {
            $data['misTickets'] = $ticketModel->getAsignados($idUsuario);
            return view('dashboard/tecnico', $data);
        } else {
            // Administrador
            $data['ticketsRecientes'] = $ticketModel->getTicketsQuery()->orderBy('tickets.fecha_creacion', 'DESC')->findAll(10);
            return view('dashboard/admin', $data);
        }
    }
}
