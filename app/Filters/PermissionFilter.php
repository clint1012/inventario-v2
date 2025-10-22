<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\PermisosModel;
use App\Models\UsuariosModel;
use App\Models\UsuariosRolesModel;
use App\Models\RolesPermisosModel;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // $arguments[0] debe ser la clave del permiso: e.g. 'bienes.ver'
        if (empty($arguments)) return; // no hay permiso especificado => permitir
        $clave = $arguments[0];

        $session = session();
        $usuario_id = $session->get('id');
        if (!$usuario_id) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $sql = "SELECT COUNT(*) as cnt
                FROM usuarios_roles ur
                JOIN roles_permisos rp ON rp.rol_id = ur.rol_id
                JOIN permisos p ON p.id = rp.permiso_id
                WHERE ur.usuario_id=? AND p.clave=?";
        $res = $db->query($sql, [$usuario_id, $clave])->getRow();

        if (!$res || $res->cnt == 0) {
            // puedes cambiar por una vista bonita o un error 403
            return redirect()->to('/')->with('msg','Sin permiso para la acción: '.$clave);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
