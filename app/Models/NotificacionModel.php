<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificacionModel extends Model
{
    protected $table            = 'notificaciones';
    protected $primaryKey       = 'id_notificacion';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_usuario',
        'id_ticket_referencia',
        'tipo_alerta',
        'mensaje',
        'leida',
        'fecha_creacion',
    ];

    /**
     * Guarda una notificación del sistema. Para notificar a usuarios usar App\Libraries\Notificador
     * (que la entrega por todos los canales activos); este método es el almacenamiento del canal "sistema".
     */
    public function crear(int $idUsuario, string $mensaje, string $tipoAlerta = 'info', ?int $idTicket = null)
    {
        return $this->insert([
            'id_usuario'           => $idUsuario,
            'id_ticket_referencia' => $idTicket,
            'tipo_alerta'          => $tipoAlerta,
            'mensaje'              => $mensaje,
            'leida'                => 0,
            'fecha_creacion'       => date('Y-m-d H:i:s'),
        ]);
    }

    public function getPorUsuario(int $idUsuario, int $limit = 10)
    {
        return $this->where('id_usuario', $idUsuario)
                    ->orderBy('fecha_creacion', 'DESC')
                    ->findAll($limit);
    }

    public function getConteoNoLeidas(int $idUsuario): int
    {
        return $this->where('id_usuario', $idUsuario)
                    ->where('leida', 0)
                    ->countAllResults();
    }

    /**
     * Marca una notificación como leída (solo si pertenece al usuario).
     * Retorna la notificación o null si no existe / no es suya.
     */
    public function marcarLeida(int $idNotificacion, int $idUsuario): ?array
    {
        $notif = $this->where('id_notificacion', $idNotificacion)
                      ->where('id_usuario', $idUsuario)
                      ->first();

        if ($notif && (int)$notif['leida'] === 0) {
            $this->update($idNotificacion, ['leida' => 1]);
        }

        return $notif;
    }

    public function marcarTodasLeidas(int $idUsuario): void
    {
        $this->where('id_usuario', $idUsuario)
             ->where('leida', 0)
             ->set('leida', 1)
             ->update();
    }

    /**
     * Marca como leídas las notificaciones del usuario sobre un ticket (al abrir su detalle).
     */
    public function marcarLeidasPorTicket(int $idUsuario, int $idTicket): void
    {
        $this->where('id_usuario', $idUsuario)
             ->where('id_ticket_referencia', $idTicket)
             ->where('leida', 0)
             ->set('leida', 1)
             ->update();
    }
}
