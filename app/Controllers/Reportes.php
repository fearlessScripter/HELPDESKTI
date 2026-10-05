<?php
namespace App\Controllers;

class Reportes extends BaseController
{
    public function index()
    {
        $session = session();
        $data = [
            'nombre' => $session->get('nombre_completo'),
            'rol'    => $session->get('rol')
        ];
        return view('reportes/index', $data);
    }
}