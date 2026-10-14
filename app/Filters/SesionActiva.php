<?php

namespace App\Filters;

use App\Models\UsuarioModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Cierra la sesión de un usuario que fue deshabilitado por el administrador
 * mientras estaba conectado.
 */
class SesionActiva implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return null;
        }

        $usuario = (new UsuarioModel())->select('estado')->find((int)$session->get('id_usuario'));

        if (!$usuario || $usuario['estado'] !== 'activo') {
            // Se quitan los datos de login (no destroy) para que el mensaje flash llegue al login
            $session->remove(['id_usuario', 'nombre_completo', 'correo', 'rol', 'id_rol', 'area', 'isLoggedIn']);
            $session->setFlashdata('error', 'Tu cuenta fue deshabilitada. Contacta al administrador.');
            return redirect()->to('/auth');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
