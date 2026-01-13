<?php

namespace App\Config;

/**
 * Constantes de la aplicación
 * Define valores constantes utilizados en toda la aplicación
 */
class AppConstants
{
    // Estados de bienes
    public const ESTADO_ACTIVO = 'activo';
    public const ESTADO_ASIGNADO = 'asignado';
    public const ESTADO_MANTENIMIENTO = 'mantenimiento';
    public const ESTADO_BAJA = 'baja';
    public const ESTADO_PRESTAMO = 'prestamo';
    public const ESTADO_DISPONIBLE = 'disponible';
    public const ESTADO_RETIRADO = 'retirado';

    // Tipos de movimiento
    public const MOVIMIENTO_ASIGNACION = 'asignacion';
    public const MOVIMIENTO_PRESTAMO = 'prestamo';
    public const MOVIMIENTO_RETIRO = 'retiro';
    public const MOVIMIENTO_DEVOLUCION = 'devolucion';

    // Estados de usuario
    public const USUARIO_ACTIVO = 'activo';
    public const USUARIO_INACTIVO = 'inactivo';

    // Extensiones permitidas para archivos
    public const EXTENSIONES_EXCEL = ['csv', 'xls', 'xlsx'];
    public const EXTENSIONES_IMAGENES = ['jpg', 'jpeg', 'png', 'gif'];

    // Rutas de archivos
    public const RUTA_BAJAS = 'uploads/bajas/';
    public const RUTA_UPLOADS = 'uploads/';

    // Límites
    public const MAX_RESULTADOS_TOP_USUARIOS = 5;
    public const MAX_ULTIMOS_MOVIMIENTOS = 5;
    public const MESES_ESTADISTICAS = 6;

    /**
     * Obtiene todos los estados de bienes
     * @return array
     */
    public static function getEstadosBienes(): array
    {
        return [
            self::ESTADO_ACTIVO,
            self::ESTADO_ASIGNADO,
            self::ESTADO_MANTENIMIENTO,
            self::ESTADO_BAJA,
            self::ESTADO_PRESTAMO,
            self::ESTADO_DISPONIBLE,
            self::ESTADO_RETIRADO
        ];
    }

    /**
     * Obtiene etiquetas de estados
     * @return array
     */
    public static function getEstadosLabels(): array
    {
        return [
            self::ESTADO_ACTIVO => 'Activos',
            self::ESTADO_ASIGNADO => 'Asignados',
            self::ESTADO_MANTENIMIENTO => 'Mantenimiento',
            self::ESTADO_BAJA => 'Baja',
            self::ESTADO_PRESTAMO => 'Préstamo',
            self::ESTADO_DISPONIBLE => 'Disponible',
            self::ESTADO_RETIRADO => 'Retirado'
        ];
    }
}
