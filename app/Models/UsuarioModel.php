<?php

namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $returnType = 'array';
    
    // Los campos que permitiremos modificar
    protected $allowedFields = [
        'nombre_completo', 
        'correo', 
        'password_hash', 
        'rol', 
        'tipo_usuario', 
        'estado'
    ];

    // Método para validar el login usando SHA-256 (como definieron en su arquitectura)
    public function validarLogin($correo, $password)
    {
        $usuario = $this->where('correo', $correo)
                        ->where('estado', 1)
                        ->first();

        // Validar contraseña encriptada en SHA-256
        if ($usuario && hash('sha256', $password) === $usuario['password_hash']) {
            return $usuario;
        }
        
        return false;
    }
}