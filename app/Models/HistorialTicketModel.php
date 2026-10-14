<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialTicketModel extends Model
{
    protected $table            = 'historial_tickets';
    protected $primaryKey       = 'id_historial';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_ticket',
        'id_usuario_accion',
        'accion',
        'descripcion',
        'fecha_registro',
    ];

    public function registrarAccion(int $idTicket, int $idUsuario, string $accion, ?string $descripcion = null)
    {
        return $this->insert([
            'id_ticket'         => $idTicket,
            'id_usuario_accion' => $idUsuario,
            'accion'            => $accion,
            'descripcion'       => $descripcion,
            'fecha_registro'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function getHistorialPorTicket(int $idTicket)
    {
        return $this->select('historial_tickets.*, usuarios.nombre_completo as usuario_nombre, roles.nombre as usuario_rol')
                    ->join('usuarios', 'usuarios.id_usuario = historial_tickets.id_usuario_accion')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol')
                    ->where('historial_tickets.id_ticket', $idTicket)
                    ->orderBy('historial_tickets.fecha_registro', 'DESC')
                    ->findAll();
    }
}
