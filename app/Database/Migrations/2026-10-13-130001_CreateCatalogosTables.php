<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatalogosTables extends Migration
{
    public function up()
    {
        // 1. Tabla: roles
        $this->forge->addField([
            'id_rol' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id_rol', true);
        $this->forge->createTable('roles', true);

        // 2. Tabla: areas_departamentos
        $this->forge->addField([
            'id_area' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default'    => 'activo',
            ],
        ]);
        $this->forge->addKey('id_area', true);
        $this->forge->createTable('areas_departamentos', true);

        // 3. Tabla: estados_ticket
        $this->forge->addField([
            'id_estado' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id_estado', true);
        $this->forge->createTable('estados_ticket', true);

        // 4. Tabla: prioridades
        $this->forge->addField([
            'id_prioridad' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'tiempo_sla_horas' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id_prioridad', true);
        $this->forge->createTable('prioridades', true);
    }

    public function down()
    {
        $this->forge->dropTable('prioridades', true);
        $this->forge->dropTable('estados_ticket', true);
        $this->forge->dropTable('areas_departamentos', true);
        $this->forge->dropTable('roles', true);
    }
}
