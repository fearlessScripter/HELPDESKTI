<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Registro de alertas de SLA ya enviadas: cada ticket recibe como máximo una alerta
 * "por_vencer" y una "vencido". El índice único impide duplicados incluso si dos
 * revisiones corren al mismo tiempo.
 */
class CrearAlertasSla extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_alerta' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_ticket' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['por_vencer', 'vencido'],
                'null'       => false,
            ],
            'fecha_envio' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id_alerta', true);
        $this->forge->addUniqueKey(['id_ticket', 'tipo']);
        $this->forge->addForeignKey('id_ticket', 'tickets', 'id_ticket', 'CASCADE', 'CASCADE');
        $this->forge->createTable('alertas_sla', true);
    }

    public function down()
    {
        $this->forge->dropTable('alertas_sla', true);
    }
}
