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

        // MODO PRUEBA: Validación sin base de datos para los 3 roles
        if ($password === '123456') {
            
            if ($correo === 'admin@empresa.cl') {
                $sesionData = [
                    'id_usuario'      => 1,
                    'nombre_completo' => 'Carlos Ramos',
                    'rol'             => 'Administrador',
                    'isLoggedIn'      => true
                ];
            } elseif ($correo === 'tecnico@empresa.cl') {
                $sesionData = [
                    'id_usuario'      => 2,
                    'nombre_completo' => 'Luis Técnico',
                    'rol'             => 'Técnico',
                    'isLoggedIn'      => true
                ];
            } elseif ($correo === 'usuario@empresa.cl') {
                $sesionData = [
                    'id_usuario'      => 3,
                    'nombre_completo' => 'Ana Usuario',
                    'rol'             => 'Solicitante',
                    'isLoggedIn'      => true
                ];
            } else {
                $session->setFlashdata('error', 'Correo no existe en el sistema de pruebas.');
                return redirect()->to('/auth');
            }

            // Si el correo es válido, guardamos la sesión y vamos al dashboard
            $session->set($sesionData);
            return redirect()->to('/dashboard');

        } else {
            $session->setFlashdata('error', 'Contraseña incorrecta. (Usa: 123456)');
            return redirect()->to('/auth');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}