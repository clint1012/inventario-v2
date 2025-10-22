<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use App\Models\RolesModel;
use App\Models\PermisosModel;
use App\Models\UsuariosRolesModel;
use App\Models\RolesPermisosModel;

class Usuarios extends BaseController
{
    protected $usuarios;
    protected $roles;
    protected $permisos;
    protected $usuariosRoles;
    protected $rolesPermisos;

    public function __construct()
    {
        $this->usuarios = new UsuariosModel();
        $this->roles = new RolesModel();
        $this->permisos = new PermisosModel();
        $this->usuariosRoles = new UsuariosRolesModel();
        $this->rolesPermisos = new RolesPermisosModel();
    }

    // Vista principal (lista)
    public function index()
    {
        $data = [
            'usuarios' => $this->usuarios->findAll(),
            'roles'    => $this->roles->findAll(),
            'permisos' => $this->permisos->findAll(),
        ];
        return view('usuarios/index', $data);
    }

    // Devuelve un usuario en JSON
    public function get($id)
    {
        $user = $this->usuarios->find($id);

        if (!$user) {
            return $this->response
                        ->setStatusCode(404)
                        ->setJSON(['ok' => false, 'msg' => 'Usuario no encontrado']);
        }

        // No enviar password si no es necesario (por seguridad)
        unset($user['password']);

        return $this->response->setJSON(['ok' => true, 'data' => $user]);
    }

    // Crear usuario (AJAX) -> devuelve JSON
    public function store()
    {
        $post = $this->request->getPost();

        if (empty($post['usuario']) || empty($post['password']) || empty($post['nombre'])) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'msg' => 'Complete los campos obligatorios']);
        }

        // verificar si usuario ya existe
        $exists = $this->usuarios->where('usuario', $post['usuario'])->first();
        if ($exists) {
            return $this->response->setStatusCode(409)->setJSON(['ok' => false, 'msg' => 'El usuario ya existe']);
        }

        $hash = hash('sha256', $post['password']);

        $insertId = $this->usuarios->insert([
            'usuario'   => $post['usuario'],
            'password'  => $hash,
            'nombre'    => $post['nombre'],
            'correo'    => $post['correo'] ?? null,
            'estado'    => 'activo',
            'creado_en' => date('Y-m-d H:i:s'),
        ]);

        if ($insertId === false) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'msg' => 'Error al crear usuario']);
        }

        return $this->response->setJSON(['ok' => true, 'msg' => 'Usuario creado', 'id' => $insertId]);
    }

    // Actualizar usuario (AJAX)
    public function update($id)
    {
        $post = $this->request->getPost();

        $user = $this->usuarios->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'msg' => 'Usuario no encontrado']);
        }

        $data = [
            'usuario' => $post['usuario'] ?? $user['usuario'],
            'nombre'  => $post['nombre'] ?? $user['nombre'],
            'correo'  => $post['correo'] ?? $user['correo'],
        ];

        if (!empty($post['password'])) {
            $data['password'] = hash('sha256', $post['password']);
        }

        $saved = $this->usuarios->update($id, $data);
        if ($saved === false) {
            return $this->response->setStatusCode(500)->setJSON(['ok' => false, 'msg' => 'Error al actualizar']);
        }

        return $this->response->setJSON(['ok' => true, 'msg' => 'Usuario actualizado']);
    }

    // Toggle estado (activar / inactivar) -> AJAX
    public function toggle($id)
    {
        $user = $this->usuarios->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'msg' => 'Usuario no encontrado']);
        }

        $nuevoEstado = ($user['estado'] === 'activo') ? 'inactivo' : 'activo';
        $this->usuarios->update($id, ['estado' => $nuevoEstado]);

        return $this->response->setJSON(['ok' => true, 'msg' => 'Estado actualizado', 'estado' => $nuevoEstado]);
    }

    // Devuelve roles y roles asignados al usuario
    public function roles($usuario_id)
    {
        $roles = $this->roles->findAll();
        $assignedRows = $this->usuariosRoles->where('usuario_id', $usuario_id)->findAll();
        $assignedIds = array_column($assignedRows, 'rol_id');

        return $this->response->setJSON(['ok' => true, 'roles' => $roles, 'assigned' => $assignedIds]);
    }

    // Guarda roles para usuario (POST usuario_id + roles[] )
    public function saveRoles()
    {
        $post = $this->request->getPost();
        $uid = $post['usuario_id'] ?? null;
        $roles = $post['roles'] ?? [];

        if (!$uid) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'msg' => 'Usuario no recibido']);
        }

        // eliminar previos
        $this->usuariosRoles->where('usuario_id', $uid)->delete();

        foreach ($roles as $rid) {
            $this->usuariosRoles->insert(['usuario_id' => $uid, 'rol_id' => $rid]);
        }

        return $this->response->setJSON(['ok' => true, 'msg' => 'Roles asignados']);
    }

    // Devuelve permisos heredados por roles del usuario (lectura)
    public function permisos($usuario_id)
    {
        $db = \Config\Database::connect();

        // 1. Traer todos los permisos
        $permisos = $this->permisos->findAll();

        // 2. Traer permisos asignados directamente al usuario
        $sql = "SELECT permiso_id FROM usuarios_permisos WHERE usuario_id = ?";
        $rows = $db->query($sql, [$usuario_id])->getResultArray();
        $assigned = array_column($rows, 'permiso_id'); // <-- lista: [1, 3, 5]

        // 3. Agregar campo "permitido" a cada permiso
        foreach ($permisos as &$p) {
            $p['permitido'] = in_array($p['id'], $assigned) ? 1 : 0;
        }

        return $this->response->setJSON([
            'ok' => true,
            'permisos' => $permisos
        ]);
    }

    public function savePermisos()
    {
        $usuario_id = $this->request->getPost('usuario_id');
        $permisos = $this->request->getPost('permisos') ?? []; // array permisos[] del modal

        if (!$usuario_id) {
            return $this->response->setJSON([
                'ok' => false,
                'msg' => 'ID de usuario no recibido'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('usuarios_permisos');

        // 1. Borrar todos los permisos actuales del usuario
        $builder->where('usuario_id', $usuario_id)->delete();

        // 2. Insertar los nuevos
        foreach ($permisos as $permiso_id) {
            $builder->insert([
                'usuario_id' => $usuario_id,
                'permiso_id' => $permiso_id
            ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'msg' => 'Permisos actualizados correctamente'
        ]);
    }

}