<?php

namespace App\Libraries\Notificaciones;

/**
 * Un medio por el que se entrega una notificación (sistema, correo, ...).
 */
interface CanalNotificacion
{
    /**
     * @param int      $idUsuario Destinatario
     * @param string   $mensaje   Texto de la notificación
     * @param string   $tipo      info | alerta | urgente | sla_vencido | asignacion | resolucion
     * @param int|null $idTicket  Ticket relacionado (si aplica)
     */
    public function enviar(int $idUsuario, string $mensaje, string $tipo, ?int $idTicket): void;
}
