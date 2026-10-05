<?php
namespace App\Controllers;

class BaseConocimientos extends BaseController
{
    public function index()
    {
        $session = session();
        $data = [
            'nombre' => $session->get('nombre_completo'),
            'rol'    => $session->get('rol')
        ];
        return view('base_conocimientos/index', $data);
    }
}