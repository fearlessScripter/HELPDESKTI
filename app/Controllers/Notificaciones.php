<?php

namespace App\Controllers;

use App\Models\NotificacionModel;
use App\Models\TicketModel;

class Notificaciones extends BaseController
{
    protected NotificacionModel $notifModel;

    public function __construct()
    {
        $this->notifModel = new NotificacionModel();
    }

    /**
     * Marca la notificación como leída y redirige al ticket relacionado,
     * solo si el usuario todavía tiene acceso a él (p. ej. un técnico al que se le
     * reasignó el ticket ya no puede abrirlo: la notificación solo se marca como leída).
     */
    public function leer($idNotificacion = null)
    {
        $session   = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        $idUsuario = (int)$session->get('id_usuario');

        $notif = $this->notifModel->marcarLeida((int)$idNotificacion, $idUsuario);

        if ($notif && !empty($notif['id_ticket_referencia'])) {
            $ticket = (new TicketModel())->find((int)$notif['id_ticket_referencia']);
            if ($ticket && TicketModel::usuarioPuedeVer($ticket, $idUsuario, (string)$session->get('rol'))) {
                return redirect()->to('/tickets/detalles/' . $ticket['id_ticket']);
            }
        }

        // Notificación solo informativa: se queda en la página actual
        return redirect()->back();
    }

    /**
     * estado de la campana para la actualización en tiempo real (el topbar lo consulta cada pocos segundos).
     *
     * Recibe ?ultimo=<id de la notificación más reciente que tiene la página>&conteo=<no leídas que muestra>.
     * Si nada cambió responde solo esos dos números. en caso de haber cambios agrega el menú ya renderizado
     * (la misma vista del topbar) y las notificaciones nuevas, para mostrarlas como aviso.
     */
    public function estado()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['sesion' => false]);
        }
        $idUsuario = (int)$session->get('id_usuario');
        // aqui la sesión solo se lee: se libera para no bloquear otras peticiones del mismo usuario
        $session->close();

        $ultimoConocido = max(0, (int)$this->request->getGet('ultimo'));
        $conteoConocido = max(0, (int)$this->request->getGet('conteo'));

        $estado = [
            'ultimo' => $this->notifModel->getUltimoId($idUsuario),
            'conteo' => $this->notifModel->getConteoNoLeidas($idUsuario),
        ];

        if ($estado['ultimo'] !== $ultimoConocido || $estado['conteo'] !== $conteoConocido) {
            $estado['menu'] = view('layout/notificaciones_menu', [
                'notificaciones' => $this->notifModel->getPorUsuario($idUsuario, 5),
                'conteoNoLeidas' => $estado['conteo'],
            ]);

            $estado['nuevas'] = array_map(static fn (array $n) => [
                'id'       => (int)$n['id_notificacion'],
                'mensaje'  => $n['mensaje'],
                'icono'    => icono_notificacion($n['tipo_alerta']),
                'fecha'    => fecha_hora($n['fecha_creacion']),
                'idTicket' => $n['id_ticket_referencia'] !== null ? (int)$n['id_ticket_referencia'] : null,
                'url'      => base_url('/notificaciones/leer/' . $n['id_notificacion']),
            ], $this->notifModel->getNuevasDesde($idUsuario, $ultimoConocido));
        }

        return $this->response->setJSON($estado);
    }

    public function leerTodas()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');

        $this->notifModel->marcarTodasLeidas((int)$session->get('id_usuario'));

        return redirect()->back();
    }
}
