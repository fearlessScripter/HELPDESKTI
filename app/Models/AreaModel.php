<?php

namespace App\Models;

use CodeIgniter\Model;

class AreaModel extends Model
{
    protected $table            = 'areas_departamentos';
    protected $primaryKey       = 'id_area';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['nombre', 'estado'];

    public function getActivas()
    {
        return $this->where('estado', 'activo')->findAll();
    }
}
