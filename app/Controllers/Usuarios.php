<?php
namespace App\Controllers;

class Usuarios extends BaseController
{
    public function index()
    {
        $session = session();
        $data = [
            'nombre' => $session->get('nombre_completo'),
            'rol'    => $session->get('rol')
        ];
        return view('usuarios/index', $data);
    }
}