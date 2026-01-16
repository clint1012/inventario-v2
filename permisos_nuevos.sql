-- ============================================
-- Script para agregar permisos de Backup y Notificaciones
-- Ejecutar en phpMyAdmin > SQL
-- ============================================

-- PASO 1: Ver roles existentes para saber cuál usar
SELECT id, nombre FROM roles;

-- PASO 2: Insertar permisos de Backup
INSERT INTO permisos (clave, descripcion, creado_en) VALUES
('backup.ver', 'Ver backups', NOW()),
('backup.crear', 'Crear backups', NOW()),
('backup.restaurar', 'Restaurar backups', NOW()),
('backup.eliminar', 'Eliminar backups', NOW());

-- PASO 3: Insertar permisos de Notificaciones
INSERT INTO permisos (clave, descripcion, creado_en) VALUES
('notificaciones.enviar', 'Enviar notificaciones', NOW()),
('notificaciones.probar', 'Probar email', NOW());

-- PASO 4: Ver los IDs de los nuevos permisos
SELECT id, clave, descripcion FROM permisos WHERE clave LIKE 'backup%' OR clave LIKE 'notificaciones%';

-- PASO 5: Asignar permisos al rol administrador
-- IMPORTANTE: Reemplaza el número 1 con el ID de tu rol administrador del PASO 1
-- Reemplaza 11, 12, 13, 14, 15, 16 con los IDs del PASO 4
INSERT INTO roles_permisos (rol_id, permiso_id) VALUES
(1, 11),  -- backup.ver
(1, 12),  -- backup.crear
(1, 13),  -- backup.restaurar
(1, 14),  -- backup.eliminar
(1, 15),  -- notificaciones.enviar
(1, 16);  -- notificaciones.probar

-- PASO 6: Verificar que se asignaron correctamente
SELECT r.nombre as rol, p.clave as permiso, p.descripcion
FROM roles r
INNER JOIN roles_permisos rp ON r.id = rp.rol_id
INNER JOIN permisos p ON rp.permiso_id = p.id
WHERE p.clave LIKE 'backup%' OR p.clave LIKE 'notificaciones%';
