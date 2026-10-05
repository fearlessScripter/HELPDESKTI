<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();

        // Si no está logueado, redirigir al login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Pasar los datos de sesión a la vista
        $data = [
            'nombre' => $session->get('nombre_completo'),
            'rol'    => $session->get('rol')
        ];

        $rol = $session->get('rol');

        if ($rol === 'Solicitante') {
            return view('dashboard/solicitante', $data);
        } elseif ($rol === 'Técnico') {
            return view('dashboard/tecnico', $data);
        } else {
            return view('dashboard/admin', $data);
        }
    }
}
