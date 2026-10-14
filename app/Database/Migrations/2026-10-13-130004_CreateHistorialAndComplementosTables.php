<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistorialAndComplementosTables extends Migration
{
    public function up()
    {
        // 1. Tabla: historial_tickets
        $this->forge->addField([
            'id_historial' => [
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
            'id_usuario_accion' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'accion' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fecha_registro' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_historial', true);
        $this->forge->addForeignKey('id_ticket', 'tickets', 'id_ticket', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_usuario_accion', 'usuarios', 'id_usuario', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('historial_tickets', true);

        // 2. Tabla: base_conocimientos
        $this->forge->addField([
            'id_solucion' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'codigo_solucion' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => false,
            ],
            'contenido' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'id_autor' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_ticket_referencia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'fecha_creacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_actualizacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_solucion', true);
        $this->forge->addUniqueKey('codigo_solucion');
        $this->forge->addForeignKey('id_autor', 'usuarios', 'id_usuario', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_ticket_referencia', 'tickets', 'id_ticket', 'SET NULL', 'SET NULL');
        $this->forge->createTable('base_conocimientos', true);

        // 3. Tabla: notificaciones
        $this->forge->addField([
            'id_notificacion' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_ticket_referencia' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tipo_alerta' => [
                'type'       => 'ENUM',
                'constraint' => ['info', 'alerta', 'urgente', 'sla_vencido', 'asignacion', 'resolucion'],
                'default'    => 'info',
            ],
            'mensaje' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'leida' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'fecha_creacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_notificacion', true);
        $this->forge->addForeignKey('id_usuario', 'usuarios', 'id_usuario', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_ticket_referencia', 'tickets', 'id_ticket', 'SET NULL', 'SET NULL');
        $this->forge->createTable('notificaciones', true);
    }

    public function down()
    {
        $this->forge->dropTable('notificaciones', true);
        $this->forge->dropTable('base_conocimientos', true);
        $this->forge->dropTable('historial_tickets', true);
    }
}
