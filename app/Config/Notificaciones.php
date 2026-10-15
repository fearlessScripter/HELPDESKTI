<?php

namespace Config;

use App\Libraries\Notificaciones\CanalSistema;
use CodeIgniter\Config\BaseConfig;

/**
 * Configuración de notificaciones y alertas de SLA.
 *
 * Toda notificación pasa por App\Libraries\Notificador, que la entrega por cada canal activo.
 * Hoy solo existe el canal "sistema" (la campana de notificaciones).
 *
 * ── Cómo agregar el envío por correo (Gmail) a futuro ──────────────────────────────
 *  1. Configurar SMTP en app/Config/Email.php o en .env (email.SMTPHost = smtp.gmail.com,
 *     email.SMTPUser, email.SMTPPass = contraseña de aplicación de Google, email.SMTPPort = 587,
 *     email.SMTPCrypto = tls).
 *  2. Crear App\Libraries\Notificaciones\CanalCorreo implementando CanalNotificacion:
 *     buscar el correo del usuario y enviarlo con service('email').
 *  3. Registrarlo en $clasesCanal y agregar 'correo' a $canales.
 *  4. Recuperación de contraseña por correo: activar $recuperacionClavePorCorreo y crear el flujo
 *     (token de un solo uso con vencimiento) reutilizando el mismo canal de correo.
 * ───────────────────────────────────────────────────────────────────────────────────
 */
class Notificaciones extends BaseConfig
{
    /**
     * Canales activos (en orden de envío).
     *
     * @var list<string>
     */
    public array $canales = ['sistema'];

    /**
     * Clase que implementa cada canal.
     *
     * @var array<string, class-string>
     */
    public array $clasesCanal = [
        'sistema' => CanalSistema::class,
        // 'correo' => \App\Libraries\Notificaciones\CanalCorreo::class,  // futuro
    ];

    /**
     * Alertas de SLA: porcentaje del plazo consumido en que se avisa "por vencer".
     * Coincide con el color de advertencia que muestra la interfaz (> 75 %).
     */
    public int $slaPorcentajeAviso = 75;

    /**
     * Cada cuántos minutos, como máximo, la propia aplicación revisa los SLA mientras se usa.
     * (Independiente del comando `php spark sla:revisar`, que puede programarse en Windows.)
     */
    public int $slaRevisionCadaMinutos = 5;

    /**
     * Futuro: recuperación de contraseña mediante un enlace enviado al correo del usuario.
     * Mientras sea false, el login indica que se solicite al administrador.
     */
    public bool $recuperacionClavePorCorreo = false;
}
