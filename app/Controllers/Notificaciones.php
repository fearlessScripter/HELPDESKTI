<?php

namespace App\Controllers;

use App\Models\NotificacionModel;

class Notificaciones extends BaseController
{
    protected NotificacionModel $notifModel;

    public function __construct()
    {
        $this->notifModel = new NotificacionModel();
    }

    /**
     * Marca la notificación como leída y redirige al ticket relacionado.
     */
    public function leer($idNotificacion = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');

        $notif = $this->notifModel->marcarLeida((int)$idNotificacion, (int)$session->get('id_usuario'));

        if ($notif && !empty($notif['id_ticket_referencia'])) {
            return redirect()->to('/tickets/detalles/' . $notif['id_ticket_referencia']);
        }

        return redirect()->to('/dashboard');
    }

    public function leerTodas()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');

        $this->notifModel->marcarTodasLeidas((int)$session->get('id_usuario'));

        return redirect()->back();
    }
}
