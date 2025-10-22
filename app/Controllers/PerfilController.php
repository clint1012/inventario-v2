<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use CodeIgniter\Controller;

class PerfilController extends Controller
{
    protected $usuarios;

    public function __construct()
    {
        $this->usuarios = new UsuariosModel();
    }

    // Muestra la vista/modal (puede ser una página o incluir un partial)
    public function index()
    {
        // Aseguramos que haya sesión (si tienes filtro 'auth' quizá no hace falta)
        if (! session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $usuarioId = session()->get('usuario_id');
        $user = $this->usuarios->find($usuarioId);

        return view('perfil/index', ['usuario' => $user]);
    }

    // Actualiza nombre y correo (AJAX -> JSON)
    public function actualizarPerfil()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $id = session()->get('usuario_id');
        if (! $id) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'Sesión no encontrada']);
        }

        $nombre = $this->request->getPost('nombre');
        $correo = $this->request->getPost('correo');

        // Validaciones básicas
        if (empty($nombre)) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'El nombre es obligatorio']);
        }

        $data = [
            'nombre' => $nombre,
            'correo' => $correo ?: null
        ];

        $saved = $this->usuarios->update($id, $data);

        if ($saved === false) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'Error al actualizar']);
        }

        // Actualizar sesión
        session()->set([
            'nombre' => $nombre,
            'correo' => $correo
        ]);

        return $this->response->setJSON(['ok' => true, 'msg' => 'Perfil actualizado']);
    }

    // Actualiza foto (upload) (AJAX -> JSON)
    public function actualizarFoto()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $id = session()->get('usuario_id');
        if (! $id) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'Sesión no encontrada']);
        }

        $file = $this->request->getFile('foto');

        if (! $file || ! $file->isValid()) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'No se subió archivo o está dañado']);
        }

        // Validar tipo y tamaño
        $allowed = ['image/jpeg','image/jpg','image/png'];
        if (! in_array($file->getMimeType(), $allowed)) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'Formato no permitido. Usa JPG/PNG.']);
        }

        $maxSize = 2 * 1024 * 1024; // 2 MB
        if ($file->getSize() > $maxSize) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'Archivo demasiado grande (máx 2MB).']);
        }

        // Preparar nombre y mover
        $ext = $file->getExtension();
        $targetFolder = FCPATH . 'uploads/usuarios/';
        // asegurar carpeta
        if (! is_dir($targetFolder)) {
            mkdir($targetFolder, 0755, true);
        }

        $newName = 'user_' . $id . '.' . $ext;

        // Borrar anterior si existe (opcional)
        $user = $this->usuarios->find($id);
        if (! empty($user['foto'])) {
            $old = $targetFolder . $user['foto'];
            if (is_file($old)) @unlink($old);
        }

        // mover (sobrescribe si existe)
        try {
            $file->move($targetFolder, $newName, true);
        } catch (\Exception $e) {
            return $this->response->setJSON(['ok' => false, 'msg' => 'Error al mover el archivo: ' . $e->getMessage()]);
        }

        // Guardar nombre en DB
        $this->usuarios->update($id, ['foto' => $newName]);

        // Actualizar sesión
        session()->set('foto', $newName);

        // Retornar URL accesible
        $url = base_url('uploads/usuarios/' . $newName);

        return $this->response->setJSON(['ok' => true, 'msg' => 'Foto actualizada', 'url' => $url, 'filename' => $newName]);
    }
}
