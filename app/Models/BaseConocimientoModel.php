<?php

namespace App\Models;

use App\Models\Traits\CodigoCorrelativo;
use CodeIgniter\Model;

class BaseConocimientoModel extends Model
{
    use CodigoCorrelativo;

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
        $this->select('base_conocimientos.*, usuarios.nombre_completo as autor_nombre, roles.nombre as autor_rol,
                       tickets.codigo_ticket as ticket_codigo, tickets.id_solicitante, tickets.id_tecnico_asignado')
             ->join('usuarios', 'usuarios.id_usuario = base_conocimientos.id_autor')
             ->join('roles', 'roles.id_rol = usuarios.id_rol')
             ->join('tickets', 'tickets.id_ticket = base_conocimientos.id_ticket_referencia', 'left');

        if (!empty($f['q'])) {
            helper('busqueda');
            aplicar_busqueda($this, $f['q'], [
                'base_conocimientos.titulo',
                'base_conocimientos.contenido',
                'base_conocimientos.codigo_solucion',
                'tickets.codigo_ticket',
                'usuarios.nombre_completo',
            ]);
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

    /**
     * Crea el artículo asignándole el siguiente código (SOL-101, SOL-102...) sin choques entre
     * creaciones simultáneas (ej. dos tickets cerrados al mismo tiempo).
     *
     * @return array{id: int, codigo: string}
     */
    public function crearConCodigo(array $datos): array
    {
        return $this->insertarConCodigoCorrelativo($datos, 'codigo_solucion', 'SOL-', 101);
    }
}
