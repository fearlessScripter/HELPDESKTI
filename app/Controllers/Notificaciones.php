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

    public function leerTodas()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');

        $this->notifModel->marcarTodasLeidas((int)$session->get('id_usuario'));

        return redirect()->back();
    }
}
