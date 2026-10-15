<?php

namespace App\Libraries;

use Config\Notificaciones;

/**
 * Revisa los tickets abiertos y genera las alertas de SLA:
 *  - "por vencer": se consumió el % configurado del plazo (por defecto 75 %).
 *  - "vencido": se superó la fecha de vencimiento sin registrar la solución.
 *
 * Destinatarios: técnico asignado (si lo hay) + administradores. Nunca el solicitante.
 * Cada alerta se registra en `alertas_sla` (índice único ticket+tipo) ANTES de notificar,
 * por lo que se envía una sola vez aunque la revisión corra en paralelo.
 *
 * Se ejecuta con `php spark sla:revisar` o automáticamente mientras se usa el sistema
 * (filtro SesionActiva, como máximo cada N minutos).
 */
class RevisorSla
{
    private const CLAVE_CACHE = 'sla_ultima_revision';

    private Notificaciones $config;
    private Notificador $notificador;

    public function __construct(?Notificador $notificador = null)
    {
        $this->config      = config(Notificaciones::class);
        $this->notificador = $notificador ?? new Notificador($this->config);
        helper(['sla', 'formato']);
    }

    /**
     * Revisa solo si pasó el intervalo configurado desde la última revisión (uso en cada petición).
     */
    public function revisarSiCorresponde(): void
    {
        $cache = cache();
        if ($cache->get(self::CLAVE_CACHE) !== null) {
            return;
        }
        $cache->save(self::CLAVE_CACHE, time(), $this->config->slaRevisionCadaMinutos * 60);
        $this->revisar();
    }

    /**
     * @return array{por_vencer: int, vencido: int}
     */
    public function revisar(): array
    {
        $db        = \Config\Database::connect();
        $ahora     = date('Y-m-d H:i:s');
        $porciento = max(1, min(99, $this->config->slaPorcentajeAviso));
        $enviadas  = ['por_vencer' => 0, 'vencido' => 0];

        // Tickets abiertos sin solución que ya consumieron el % de aviso y aún no tienen alerta de vencido
        $candidatos = $db->table('tickets t')
            ->select('t.id_ticket, t.codigo_ticket, t.asunto, t.id_tecnico_asignado, t.fecha_creacion, t.fecha_vencimiento_sla, p.nombre AS prioridad')
            ->join('prioridades p', 'p.id_prioridad = t.id_prioridad', 'left')
            ->join('alertas_sla av', "av.id_ticket = t.id_ticket AND av.tipo = 'vencido'", 'left')
            ->whereIn('t.id_estado', [1, 2])
            ->where('t.fecha_resolucion IS NULL')
            ->where('t.fecha_vencimiento_sla IS NOT NULL')
            ->where('av.id_alerta IS NULL')
            ->where(
                'TIMESTAMPDIFF(SECOND, t.fecha_creacion, ' . $db->escape($ahora) . ') * 100 >= '
                . $porciento . ' * TIMESTAMPDIFF(SECOND, t.fecha_creacion, t.fecha_vencimiento_sla)',
                null,
                false
            )
            ->get()->getResultArray();

        $admins = $this->notificador->idsAdministradores();

        foreach ($candidatos as $t) {
            $vencido = strtotime($t['fecha_vencimiento_sla']) <= time();
            $tipo    = $vencido ? 'vencido' : 'por_vencer';

            // Registrar primero: si ya existía (otra revisión se adelantó), no se notifica de nuevo
            $db->table('alertas_sla')->ignore(true)->insert([
                'id_ticket'   => $t['id_ticket'],
                'tipo'        => $tipo,
                'fecha_envio' => $ahora,
            ]);
            if ($db->affectedRows() !== 1) {
                continue;
            }

            $destinatarios = $admins;
            if (!empty($t['id_tecnico_asignado'])) {
                $destinatarios[] = (int)$t['id_tecnico_asignado'];
            }

            $prioridad = $t['prioridad'] ? " ({$t['prioridad']})" : '';
            if ($vencido) {
                $mensaje = "SLA vencido: el ticket #{$t['codigo_ticket']}{$prioridad} superó su plazo el "
                    . fecha_hora($t['fecha_vencimiento_sla']) . ". Asunto: {$t['asunto']}";
                $tipoNotificacion = 'sla_vencido';
            } else {
                $restante = intdiv(strtotime($t['fecha_vencimiento_sla']) - time(), 60);
                $mensaje = "SLA por vencer: el ticket #{$t['codigo_ticket']}{$prioridad} vence en "
                    . formato_minutos($restante) . ". Asunto: {$t['asunto']}";
                $tipoNotificacion = 'alerta';
            }

            $this->notificador->enviarAVarios($destinatarios, mb_strimwidth($mensaje, 0, 255, '…'), $tipoNotificacion, (int)$t['id_ticket']);
            $enviadas[$tipo]++;
        }

        return $enviadas;
    }
}
