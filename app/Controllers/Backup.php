<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditoriaModel;

class Backup extends BaseController
{
    protected $dbBackupPath;

    public function __construct()
    {
        $this->dbBackupPath = WRITEPATH . 'backups/';
        
        // Crear directorio si no existe
        if (!is_dir($this->dbBackupPath)) {
            mkdir($this->dbBackupPath, 0755, true);
        }
    }

    /**
     * Vista principal de backups
     */
    public function index()
    {
        // Verificar permisos de administrador o backup
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (str_contains($permiso, 'backup') || str_contains($permiso, 'administrador') || str_contains($permiso, 'auditoria')) {
                $tienePermiso = true;
                break;
            }
        }
        
        if (!$tienePermiso) {
            return redirect()->to(base_url('home'))->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $backups = $this->listarBackups();
        
        return view('backup/index', [
            'backups' => $backups,
            'backup_path' => $this->dbBackupPath
        ]);
    }

    /**
     * Crear backup de la base de datos
     */
    public function crear()
    {
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (str_contains($permiso, 'backup') || str_contains($permiso, 'administrador') || str_contains($permiso, 'auditoria')) {
                $tienePermiso = true;
                break;
            }
        }
        
        if (!$tienePermiso) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sin permisos']);
        }

        try {
            $db = \Config\Database::connect();
            $dbName = $db->database;
            
            // Nombre del archivo
            $fileName = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $this->dbBackupPath . $fileName;

            // Obtener configuración de la BD
            $config = config('Database');
            $hostname = $config->default['hostname'];
            $username = $config->default['username'];
            $password = $config->default['password'];

            // Comando mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($hostname),
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );

            // Ejecutar comando
            exec($command, $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($filePath) || filesize($filePath) === 0) {
                // Intentar método alternativo con CodeIgniter
                $this->crearBackupAlternativo($filePath);
            }

            if (file_exists($filePath) && filesize($filePath) > 0) {
                // Registrar en auditoría
                AuditoriaModel::registrar('CREAR_BACKUP', 'Sistema', null, [
                    'archivo' => $fileName,
                    'tamaño' => $this->formatBytes(filesize($filePath))
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Backup creado exitosamente',
                    'archivo' => $fileName,
                    'tamaño' => $this->formatBytes(filesize($filePath))
                ]);
            } else {
                throw new \Exception('Error al crear el backup');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al crear backup: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al crear backup: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Método alternativo usando el DBUtil de CodeIgniter
     */
    private function crearBackupAlternativo($filePath)
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        
        // Obtener todas las tablas
        $tables = $db->listTables();
        
        $backup = "-- Backup de Base de Datos\n";
        $backup .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
        $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Estructura de la tabla
            $createTable = $db->query("SHOW CREATE TABLE `$table`")->getRowArray();
            $backup .= "DROP TABLE IF EXISTS `$table`;\n";
            $backup .= $createTable['Create Table'] . ";\n\n";

            // Datos de la tabla
            $query = $db->table($table)->get();
            if ($query->getNumRows() > 0) {
                $backup .= "INSERT INTO `$table` VALUES \n";
                $rows = [];
                foreach ($query->getResultArray() as $row) {
                    $values = array_map(function($value) use ($db) {
                        return $value === null ? 'NULL' : $db->escape($value);
                    }, $row);
                    $rows[] = '(' . implode(',', $values) . ')';
                }
                $backup .= implode(",\n", $rows) . ";\n\n";
            }
        }

        $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($filePath, $backup);
    }

    /**
     * Descargar un backup
     */
    public function descargar($archivo)
    {
        if (session()->get('rol') !== 'administrador') {
            return redirect()->to(base_url())->with('error', 'Sin permisos');
        }

        $filePath = $this->dbBackupPath . $archivo;

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Archivo no encontrado');
        }

        // Registrar descarga
        AuditoriaModel::registrar('DESCARGAR_BACKUP', 'Sistema', null, [
            'archivo' => $archivo
        ]);

        return $this->response->download($filePath, null);
    }

    /**
     * Eliminar un backup
     */
    public function eliminar()
    {
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (str_contains($permiso, 'backup') || str_contains($permiso, 'administrador') || str_contains($permiso, 'auditoria')) {
                $tienePermiso = true;
                break;
            }
        }
        
        if (!$tienePermiso) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sin permisos']);
        }

        $archivo = $this->request->getPost('archivo');
        $filePath = $this->dbBackupPath . $archivo;

        if (!file_exists($filePath)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Archivo no encontrado']);
        }

        if (unlink($filePath)) {
            // Registrar eliminación
            AuditoriaModel::registrar('ELIMINAR_BACKUP', 'Sistema', null, [
                'archivo' => $archivo
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Backup eliminado correctamente'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar el archivo'
            ]);
        }
    }

    /**
     * Restaurar un backup
     */
    public function restaurar()
    {
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (str_contains($permiso, 'backup') || str_contains($permiso, 'administrador') || str_contains($permiso, 'auditoria')) {
                $tienePermiso = true;
                break;
            }
        }
        
        if (!$tienePermiso) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sin permisos']);
        }

        $archivo = $this->request->getPost('archivo');
        $filePath = $this->dbBackupPath . $archivo;

        if (!file_exists($filePath)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Archivo no encontrado']);
        }

        try {
            $config = config('Database');
            $hostname = $config->default['hostname'];
            $username = $config->default['username'];
            $password = $config->default['password'];
            $dbName = $config->default['database'];

            // Comando mysql para restaurar
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($hostname),
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                // Registrar restauración
                AuditoriaModel::registrar('RESTAURAR_BACKUP', 'Sistema', null, [
                    'archivo' => $archivo
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Base de datos restaurada correctamente. Se recomienda cerrar sesión y volver a ingresar.'
                ]);
            } else {
                throw new \Exception('Error al ejecutar comando de restauración: ' . implode("\n", $output));
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al restaurar backup: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al restaurar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Listar todos los backups disponibles
     */
    private function listarBackups()
    {
        $backups = [];
        
        if (is_dir($this->dbBackupPath)) {
            $files = scandir($this->dbBackupPath);
            
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $filePath = $this->dbBackupPath . $file;
                    $backups[] = [
                        'nombre' => $file,
                        'tamaño' => $this->formatBytes(filesize($filePath)),
                        'fecha' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'ruta' => $filePath
                    ];
                }
            }
            
            // Ordenar por fecha descendente
            usort($backups, function($a, $b) {
                return strtotime($b['fecha']) - strtotime($a['fecha']);
            });
        }
        
        return $backups;
    }

    /**
     * Formatear bytes a formato legible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Limpiar backups antiguos (más de 30 días)
     */
    public function limpiarAntiguos()
    {
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (str_contains($permiso, 'backup') || str_contains($permiso, 'administrador') || str_contains($permiso, 'auditoria')) {
                $tienePermiso = true;
                break;
            }
        }
        
        if (!$tienePermiso) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sin permisos']);
        }

        $eliminados = 0;
        $dias = 30;
        $fechaLimite = strtotime("-$dias days");

        if (is_dir($this->dbBackupPath)) {
            $files = scandir($this->dbBackupPath);
            
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $filePath = $this->dbBackupPath . $file;
                    
                    if (filemtime($filePath) < $fechaLimite) {
                        if (unlink($filePath)) {
                            $eliminados++;
                        }
                    }
                }
            }
        }

        // Registrar limpieza
        AuditoriaModel::registrar('LIMPIAR_BACKUPS', 'Sistema', null, [
            'archivos_eliminados' => $eliminados,
            'dias' => $dias
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => "Se eliminaron $eliminados backup(s) de más de $dias días"
        ]);
    }
}
