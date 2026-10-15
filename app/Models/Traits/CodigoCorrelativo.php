<?php

namespace App\Models\Traits;

use RuntimeException;

/**
 * Inserta un registro asignándole el siguiente código correlativo (TK-1001, SOL-101...) sin choques.
 *
 * Problema que resuelve: si dos usuarios crean un registro al mismo tiempo, ambos leían el mismo
 * "último código", el segundo chocaba con el índice único y su registro no se guardaba.
 * Ahora el cálculo del código y el guardado ocurren bajo un bloqueo con nombre de MySQL (GET_LOCK):
 * se ejecutan de a uno, sin bloquear tablas y sin riesgo de interbloqueos (deadlocks).
 */
trait CodigoCorrelativo
{
    /**
     * @param array  $datos        Datos del registro (sin el código)
     * @param string $columna      Columna del código (ej. 'codigo_ticket')
     * @param string $prefijo      Prefijo (ej. 'TK-')
     * @param int    $primerNumero Número del primer código si la tabla está vacía (ej. 1001)
     *
     * @return array{id: int, codigo: string}
     */
    protected function insertarConCodigoCorrelativo(array $datos, string $columna, string $prefijo, int $primerNumero): array
    {
        $nombreBloqueo = 'helpdesk_codigo_' . $this->table;
        $obtenido = (int)($this->db->query('SELECT GET_LOCK(?, 10) AS ok', [$nombreBloqueo])->getRow()->ok ?? 0);
        if ($obtenido !== 1) {
            throw new RuntimeException('No se pudo generar el código: el sistema está ocupado, intenta nuevamente.');
        }

        try {
            $inicioNumero = strlen($prefijo) + 1;
            $fila = $this->db->query(
                "SELECT MAX(CAST(SUBSTRING({$columna}, {$inicioNumero}) AS UNSIGNED)) AS ultimo FROM {$this->table} WHERE {$columna} LIKE ?",
                [$prefijo . '%']
            )->getRow();

            $siguiente = max($primerNumero - 1, (int)($fila->ultimo ?? 0)) + 1;
            $datos[$columna] = $prefijo . $siguiente;

            $id = (int)$this->insert($datos);

            return ['id' => $id, 'codigo' => $datos[$columna]];
        } finally {
            $this->db->query('SELECT RELEASE_LOCK(?)', [$nombreBloqueo]);
        }
    }
}
