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

    /**
     * Consulta de artículos con autor y ticket de origen, aplicando filtros (para paginar).
     */
    public function queryArticulos(array $f = [])
    {
        $this->select('base_conocimientos.*, usuarios.nombre_completo as autor_nombre, tickets.codigo_ticket as ticket_codigo')
             ->join('usuarios', 'usuarios.id_usuario = base_conocimientos.id_autor')
             ->join('tickets', 'tickets.id_ticket = base_conocimientos.id_ticket_referencia', 'left');

        if (!empty($f['q'])) {
            $this->groupStart()
                 ->like('base_conocimientos.titulo', $f['q'])
                 ->orLike('base_conocimientos.contenido', $f['q'])
                 ->orLike('base_conocimientos.codigo_solucion', $f['q'])
                 ->orLike('tickets.codigo_ticket', $f['q'])
                 ->groupEnd();
        }
        if (!empty($f['id_autor'])) {
            $this->where('base_conocimientos.id_autor', $f['id_autor']);
        }
        if (($f['origen'] ?? '') === 'ticket') {
            $this->where('base_conocimientos.id_ticket_referencia IS NOT NULL');
        } elseif (($f['origen'] ?? '') === 'manual') {
            $this->where('base_conocimientos.id_ticket_referencia IS NULL');
        }
        if (!empty($f['desde'])) {
            $this->where('base_conocimientos.fecha_creacion >=', $f['desde'] . ' 00:00:00');
        }
        if (!empty($f['hasta'])) {
            $this->where('base_conocimientos.fecha_creacion <=', $f['hasta'] . ' 23:59:59');
        }

        return $this->orderBy('base_conocimientos.fecha_creacion', 'DESC')
                    ->orderBy('base_conocimientos.id_solucion', 'DESC');
    }

    /**
     * Usuarios que han escrito al menos un artículo (para el filtro por autor).
     */
    public function getAutores(): array
    {
        return $this->db->table('base_conocimientos')
                        ->select('usuarios.id_usuario, usuarios.nombre_completo')
                        ->join('usuarios', 'usuarios.id_usuario = base_conocimientos.id_autor')
                        ->groupBy('usuarios.id_usuario, usuarios.nombre_completo')
                        ->orderBy('usuarios.nombre_completo', 'ASC')
                        ->get()->getResultArray();
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
