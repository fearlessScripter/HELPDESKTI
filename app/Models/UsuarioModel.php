<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id_usuario';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    
    protected $allowedFields = [
        'id_rol',
        'id_area',
        'nombre_completo',
        'correo',
        'password_hash',
        'estado',
        'fecha_creacion',
    ];

    /**
     * Valida login del usuario por correo y contraseña (SHA-256)
     * e incluye la información de su rol y área.
     */
    public function validarLogin(string $correo, string $password)
    {
        $usuario = $this->select('usuarios.*, roles.nombre as rol, areas_departamentos.nombre as area')
                        ->join('roles', 'roles.id_rol = usuarios.id_rol')
                        ->join('areas_departamentos', 'areas_departamentos.id_area = usuarios.id_area', 'left')
                        ->where('usuarios.correo', $correo)
                        ->where('usuarios.estado', 'activo')
                        ->first();

        if ($usuario && hash('sha256', $password) === $usuario['password_hash']) {
            return $usuario;
        }

        return false;
    }

    /**
     * Obtiene todos los técnicos activos (id_rol = 2)
     */
    public function getTecnicos()
    {
        return $this->select('usuarios.*, areas_departamentos.nombre as area')
                    ->join('areas_departamentos', 'areas_departamentos.id_area = usuarios.id_area', 'left')
                    ->where('usuarios.id_rol', 2)
                    ->where('usuarios.estado', 'activo')
                    ->findAll();
    }
}