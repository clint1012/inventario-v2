<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LicenciasModel;
use App\Models\AsignacionModel;

class Notificaciones extends BaseController
{
    /**
     * Enviar notificaciones de licencias por vencer
     */
    public function enviarLicenciasVencer()
    {
        $licenciasModel = new LicenciasModel();
        $diasAviso = 30;

        $licencias = $licenciasModel
            ->where('fecha_expiracion IS NOT NULL')
            ->where('fecha_expiracion <=', date('Y-m-d', strtotime("+$diasAviso days")))
            ->where('fecha_expiracion >=', date('Y-m-d'))
            ->findAll();

        if (empty($licencias)) {
            log_message('info', 'No hay licencias próximas a vencer');
            return 0;
        }

        // Obtener correos de administradores
        $correos = $this->obtenerCorreosAdministradores();

        if (empty($correos)) {
            log_message('error', 'No hay correos de administradores configurados');
            return 0;
        }

        $email = \Config\Services::email();
        $enviados = 0;

        foreach ($correos as $correo) {
            $email->clear();
            $email->setTo($correo);
            $email->setSubject('⚠️ Licencias de Software Próximas a Vencer');

            $mensaje = view('emails/licencias_vencer', [
                'licencias' => $licencias,
                'diasAviso' => $diasAviso
            ]);

            $email->setMessage($mensaje);

            if ($email->send()) {
                $enviados++;
                log_message('info', "Email de licencias enviado a: $correo");
            } else {
                log_message('error', "Error al enviar email a: $correo - " . $email->printDebugger(['headers']));
            }
        }

        return $enviados;
    }

    /**
     * Enviar notificaciones de préstamos por vencer
     */
    public function enviarPrestamosVencer()
    {
        $asignacionModel = new AsignacionModel();
        
        $prestamos = $asignacionModel
            ->select('movimientos.*, personas.nombre_completo, personas.correo')
            ->join('personas', 'personas.id = movimientos.id_personas', 'left')
            ->where('movimientos.tipo', 'prestamo')
            ->where('movimientos.estado', 'activo')
            ->where('movimientos.fecha_limite IS NOT NULL')
            ->where('movimientos.fecha_limite <=', date('Y-m-d', strtotime('+7 days')))
            ->where('movimientos.fecha_limite >=', date('Y-m-d'))
            ->findAll();

        if (empty($prestamos)) {
            log_message('info', 'No hay préstamos próximos a vencer');
            return 0;
        }

        $email = \Config\Services::email();
        $enviados = 0;

        // Agrupar por usuario
        $prestamosPorUsuario = [];
        foreach ($prestamos as $prestamo) {
            $correo = $prestamo['correo'];
            if (!empty($correo)) {
                if (!isset($prestamosPorUsuario[$correo])) {
                    $prestamosPorUsuario[$correo] = [
                        'nombre' => $prestamo['nombre_completo'],
                        'prestamos' => []
                    ];
                }
                $prestamosPorUsuario[$correo]['prestamos'][] = $prestamo;
            }
        }

        // Enviar correos
        foreach ($prestamosPorUsuario as $correo => $data) {
            $email->clear();
            $email->setTo($correo);
            $email->setSubject('⏰ Recordatorio: Préstamos Próximos a Vencer');

            $mensaje = view('emails/prestamos_vencer', [
                'nombre' => $data['nombre'],
                'prestamos' => $data['prestamos']
            ]);

            $email->setMessage($mensaje);

            if ($email->send()) {
                $enviados++;
                log_message('info', "Email de préstamos enviado a: $correo");
            } else {
                log_message('error', "Error al enviar email a: $correo");
            }
        }

        return $enviados;
    }

    /**
     * Ejecutar todas las notificaciones
     */
    public function ejecutarTodas()
    {
        $resultado = [
            'licencias' => $this->enviarLicenciasVencer(),
            'prestamos' => $this->enviarPrestamosVencer()
        ];

        log_message('info', 'Notificaciones ejecutadas: ' . json_encode($resultado));

        return $this->response->setJSON([
            'success' => true,
            'resultado' => $resultado
        ]);
    }

    /**
     * Obtener correos de administradores
     */
    private function obtenerCorreosAdministradores()
    {
        $usuariosModel = new \App\Models\UsuariosModel();
        
        $admins = $usuariosModel
            ->select('correo')
            ->where('rol', 'administrador')
            ->where('estado', 'activo')
            ->whereNotIn('correo', ['', null])
            ->findAll();

        return array_column($admins, 'correo');
    }

    /**
     * Probar configuración de correo
     */
    public function probarEmail()
    {
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (str_contains($permiso, 'notificaciones') || str_contains($permiso, 'administrador') || str_contains($permiso, 'auditoria')) {
                $tienePermiso = true;
                break;
            }
        }
        
        if (!$tienePermiso) {
            return redirect()->to(base_url('home'))->with('error', 'No tienes permisos para esta acción');
        }

        $correo = session()->get('correo');

        if (empty($correo)) {
            return redirect()->back()->with('error', 'Tu cuenta no tiene correo configurado');
        }

        $email = \Config\Services::email();
        $email->setTo($correo);
        $email->setSubject('✅ Prueba de Configuración de Email - Sistema Inventario');
        
        $mensaje = view('emails/prueba', [
            'nombre' => session()->get('nombre'),
            'fecha' => date('d/m/Y H:i:s')
        ]);

        $email->setMessage($mensaje);

        if ($email->send()) {
            return redirect()->back()->with('success', 'Email de prueba enviado correctamente a: ' . $correo);
        } else {
            log_message('error', 'Error al enviar email de prueba: ' . $email->printDebugger(['headers']));
            return redirect()->back()->with('error', 'Error al enviar email. Verifica la configuración en app/Config/Email.php');
        }
    }
}
