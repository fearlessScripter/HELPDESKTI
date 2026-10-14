<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_usuario' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_rol' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_area' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nombre_completo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'correo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default'    => 'activo',
            ],
            'fecha_creacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_usuario', true);
        $this->forge->addUniqueKey('correo');
        $this->forge->addForeignKey('id_rol', 'roles', 'id_rol', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_area', 'areas_departamentos', 'id_area', 'SET NULL', 'SET NULL');
        $this->forge->createTable('usuarios', true);
    }

    public function down()
    {
        $this->forge->dropTable('usuarios', true);
    }
}
