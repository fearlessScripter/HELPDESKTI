<?php

namespace App\Libraries\Notificaciones;

use App\Models\NotificacionModel;

/**
 * Canal "sistema": guarda la notificación para la campana de la barra superior.
 */
class CanalSistema implements CanalNotificacion
{
    private NotificacionModel $modelo;

    public function __construct()
    {
        $this->modelo = new NotificacionModel();
    }

    public function enviar(int $idUsuario, string $mensaje, string $tipo, ?int $idTicket): void
    {
        $this->modelo->crear($idUsuario, $mensaje, $tipo, $idTicket);
    }
}
