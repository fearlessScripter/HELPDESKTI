<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_ticket' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'codigo_ticket' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'id_solicitante' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_tecnico_asignado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_area_afectada' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'nivel_impacto' => [
                'type'       => 'ENUM',
                'constraint' => ['bajo', 'medio', 'alto'],
                'null'       => false,
            ],
            'nivel_urgencia' => [
                'type'       => 'ENUM',
                'constraint' => ['baja', 'media', 'alta'],
                'null'       => false,
            ],
            'id_prioridad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_estado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'asunto' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'solucion_aplicada' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fecha_creacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_asignacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_resolucion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_cierre' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_vencimiento_sla' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_ticket', true);
        $this->forge->addUniqueKey('codigo_ticket');
        $this->forge->addForeignKey('id_solicitante', 'usuarios', 'id_usuario', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_tecnico_asignado', 'usuarios', 'id_usuario', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('id_area_afectada', 'areas_departamentos', 'id_area', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_prioridad', 'prioridades', 'id_prioridad', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('id_estado', 'estados_ticket', 'id_estado', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('tickets', true);
    }

    public function down()
    {
        $this->forge->dropTable('tickets', true);
    }
}
