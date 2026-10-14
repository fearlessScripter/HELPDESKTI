<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\AreaModel;
use App\Models\NotificacionModel;
use App\Models\TicketModel;

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

    /**
     * Verifica sesión de Administrador. Devuelve una redirección si no corresponde.
     */
    private function soloAdmin()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) return redirect()->to('/auth');
        if ($session->get('rol') !== 'Administrador') return redirect()->to('/dashboard');
        return null;
    }

    public function index()
    {
        if ($r = $this->soloAdmin()) return $r;

        $session   = session();
        $idUsuario = (int)$session->get('id_usuario');

        $filtros = [
            'q'          => trim((string)$this->request->getGet('q')),
            'id_rol'     => (int)$this->request->getGet('id_rol'),
            'id_area'    => (string)$this->request->getGet('id_area'),
            'estado'     => in_array($this->request->getGet('estado'), ['activo', 'inactivo'], true) ? $this->request->getGet('estado') : '',
            'por_pagina' => $this->porPaginaGet(),
        ];

        $query = $this->usuarioModel->select('usuarios.*, roles.nombre as rol_nombre, areas_departamentos.nombre as area_nombre')
                                    ->join('roles', 'roles.id_rol = usuarios.id_rol')
                                    ->join('areas_departamentos', 'areas_departamentos.id_area = usuarios.id_area', 'left');

        if ($filtros['q'] !== '') {
            aplicar_busqueda($query, $filtros['q'], [
                'usuarios.nombre_completo',
                'usuarios.correo',
                'roles.nombre',
                'areas_departamentos.nombre',
            ]);
        }
        if ($filtros['id_rol']) {
            $query->where('usuarios.id_rol', $filtros['id_rol']);
        }
        if ($filtros['id_area'] === 'sin') {
            $query->where('usuarios.id_area IS NULL');
        } elseif ((int)$filtros['id_area']) {
            $query->where('usuarios.id_area', (int)$filtros['id_area']);
        }
        if ($filtros['estado'] !== '') {
            $query->where('usuarios.estado', $filtros['estado']);
        }

        $usuarios = $query->orderBy('usuarios.estado', 'ASC') // activos primero
                          ->orderBy('usuarios.nombre_completo', 'ASC')
                          ->paginate($filtros['por_pagina']);

        $data = [
            'id_usuario'     => $idUsuario,
            'nombre'         => $session->get('nombre_completo'),
            'rol'            => $session->get('rol'),
            'usuarios'       => $usuarios,
            'pager'          => $this->usuarioModel->pager,
            'filtros'        => $filtros,
            'areas'          => $this->areaModel->getActivas(),
            'notificaciones' => $this->notifModel->getPorUsuario($idUsuario, 5),
            'conteoNoLeidas' => $this->notifModel->getConteoNoLeidas($idUsuario),
        ];

        return view('usuarios/index', $data);
    }

    public function crear()
    {
        if ($r = $this->soloAdmin()) return $r;
        $session = session();

        $nombreCompleto = trim((string)$this->request->getPost('nombre_completo'));
        $correo         = trim((string)$this->request->getPost('correo'));
        $password       = (string)$this->request->getPost('password');
        $idRol          = (int)$this->request->getPost('id_rol');
        $idArea         = (int)$this->request->getPost('id_area');

        if (empty($nombreCompleto) || empty($correo) || empty($password) || empty($idRol)) {
            $session->setFlashdata('error', 'Todos los campos obligatorios deben ser completados.');
            return redirect()->back();
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || !in_array($idRol, [1, 2, 3], true)) {
            $session->setFlashdata('error', 'El correo o el rol indicado no es válido.');
            return redirect()->back();
        }

        if (strlen($password) < 6) {
            $session->setFlashdata('error', 'La contraseña debe tener al menos 6 caracteres.');
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

    /**
     * Editar datos del usuario (nombre, correo, rol y área).
     */
    public function actualizar()
    {
        if ($r = $this->soloAdmin()) return $r;
        $session = session();

        $id             = (int)$this->request->getPost('id_usuario');
        $nombreCompleto = trim((string)$this->request->getPost('nombre_completo'));
        $correo         = trim((string)$this->request->getPost('correo'));
        $idRol          = (int)$this->request->getPost('id_rol');
        $idArea         = (int)$this->request->getPost('id_area');

        $usuario = $this->usuarioModel->find($id);
        if (!$usuario) {
            $session->setFlashdata('error', 'El usuario no existe.');
            return redirect()->back();
        }

        if (empty($nombreCompleto) || !filter_var($correo, FILTER_VALIDATE_EMAIL) || !in_array($idRol, [1, 2, 3], true)) {
            $session->setFlashdata('error', 'Revisa los datos: nombre, correo y rol son obligatorios y deben ser válidos.');
            return redirect()->back();
        }

        if ($this->usuarioModel->where('correo', $correo)->where('id_usuario !=', $id)->first()) {
            $session->setFlashdata('error', 'El correo ya pertenece a otro usuario.');
            return redirect()->back();
        }

        // El administrador no puede quitarse su propio rol (evita quedar sin acceso)
        if ($id === (int)$session->get('id_usuario') && $idRol !== (int)$usuario['id_rol']) {
            $session->setFlashdata('error', 'No puedes cambiar tu propio rol.');
            return redirect()->back();
        }

        // Un técnico con tickets en proceso no puede dejar de ser técnico
        if ((int)$usuario['id_rol'] === 2 && $idRol !== 2 && ($pendientes = $this->ticketsEnProceso($id)) > 0) {
            $session->setFlashdata('error', "No se puede cambiar el rol: el técnico tiene {$pendientes} ticket(s) En Proceso. Reasígnalos primero.");
            return redirect()->back();
        }

        $this->usuarioModel->update($id, [
            'nombre_completo' => $nombreCompleto,
            'correo'          => $correo,
            'id_rol'          => $idRol,
            'id_area'         => $idArea ?: null,
        ]);

        $session->setFlashdata('success', "Datos de {$nombreCompleto} actualizados correctamente.");
        return redirect()->back();
    }

    /**
     * Activar / desactivar un usuario. Un usuario inactivo no puede iniciar sesión.
     */
    public function cambiarEstado()
    {
        if ($r = $this->soloAdmin()) return $r;
        $session = session();

        $id      = (int)$this->request->getPost('id_usuario');
        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            $session->setFlashdata('error', 'El usuario no existe.');
            return redirect()->back();
        }

        if ($id === (int)$session->get('id_usuario')) {
            $session->setFlashdata('error', 'No puedes deshabilitar tu propia cuenta.');
            return redirect()->back();
        }

        $nuevoEstado = $usuario['estado'] === 'activo' ? 'inactivo' : 'activo';

        if ($nuevoEstado === 'inactivo' && (int)$usuario['id_rol'] === 2 && ($pendientes = $this->ticketsEnProceso($id)) > 0) {
            $session->setFlashdata('error', "No se puede deshabilitar a {$usuario['nombre_completo']}: tiene {$pendientes} ticket(s) En Proceso. Reasígnalos primero.");
            return redirect()->back();
        }

        $this->usuarioModel->update($id, ['estado' => $nuevoEstado]);

        $accion = $nuevoEstado === 'activo' ? 'habilitado' : 'deshabilitado';
        $session->setFlashdata('success', "Usuario {$usuario['nombre_completo']} {$accion} correctamente.");
        return redirect()->back();
    }

    /**
     * El administrador asigna una nueva contraseña al usuario.
     */
    public function restablecerPassword()
    {
        if ($r = $this->soloAdmin()) return $r;
        $session = session();

        $id        = (int)$this->request->getPost('id_usuario');
        $password  = (string)$this->request->getPost('password');
        $confirmar = (string)$this->request->getPost('password_confirmar');

        $usuario = $this->usuarioModel->find($id);
        if (!$usuario) {
            $session->setFlashdata('error', 'El usuario no existe.');
            return redirect()->back();
        }

        if (strlen($password) < 6) {
            $session->setFlashdata('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            return redirect()->back();
        }

        if ($password !== $confirmar) {
            $session->setFlashdata('error', 'Las contraseñas no coinciden.');
            return redirect()->back();
        }

        $this->usuarioModel->update($id, ['password_hash' => hash('sha256', $password)]);

        $session->setFlashdata('success', "Contraseña de {$usuario['nombre_completo']} restablecida correctamente.");
        return redirect()->back();
    }

    private function ticketsEnProceso(int $idTecnico): int
    {
        return (new TicketModel())->where('id_tecnico_asignado', $idTecnico)
                                  ->where('id_estado', 2)
                                  ->countAllResults();
    }
}
