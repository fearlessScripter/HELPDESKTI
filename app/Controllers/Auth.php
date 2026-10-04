<?php

namespace App\Controllers;
use App\Models\UsuarioModel;

class Auth extends BaseController
{
    public function index()
    {
        // Si ya hay sesión, redirigir al dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $model = new UsuarioModel();

        $correo = $this->request->getPost('correo');
        $password = $this->request->getPost('password');

        $usuario = $model->validarLogin($correo, $password);

        if ($usuario) {
            // Guardar datos en la sesión
            $sesionData = [
                'id_usuario'      => $usuario['id_usuario'],
                'nombre_completo' => $usuario['nombre_completo'],
                'rol'             => $usuario['rol'],
                'isLoggedIn'      => true
            ];
            $session->set($sesionData);
            return redirect()->to('/dashboard');
        } else {
            $session->setFlashdata('error', 'Correo o contraseña incorrectos, o usuario inactivo.');
            return redirect()->to('/auth');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}