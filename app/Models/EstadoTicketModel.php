<?php

namespace App\Models;

use CodeIgniter\Model;

class EstadoTicketModel extends Model
{
    protected $table            = 'estados_ticket';
    protected $primaryKey       = 'id_estado';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['nombre'];
}
