<?php

namespace App\Commands;

use App\Libraries\RevisorSla;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * php spark sla:revisar
 *
 * Genera las alertas de SLA (por vencer / vencido). Puede programarse en el
 * Programador de tareas de Windows (ej. cada 15 minutos) para que las alertas
 * salgan aunque nadie esté usando el sistema.
 */
class RevisarSla extends BaseCommand
{
    protected $group       = 'HelpDesk';
    protected $name        = 'sla:revisar';
    protected $description = 'Revisa los tickets abiertos y envía las alertas de SLA por vencer y vencido.';

    public function run(array $params)
    {
        $resultado = (new RevisorSla())->revisar();

        CLI::write('Alertas "por vencer" enviadas: ' . $resultado['por_vencer'], 'yellow');
        CLI::write('Alertas "vencido" enviadas:    ' . $resultado['vencido'], 'red');
        CLI::write('Revisión de SLA completada (' . date('d-m-Y H:i') . ').', 'green');
    }
}
