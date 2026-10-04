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

        return view('dashboard/index', $data);
    }
}
