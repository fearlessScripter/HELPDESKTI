<?php

/**
 * Tono de la interfaz (claro / oscuro).
 */

if (!function_exists('tema_actual')) {
    /**
     * 'claro' u 'oscuro'. Con sesión: la preferencia guardada del usuario.
     * Sin sesión (login): el último tono usado en este navegador (cookie).
     */
    function tema_actual(): string
    {
        $tema = session()->get('isLoggedIn')
            ? session()->get('tema')
            : service('request')->getCookie('tema');

        return $tema === 'oscuro' ? 'oscuro' : 'claro';
    }
}

if (!function_exists('tema_bootstrap')) {
    /**
     * Valor para el atributo data-bs-theme de Bootstrap ('light' / 'dark').
     */
    function tema_bootstrap(): string
    {
        return tema_actual() === 'oscuro' ? 'dark' : 'light';
    }
}
