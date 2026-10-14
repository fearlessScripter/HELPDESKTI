<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

/**
 * Preferencias del usuario conectado (menú de perfil, arriba a la derecha).
 */
class Perfil extends BaseController
{
    /**
     * Guarda el tono elegido (claro/oscuro). Responde JSON: el cambio ya se aplicó en pantalla.
     */
    public function tema()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['ok' => false]);
        }

        $tema = (string)$this->request->getPost('tema');
        if (!in_array($tema, ['claro', 'oscuro'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => 'Tono no válido']);
        }

        (new UsuarioModel())->update((int)$session->get('id_usuario'), ['tema' => $tema]);
        $session->set('tema', $tema);

        return $this->response->setJSON(['ok' => true, 'tema' => $tema]);
    }
}
