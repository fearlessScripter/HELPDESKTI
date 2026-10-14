<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseConocimientoModel extends Model
{
    protected $table            = 'base_conocimientos';
    protected $primaryKey       = 'id_solucion';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'codigo_solucion',
        'titulo',
        'contenido',
        'id_autor',
        'id_ticket_referencia',
        'fecha_creacion',
        'fecha_actualizacion',
    ];

    public function getArticulosConAutor()
    {
        return $this->select('base_conocimientos.*, usuarios.nombre_completo as autor_nombre, tickets.codigo_ticket as ticket_codigo')
                    ->join('usuarios', 'usuarios.id_usuario = base_conocimientos.id_autor')
                    ->join('tickets', 'tickets.id_ticket = base_conocimientos.id_ticket_referencia', 'left')
                    ->orderBy('base_conocimientos.fecha_creacion', 'DESC')
                    ->findAll();
    }

    public function existePorTicket(int $idTicket): bool
    {
        return $this->where('id_ticket_referencia', $idTicket)->countAllResults() > 0;
    }

    public function generarCodigo(): string
    {
        $ultimo = $this->orderBy('id_solucion', 'DESC')->first();
        $siguienteNumero = 101;
        if ($ultimo && preg_match('/SOL-(\d+)/', $ultimo['codigo_solucion'], $matches)) {
            $siguienteNumero = (int)$matches[1] + 1;
        }
        return 'SOL-' . $siguienteNumero;
    }
}
