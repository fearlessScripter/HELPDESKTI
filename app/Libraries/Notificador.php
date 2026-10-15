<?php

namespace App\Libraries;

use App\Libraries\Notificaciones\CanalNotificacion;
use Config\Notificaciones;

/**
 * Punto único para enviar notificaciones a los usuarios.
 *
 * Reparte cada notificación a los canales activos de Config\Notificaciones. Hoy: el sistema
 * (campana). Para agregar correo a futuro basta con registrar el canal en la configuración;
 * los controladores no cambian.
 */
class Notificador
{
    /** @var list<CanalNotificacion> */
    private array $canales = [];

    public function __construct(?Notificaciones $config = null)
    {
        $config ??= config(Notificaciones::class);
        foreach ($config->canales as $nombre) {
            $clase = $config->clasesCanal[$nombre] ?? null;
            if ($clase !== null) {
                $this->canales[] = new $clase();
            }
        }
    }

    public function enviar(int $idUsuario, string $mensaje, string $tipo = 'info', ?int $idTicket = null): void
    {
        foreach ($this->canales as $canal) {
            $canal->enviar($idUsuario, $mensaje, $tipo, $idTicket);
        }
    }

    /**
     * @param list<int> $idsUsuarios
     */
    public function enviarAVarios(array $idsUsuarios, string $mensaje, string $tipo = 'info', ?int $idTicket = null): void
    {
        foreach (array_unique($idsUsuarios) as $idUsuario) {
            $this->enviar((int)$idUsuario, $mensaje, $tipo, $idTicket);
        }
    }

    /**
     * A todos los administradores activos.
     */
    public function enviarAAdministradores(string $mensaje, string $tipo = 'alerta', ?int $idTicket = null): void
    {
        $this->enviarAVarios($this->idsAdministradores(), $mensaje, $tipo, $idTicket);
    }

    /**
     * @return list<int>
     */
    public function idsAdministradores(): array
    {
        $filas = \Config\Database::connect()->table('usuarios')
            ->select('id_usuario')
            ->where('id_rol', 1)
            ->where('estado', 'activo')
            ->get()->getResultArray();

        return array_map('intval', array_column($filas, 'id_usuario'));
    }
}
