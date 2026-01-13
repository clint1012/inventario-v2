<?php

use App\Models\AuditoriaModel;

/**
 * Registrar una acción en auditoría
 * 
 * @param string $accion Tipo de acción (crear, editar, eliminar, importar, etc.)
 * @param string $modulo Módulo del sistema (usuarios, bienes, personas, etc.)
 * @param int|null $registroId ID del registro afectado
 * @param string|array|null $detalles Detalles adicionales de la acción
 */
if (!function_exists('registrar_auditoria')) {
    function registrar_auditoria(string $accion, string $modulo, ?int $registroId = null, $detalles = null): void
    {
        try {
            $auditoriaModel = new AuditoriaModel();
            $request = \Config\Services::request();
            
            // Obtener datos del usuario de la sesión
            $session = session();
            $usuarioId = $session->get('usuario_id');
            $usuarioNombre = $session->get('nombre') ?? $session->get('usuario') ?? 'Sistema';
            
            // Preparar detalles
            $detallesJson = null;
            if ($detalles !== null) {
                if (is_array($detalles)) {
                    $detallesJson = json_encode($detalles, JSON_UNESCAPED_UNICODE);
                } else {
                    $detallesJson = (string)$detalles;
                }
            }
            
            // Insertar registro de auditoría
            $auditoriaModel->insert([
                'usuario_id' => $usuarioId,
                'usuario_nombre' => $usuarioNombre,
                'accion' => $accion,
                'modulo' => $modulo,
                'registro_id' => $registroId,
                'detalles' => $detallesJson,
                'ip_address' => $request->getIPAddress(),
                'user_agent' => $request->getUserAgent()->getAgentString()
            ]);
        } catch (\Exception $e) {
            // No detener el flujo si falla la auditoría
            log_message('error', 'Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}
