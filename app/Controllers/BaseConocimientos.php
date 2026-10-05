<?php
namespace App\Controllers;

class BaseConocimientos extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if (!in_array($session->get('rol'), ['Administrador', 'Técnico'])) return redirect()->to('/dashboard');

        $data = [
            'nombre' => $session->get('nombre_completo'),
            'rol'    => $session->get('rol')
        ];
        return view('base_conocimientos/index', $data);
    }
}