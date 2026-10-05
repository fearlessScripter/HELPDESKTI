<?php
namespace App\Controllers;

class Reportes extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if ($session->get('rol') !== 'Administrador') return redirect()->to('/dashboard');

        $data = [
            'nombre' => $session->get('nombre_completo'),
            'rol'    => $session->get('rol')
        ];
        return view('reportes/index', $data);
    }
}