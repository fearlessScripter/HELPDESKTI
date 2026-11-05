<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Envío único del formulario "Nuevo Ticket".
 *
 * cada formulario lleva un token aleatorio (token_envio). El índice único impide que el MISMO
 * formulario cree dos tickets aunque llegue varias veces (doble clic, varios Enter, reenviar con F5).
 * Formularios distintos (otro usuario, otra pestaña) tienen tokens distintos y no se afectan entre sí.
 * los tickets anteriores quedan con NULL, que MySQL permite repetir en un índice único.
 */
class TokenEnvioTickets extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tickets', [
            'token_envio' => [
                'type'       => 'CHAR',
                'constraint' => 32,
                'null'       => true,
                'after'      => 'codigo_ticket',
            ],
        ]);

        $this->forge->addUniqueKey('token_envio', 'uq_tickets_token_envio');
        $this->forge->processIndexes('tickets');
    }

    public function down()
    {
        $this->forge->dropKey('tickets', 'uq_tickets_token_envio', false);
        $this->forge->dropColumn('tickets', 'token_envio');
    }
}
