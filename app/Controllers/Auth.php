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

    /** Intentos fallidos permitidos (por IP) antes del bloqueo temporal. */
    private const MAX_INTENTOS_FALLIDOS = 5;

    /** Duración del bloqueo del inicio de sesión tras agotar los intentos. */
    private const MINUTOS_BLOQUEO = 5;

    public function login()
    {
        $session = session();
        $cache   = cache();
        $ip      = md5($this->request->getIPAddress());
        $claveFallos  = 'login_fallos_' . $ip;
        $claveBloqueo = 'login_bloqueo_' . $ip;

        // Protección contra fuerza bruta: mientras dure el bloqueo no se revisa ninguna contraseña
        $bloqueadoHasta = (int)$cache->get($claveBloqueo);
        if ($bloqueadoHasta > time()) {
            $session->setFlashdata('error', 'Por seguridad, el inicio de sesión está bloqueado tras '
                . self::MAX_INTENTOS_FALLIDOS . ' intentos fallidos. Inténtalo de nuevo en '
                . $this->tiempoRestante($bloqueadoHasta - time()) . '.');
            return redirect()->to('/auth');
        }

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
                'tema'            => $usuario['tema'] ?? 'claro',
                'isLoggedIn'      => true,
            ];

            // Inicio correcto: se reinicia el contador de intentos fallidos de esta IP
            $cache->delete($claveFallos);

            // Nuevo ID de sesión al autenticarse (evita la fijación de sesión) y se descarta el anterior
            $session->regenerate(true);
            $session->set($sesionData);
            return redirect()->to('/dashboard');
        }

        // Intento fallido: se cuenta (el contador se olvida tras MINUTOS_BLOQUEO sin nuevos fallos)
        $fallos = (int)$cache->get($claveFallos) + 1;
        if ($fallos >= self::MAX_INTENTOS_FALLIDOS) {
            $segundos = self::MINUTOS_BLOQUEO * 60;
            $cache->save($claveBloqueo, time() + $segundos, $segundos);
            $cache->delete($claveFallos);
            $session->setFlashdata('error', 'Credenciales incorrectas. Superaste los ' . self::MAX_INTENTOS_FALLIDOS
                . ' intentos permitidos: por seguridad, el inicio de sesión queda bloqueado por '
                . self::MINUTOS_BLOQUEO . ' minutos.');
            return redirect()->to('/auth');
        }

        // No se informa cuántos intentos quedan (no dar pistas a quien intenta adivinar contraseñas)
        $cache->save($claveFallos, $fallos, self::MINUTOS_BLOQUEO * 60);
        $session->setFlashdata('error', 'Credenciales incorrectas o usuario inactivo.');
        return redirect()->to('/auth');
    }

    /**
     * 272 -> "4 min 32 s"; 45 -> "45 s"
     */
    private function tiempoRestante(int $segundos): string
    {
        $min = intdiv($segundos, 60);
        $seg = $segundos % 60;
        if ($min === 0) {
            return "{$seg} s";
        }
        return $seg === 0 ? "{$min} min" : "{$min} min {$seg} s";
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}