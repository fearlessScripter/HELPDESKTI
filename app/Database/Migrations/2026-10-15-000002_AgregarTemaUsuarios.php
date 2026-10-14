<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Preferencia de tono (claro/oscuro) por usuario.
 */
class AgregarTemaUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addColumn('usuarios', [
            'tema' => [
                'type'       => 'ENUM',
                'constraint' => ['claro', 'oscuro'],
                'null'       => false,
                'default'    => 'claro',
                'after'      => 'estado',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('usuarios', 'tema');
    }
}
