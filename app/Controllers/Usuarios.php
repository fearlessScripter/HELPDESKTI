<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\AreaModel;
use App\Models\NotificacionModel;

class Usuarios extends BaseController
{
    protected UsuarioModel $usuarioModel;
    protected AreaModel $areaModel;
    protected NotificacionModel $notifModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->areaModel    = new AreaModel();
        $this->notifModel   = new NotificacionModel();
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if ($session->get('rol') !== 'Administrador') return redirect()->to('/dashboard');

        $idUsuario = (int)$session->get('id_usuario');

        $usuarios = $this->usuarioModel->select('usuarios.*, roles.nombre as rol_nombre, areas_departamentos.nombre as area_nombre')
                                       ->join('roles', 'roles.id_rol = usuarios.id_rol')
                                       ->join('areas_departamentos', 'areas_departamentos.id_area = usuarios.id_area', 'left')
                                       ->orderBy('usuarios.id_usuario', 'ASC')
                                       ->findAll();

        $areas = $this->areaModel->getActivas();

        $data = [
            'id_usuario'     => $idUsuario,
            'nombre'         => $session->get('nombre_completo'),
            'rol'            => $session->get('rol'),
            'usuarios'       => $usuarios,
            'areas'          => $areas,
            'notificaciones' => $this->notifModel->getPorUsuario($idUsuario, 5),
            'conteoNoLeidas' => $this->notifModel->getConteoNoLeidas($idUsuario),
        ];

        return view('usuarios/index', $data);
    }

    public function crear()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if ($session->get('rol') !== 'Administrador') return redirect()->to('/dashboard');

        $nombreCompleto = trim((string)$this->request->getPost('nombre_completo'));
        $correo         = trim((string)$this->request->getPost('correo'));
        $password       = (string)$this->request->getPost('password');
        $idRol          = (int)$this->request->getPost('id_rol');
        $idArea         = (int)$this->request->getPost('id_area');

        if (empty($nombreCompleto) || empty($correo) || empty($password) || empty($idRol)) {
            $session->setFlashdata('error', 'Todos los campos obligatorios deben ser completados.');
            return redirect()->back();
        }

        // Verificar correo duplicado
        if ($this->usuarioModel->where('correo', $correo)->first()) {
            $session->setFlashdata('error', 'El correo ya se encuentra registrado en el sistema.');
            return redirect()->back();
        }

        $now = date('Y-m-d H:i:s');
        $this->usuarioModel->insert([
            'id_rol'          => $idRol,
            'id_area'         => !empty($idArea) ? $idArea : null,
            'nombre_completo' => $nombreCompleto,
            'correo'          => $correo,
            'password_hash'   => hash('sha256', $password),
            'estado'          => 'activo',
            'fecha_creacion'  => $now,
        ]);

        $session->setFlashdata('success', "Usuario {$nombreCompleto} creado exitosamente.");
        return redirect()->to('/usuarios');
    }
}