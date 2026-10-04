<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $correo = $this->request->getPost('correo');
        $password = $this->request->getPost('password');

        // MODO PRUEBA: Validación sin base de datos
        if ($correo === 'admin@empresa.cl' && $password === '123456') {
            $sesionData = [
                'id_usuario'      => 1,
                'nombre_completo' => 'Carlos Ramos (Prueba de Vista)',
                'rol'             => 'Administrador', // Puedes cambiar esto a 'Solicitante' o 'Técnico' para probar los menús
                'isLoggedIn'      => true
            ];
            $session->set($sesionData);
            return redirect()->to('/dashboard');
        } else {
            $session->setFlashdata('error', 'Credenciales de prueba: admin@empresa.cl / 123456');
            return redirect()->to('/auth');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}