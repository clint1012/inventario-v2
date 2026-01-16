-- ============================================
-- SISTEMA DE GESTIÓN DE SESIONES ACTIVAS
-- ============================================

-- Tabla para sesiones activas
CREATE TABLE IF NOT EXISTS `sesiones_activas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `navegador` varchar(100) DEFAULT NULL,
  `sistema_operativo` varchar(100) DEFAULT NULL,
  `ultima_actividad` datetime NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `activa` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `activa` (`activa`),
  KEY `ultima_actividad` (`ultima_actividad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para historial de logins
CREATE TABLE IF NOT EXISTS `historial_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `accion` enum('LOGIN','LOGOUT','SESION_CERRADA') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `navegador` varchar(100) DEFAULT NULL,
  `sistema_operativo` varchar(100) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `duracion_segundos` int(11) DEFAULT NULL COMMENT 'Duración de la sesión en segundos (solo para LOGOUT)',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `accion` (`accion`),
  KEY `fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos para gestión de sesiones (agregar a tabla permisos)
INSERT INTO `permisos` (`clave`, `descripcion`, `creado_en`) VALUES
('sesiones.ver', 'Ver sesiones activas del sistema', NOW()),
('sesiones.cerrar', 'Cerrar sesiones de otros usuarios', NOW()),
('sesiones.historial', 'Ver historial de inicios de sesión', NOW());

-- ============================================
-- NOTA: Después de ejecutar los INSERT anteriores,
-- ejecuta esta consulta para obtener los IDs:
-- ============================================
-- SELECT id, clave FROM permisos WHERE clave LIKE 'sesiones%';

-- ============================================
-- Asignar permisos al rol administrador (rol_id = 1)
-- IMPORTANTE: Reemplaza los IDs (24, 25, 26) con los IDs reales
-- obtenidos de la consulta anterior
-- ============================================
INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`) 
SELECT 1, id FROM permisos WHERE clave IN ('sesiones.ver', 'sesiones.cerrar', 'sesiones.historial');

-- ============================================
-- CONSULTAS ÚTILES
-- ============================================

-- Ver sesiones activas actualmente
-- SELECT sa.*, u.correo 
-- FROM sesiones_activas sa
-- JOIN usuarios u ON sa.usuario_id = u.id
-- WHERE sa.activa = 1
-- ORDER BY sa.ultima_actividad DESC;

-- Ver historial de logins del día
-- SELECT * FROM historial_logins
-- WHERE DATE(fecha) = CURDATE()
-- ORDER BY fecha DESC;

-- Ver usuarios con múltiples sesiones activas
-- SELECT usuario_id, usuario, nombre, COUNT(*) as sesiones_activas
-- FROM sesiones_activas
-- WHERE activa = 1
-- GROUP BY usuario_id, usuario, nombre
-- HAVING COUNT(*) > 1;

-- Limpiar sesiones inactivas (más de 2 horas sin actividad)
-- UPDATE sesiones_activas 
-- SET activa = 0 
-- WHERE activa = 1 
-- AND ultima_actividad < DATE_SUB(NOW(), INTERVAL 2 HOUR);
