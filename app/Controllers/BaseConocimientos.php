<?php

namespace App\Controllers;

use App\Models\BaseConocimientoModel;
use App\Models\NotificacionModel;

class BaseConocimientos extends BaseController
{
    protected BaseConocimientoModel $kbModel;
    protected NotificacionModel $notifModel;

    public function __construct()
    {
        $this->kbModel = new BaseConocimientoModel();
        $this->notifModel = new NotificacionModel();
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if (!in_array($session->get('rol'), ['Administrador', 'Técnico'])) return redirect()->to('/dashboard');

        $idUsuario = (int)$session->get('id_usuario');

        $origen  = (string)$this->request->getGet('origen');
        $filtros = [
            'q'          => trim((string)$this->request->getGet('q')),
            'id_autor'   => (int)$this->request->getGet('id_autor'),
            'origen'     => in_array($origen, ['ticket', 'manual'], true) ? $origen : '',
            'desde'      => $this->fechaGet('desde'),
            'hasta'      => $this->fechaGet('hasta'),
            'por_pagina' => $this->porPaginaGet(),
        ];

        $articulos = $this->kbModel->queryArticulos($filtros)->paginate($filtros['por_pagina']);

        $data = [
            'id_usuario'     => $idUsuario,
            'nombre'         => $session->get('nombre_completo'),
            'rol'            => $session->get('rol'),
            'articulos'      => $articulos,
            'pager'          => $this->kbModel->pager,
            'filtros'        => $filtros,
            'autores'        => $this->kbModel->getAutores(),
            'notificaciones' => $this->notifModel->getPorUsuario($idUsuario, 5),
            'conteoNoLeidas' => $this->notifModel->getConteoNoLeidas($idUsuario),
            // Token de envío único del formulario "Nueva solución" (ver crear())
            'tokenEnvio'     => bin2hex(random_bytes(16)),
        ];

        return view('base_conocimientos/index', $data);
    }

    public function crear()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if (!in_array($session->get('rol'), ['Administrador', 'Técnico'])) return redirect()->to('/dashboard');

        // Envío único: si el mismo formulario llega varias veces (doble clic, varios Enter, Atrás y reenviar),
        // solo el primero crea la solución. Los formularios ya usados se recuerdan en la sesión del usuario.
        // Funciona porque las solicitudes de una misma sesión se atienden de a una (la sesión queda
        // bloqueada mientras se procesa cada una): la segunda siempre ve lo que guardó la primera.
        $token = (string)$this->request->getPost('token_envio');
        if (!preg_match('/^[0-9a-f]{32}$/', $token)) {
            $session->setFlashdata('error', 'El formulario expiró. Vuelve a completarlo y envíalo nuevamente.');
            return redirect()->to('/base-conocimientos');
        }
        $enviados = (array)$session->get('kb_envios_usados');
        if (isset($enviados[$token])) {
            log_message('notice', "Envío repetido del formulario ignorado: solución {$enviados[$token]} (usuario {$session->get('id_usuario')}).");
            $session->setFlashdata('success', "Solución {$enviados[$token]} registrada exitosamente.");
            return redirect()->to('/base-conocimientos');
        }

        $titulo    = trim((string)$this->request->getPost('titulo'));
        $contenido = trim((string)$this->request->getPost('contenido'));
        $idUsuario = (int)$session->get('id_usuario');

        if (empty($titulo) || empty($contenido)) {
            $session->setFlashdata('error', 'El título y el contenido son obligatorios.');
            return redirect()->back();
        }

        // El título se guarda en un campo de 200 caracteres: sin este control, la base se cortaría sin avisar
        if (mb_strlen($titulo) > 200) {
            $session->setFlashdata('error', 'El título puede tener como máximo 200 caracteres.');
            return redirect()->back();
        }

        $now = date('Y-m-d H:i:s');

        $creado = $this->kbModel->crearConCodigo([
            'titulo'               => $titulo,
            'contenido'            => $contenido,
            'id_autor'             => $idUsuario,
            'id_ticket_referencia' => null,
            'fecha_creacion'       => $now,
            'fecha_actualizacion'  => $now,
        ]);

        // Se recuerdan los últimos 20 formularios enviados (token => código de la solución creada)
        $enviados[$token] = $creado['codigo'];
        $session->set('kb_envios_usados', array_slice($enviados, -20, null, true));

        $session->setFlashdata('success', "Solución {$creado['codigo']} registrada exitosamente.");
        return redirect()->to('/base-conocimientos');
    }

    /**
     * Modificar una solución existente: exclusivo del Administrador.
     */
    public function actualizar()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if ($session->get('rol') !== 'Administrador') {
            $session->setFlashdata('error', 'Solo el administrador puede modificar soluciones existentes.');
            return redirect()->to('/base-conocimientos');
        }

        $idSolucion = (int)$this->request->getPost('id_solucion');
        $titulo     = trim((string)$this->request->getPost('titulo'));
        $contenido  = trim((string)$this->request->getPost('contenido'));

        $articulo = $this->kbModel->find($idSolucion);
        if (!$articulo) {
            $session->setFlashdata('error', 'La solución que intentas modificar no existe.');
            return redirect()->to('/base-conocimientos');
        }

        if (empty($titulo) || empty($contenido)) {
            $session->setFlashdata('error', 'El título y el contenido son obligatorios.');
            return redirect()->to('/base-conocimientos');
        }

        if (mb_strlen($titulo) > 200) {
            $session->setFlashdata('error', 'El título puede tener como máximo 200 caracteres.');
            return redirect()->to('/base-conocimientos');
        }

        $this->kbModel->update($idSolucion, [
            'titulo'              => $titulo,
            'contenido'           => $contenido,
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
        ]);

        $session->setFlashdata('success', "Solución {$articulo['codigo_solucion']} actualizada exitosamente.");
        return redirect()->to('/base-conocimientos');
    }
}