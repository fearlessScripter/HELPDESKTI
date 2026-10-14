<?php

/**
 * Búsqueda por palabras para los listados.
 *
 * "impresora piso 2" encuentra los registros que contienen TODAS las palabras,
 * cada una en cualquiera de los campos indicados (asunto, descripción, solicitante...).
 * La colación utf8mb4_general_ci hace que no distinga mayúsculas ni tildes.
 */

if (!function_exists('terminos_busqueda')) {
    /**
     * Separa el texto en palabras útiles (máx. 6), sin '#' inicial (ej: "#TK-1003").
     *
     * @return list<string>
     */
    function terminos_busqueda(string $texto): array
    {
        $palabras = preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $palabras = array_map(static fn ($p) => ltrim($p, '#'), $palabras);
        $palabras = array_filter($palabras, static fn ($p) => $p !== '');

        return array_slice(array_values(array_unique($palabras)), 0, 6);
    }
}

if (!function_exists('aplicar_busqueda')) {
    /**
     * Agrega a la consulta: (campo1 LIKE %p1% OR campo2 LIKE %p1%) AND (... %p2% ...)
     *
     * @param \CodeIgniter\Model|\CodeIgniter\Database\BaseBuilder $query
     * @param list<string> $campos
     */
    function aplicar_busqueda($query, string $texto, array $campos)
    {
        foreach (terminos_busqueda($texto) as $palabra) {
            $query->groupStart();
            foreach ($campos as $i => $campo) {
                $i === 0 ? $query->like($campo, $palabra) : $query->orLike($campo, $palabra);
            }
            $query->groupEnd();
        }

        return $query;
    }
}
