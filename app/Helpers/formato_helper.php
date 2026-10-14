<?php

/**
 * Formatos de presentación comunes (fechas y nombres).
 * Cambiar aquí el formato lo cambia en todo el sistema.
 */

if (!function_exists('fecha_hora')) {
    /**
     * "2026-10-14 04:24:05" -> "14-10-2026 04:24"
     */
    function fecha_hora(?string $fecha): string
    {
        if (empty($fecha) || ($ts = strtotime($fecha)) === false) {
            return '—';
        }
        return date('d-m-Y H:i', $ts);
    }
}

if (!function_exists('fecha_corta')) {
    /**
     * "2026-10-14 04:24:05" -> "14-10-2026"
     */
    function fecha_corta(?string $fecha): string
    {
        if (empty($fecha) || ($ts = strtotime($fecha)) === false) {
            return '—';
        }
        return date('d-m-Y', $ts);
    }
}

if (!function_exists('fecha_texto')) {
    /**
     * Para frases: "14-10-2026 a las 04:24" (ej: "Creado el 14-10-2026 a las 04:24")
     */
    function fecha_texto(?string $fecha): string
    {
        if (empty($fecha) || ($ts = strtotime($fecha)) === false) {
            return '—';
        }
        return date('d-m-Y', $ts) . ' a las ' . date('H:i', $ts);
    }
}

if (!function_exists('color_prioridad')) {
    /**
     * Colores por prioridad: [fondo, texto]. Urgente rojo oscuro, Alta rojo anaranjado,
     * Media amarillo, Baja verde (texto con contraste legible sobre cada fondo).
     */
    function color_prioridad(?string $nombre): array
    {
        return [
            'Urgente' => ['#8b0000', '#ffffff'],
            'Alta'    => ['#c2410c', '#ffffff'],
            'Media'   => ['#ffc107', '#212529'],
            'Baja'    => ['#198754', '#ffffff'],
        ][$nombre] ?? ['#6c757d', '#ffffff'];
    }
}

if (!function_exists('badge_prioridad')) {
    /**
     * Badge de prioridad con su color. Si se pasa $slaMinutos, agrega el plazo: "Alta (2 días)".
     */
    function badge_prioridad(?string $nombre, ?int $slaMinutos = null, string $prefijoSla = ''): string
    {
        [$fondo, $texto] = color_prioridad($nombre);
        $etiqueta = $nombre ?: 'Sin prioridad';
        if ($slaMinutos) {
            $etiqueta .= ' (' . $prefijoSla . formato_minutos($slaMinutos) . ')';
        }

        return '<span class="badge" style="background-color:' . $fondo . ';color:' . $texto . '">' . esc($etiqueta) . '</span>';
    }
}

if (!function_exists('nombre_con_rol')) {
    /**
     * "Ana Torres" + "Solicitante" -> "Ana Torres (Solicitante)"
     */
    function nombre_con_rol(?string $nombre, ?string $rol): string
    {
        $nombre = trim((string)$nombre);
        return $rol ? "{$nombre} ({$rol})" : $nombre;
    }
}
