<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

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
        $correo = trim((string)$this->request->getPost('correo'));
        $password = (string)$this->request->getPost('password');

        if (empty($correo) || empty($password)) {
            $session->setFlashdata('error', 'Por favor ingresa tu correo y contraseña.');
            return redirect()->to('/auth');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->validarLogin($correo, $password);

        if ($usuario) {
            $sesionData = [
                'id_usuario'      => (int)$usuario['id_usuario'],
                'nombre_completo' => $usuario['nombre_completo'],
                'correo'          => $usuario['correo'],
                'rol'             => $usuario['rol'], // 'Administrador', 'Técnico', 'Solicitante'
                'id_rol'          => (int)$usuario['id_rol'],
                'area'            => $usuario['area'] ?? null,
                'isLoggedIn'      => true,
            ];

            $session->set($sesionData);
            return redirect()->to('/dashboard');
        }

        $session->setFlashdata('error', 'Credenciales incorrectas o usuario inactivo.');
        return redirect()->to('/auth');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}