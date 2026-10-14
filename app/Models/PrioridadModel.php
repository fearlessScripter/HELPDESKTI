<?php

namespace App\Models;

use CodeIgniter\Model;

class PrioridadModel extends Model
{
    protected $table            = 'prioridades';
    protected $primaryKey       = 'id_prioridad';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['nombre', 'tiempo_sla_minutos'];
}
