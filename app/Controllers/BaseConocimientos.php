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
        ];

        return view('base_conocimientos/index', $data);
    }

    public function crear()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if (!in_array($session->get('rol'), ['Administrador', 'Técnico'])) return redirect()->to('/dashboard');

        $titulo    = trim((string)$this->request->getPost('titulo'));
        $contenido = trim((string)$this->request->getPost('contenido'));
        $idUsuario = (int)$session->get('id_usuario');

        if (empty($titulo) || empty($contenido)) {
            $session->setFlashdata('error', 'El título y el contenido son obligatorios.');
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

        $this->kbModel->update($idSolucion, [
            'titulo'              => $titulo,
            'contenido'           => $contenido,
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
        ]);

        $session->setFlashdata('success', "Solución {$articulo['codigo_solucion']} actualizada exitosamente.");
        return redirect()->to('/base-conocimientos');
    }
}