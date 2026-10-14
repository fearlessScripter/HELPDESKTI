<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Nueva política de SLA con 4 prioridades (en minutos):
 *   Urgente 24 h (1440) · Alta 2 días (2880) · Media 3 días (4320) · Baja 5 días (7200)
 *
 * Se mantienen los IDs existentes (1 Baja, 2 Media, 3 Alta) y se agrega 4 Urgente,
 * de modo que "id mayor = más prioritario" sigue siendo válido en todo el sistema.
 * También recalcula el vencimiento SLA de los tickets existentes con la nueva política.
 */
class CuatroPrioridadesSla extends Migration
{
    private const NUEVO = [1 => 7200, 2 => 4320, 3 => 2880, 4 => 1440];
    private const ANTERIOR = [1 => 1440, 2 => 240, 3 => 120];

    public function up()
    {
        foreach (self::NUEVO as $id => $minutos) {
            if ($id === 4) {
                $this->db->table('prioridades')->ignore(true)->insert([
                    'id_prioridad'       => 4,
                    'nombre'             => 'Urgente',
                    'tiempo_sla_minutos' => $minutos,
                ]);
            }
            $this->db->table('prioridades')->where('id_prioridad', $id)->update(['tiempo_sla_minutos' => $minutos]);
        }

        $this->recalcularVencimientos();
    }

    public function down()
    {
        // Los tickets en Urgente vuelven a Alta antes de eliminar la prioridad
        $this->db->table('tickets')->where('id_prioridad', 4)->update(['id_prioridad' => 3]);
        $this->db->table('prioridades')->where('id_prioridad', 4)->delete();

        foreach (self::ANTERIOR as $id => $minutos) {
            $this->db->table('prioridades')->where('id_prioridad', $id)->update(['tiempo_sla_minutos' => $minutos]);
        }

        $this->recalcularVencimientos();
    }

    private function recalcularVencimientos(): void
    {
        $this->db->query('
            UPDATE tickets t
            JOIN prioridades p ON p.id_prioridad = t.id_prioridad
            SET t.fecha_vencimiento_sla = DATE_ADD(t.fecha_creacion, INTERVAL p.tiempo_sla_minutos MINUTE)
        ');
    }
}
