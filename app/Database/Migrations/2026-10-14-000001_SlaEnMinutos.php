<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cambia el SLA de las prioridades de horas decimales a minutos enteros.
 * Ej: 4.00 h -> 240 min, 24.00 h -> 1440 min.
 */
class SlaEnMinutos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('prioridades', [
            'tiempo_sla_minutos' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'after'      => 'nombre',
            ],
        ]);

        $this->db->query('UPDATE prioridades SET tiempo_sla_minutos = ROUND(tiempo_sla_horas * 60)');

        $this->forge->dropColumn('prioridades', 'tiempo_sla_horas');
    }

    public function down()
    {
        $this->forge->addColumn('prioridades', [
            'tiempo_sla_horas' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => false,
                'default'    => 0,
                'after'      => 'nombre',
            ],
        ]);

        $this->db->query('UPDATE prioridades SET tiempo_sla_horas = tiempo_sla_minutos / 60');

        $this->forge->dropColumn('prioridades', 'tiempo_sla_minutos');
    }
}
