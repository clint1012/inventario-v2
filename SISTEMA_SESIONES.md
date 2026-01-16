# 🔐 SISTEMA DE GESTIÓN DE SESIONES ACTIVAS

## 📋 DESCRIPCIÓN

Sistema completo para monitorear, gestionar y auditar las sesiones de usuarios en tiempo real.

## ✨ CARACTERÍSTICAS IMPLEMENTADAS

### 1️⃣ **Monitoreo de Sesiones Activas**
- ✅ Vista en tiempo real de usuarios conectados
- ✅ Información detallada: IP, navegador, sistema operativo
- ✅ Tiempo de inactividad y duración de sesión
- ✅ Auto-refresh cada 30 segundos
- ✅ Detección de sesiones múltiples

### 2️⃣ **Gestión Remota de Sesiones**
- ✅ Cerrar sesiones individuales
- ✅ Cerrar todas las sesiones de un usuario
- ✅ Limpiar sesiones expiradas automáticamente
- ✅ Protección: no se puede cerrar la propia sesión

### 3️⃣ **Historial Completo de Logins**
- ✅ Registro de LOGIN, LOGOUT y SESION_CERRADA
- ✅ Filtros por fecha y tipo de acción
- ✅ Duración de cada sesión
- ✅ Exportación a Excel (próximamente)

### 4️⃣ **Dashboard de Estadísticas**
- ✅ Sesiones activas actuales
- ✅ Usuarios conectados únicos
- ✅ Logins del día
- ✅ Usuarios con múltiples sesiones

### 5️⃣ **Seguridad y Auditoría**
- ✅ Registro detallado en auditoría
- ✅ Control de permisos por rol
- ✅ Detección de navegador y SO
- ✅ Tracking de IP y User Agent

---

## 📦 ARCHIVOS CREADOS/MODIFICADOS

### **Base de Datos**
```
sesiones_activas.sql
├── Tabla: sesiones_activas
├── Tabla: historial_logins
├── 3 Permisos nuevos (IDs 24-26)
└── Asignación a rol administrador
```

### **Modelos**
```
app/Models/
├── SesionesActivasModel.php    (Gestión de sesiones activas)
└── HistorialLoginsModel.php    (Historial de accesos)
```

### **Controladores**
```
app/Controllers/
├── Sesiones.php                (Controlador principal - 7 métodos)
└── Login.php (MODIFICADO)      (Registro automático de sesiones)
```

### **Vistas**
```
app/Views/sesiones/
├── index.php                   (Gestión de sesiones activas)
└── historial.php              (Historial de logins)
```

### **Configuración**
```
app/Config/
├── Routes.php (MODIFICADO)     (7 rutas nuevas)
└── app/Views/plantilla.php     (Menú "Sesiones Activas")
```

---

## 🚀 INSTALACIÓN

### **PASO 1: Crear tablas en la base de datos**
```sql
-- Ejecutar en phpMyAdmin:
1. Abrir phpMyAdmin
2. Seleccionar base de datos "inventariov2"
3. Click en pestaña "SQL"
4. Copiar y pegar contenido de: sesiones_activas.sql
5. Click en "Continuar"
```

### **PASO 2: Verificar permisos**
```sql
-- Verificar que se crearon los 3 permisos:
SELECT * FROM permisos WHERE id IN (24, 25, 26);

-- Verificar asignación al rol administrador:
SELECT * FROM roles_permisos WHERE permiso_id IN (24, 25, 26);
```

### **PASO 3: Limpiar caché**
```bash
# En terminal PowerShell:
cd c:\xampp\htdocs\inventariov2
php spark cache:clear
```

### **PASO 4: Probar el sistema**
```
1. Acceder al sistema: http://localhost/inventariov2
2. Login como administrador
3. Click en menú lateral "Sesiones Activas"
4. Verificar que aparece tu sesión actual
```

---

## 📊 USO DEL SISTEMA

### **1. Vista Principal - Sesiones Activas**

**Ubicación:** Menú lateral → Sesiones Activas

**Características:**
- 📊 4 tarjetas de estadísticas en la parte superior
- 📋 Tabla con todas las sesiones activas
- 🔄 Botón "Refrescar" para actualizar manualmente
- 🧹 Botón "Limpiar Expiradas" (cierra sesiones >2 horas inactivas)
- 🕐 Auto-refresh automático cada 30 segundos

**Columnas de la tabla:**
- **Usuario:** Nombre de usuario
- **Nombre:** Nombre completo
- **IP:** Dirección IP
- **Navegador:** Chrome, Firefox, Edge, etc.
- **SO:** Windows, macOS, Linux, etc.
- **Inicio:** Fecha y hora de login
- **Última Actividad:** Última acción + tiempo de inactividad
- **Duración:** Tiempo total conectado
- **Acciones:** Botón "Cerrar" (no disponible para tu propia sesión)

### **2. Cerrar Sesiones Remotamente**

**Casos de uso:**
- Usuario olvidó cerrar sesión en otra computadora
- Detectar sesiones sospechosas
- Forzar cierre por mantenimiento
- Resolver conflictos de sesiones múltiples

**Cómo cerrar una sesión:**
```
1. Localizar la sesión en la tabla
2. Click en botón rojo "Cerrar"
3. Confirmar en el diálogo
4. La sesión se cierra inmediatamente
5. Se registra en historial y auditoría
```

**Nota:** No puedes cerrar tu propia sesión desde aquí (usa Logout normal).

### **3. Historial de Logins**

**Ubicación:** Sesiones Activas → Ver Historial

**Filtros disponibles:**
- **Acción:** LOGIN, LOGOUT, SESION_CERRADA
- **Fecha Desde:** Fecha inicio del periodo
- **Fecha Hasta:** Fecha fin del periodo

**Información mostrada:**
- Fecha y hora exacta
- Tipo de acción con badge de color
- Usuario y nombre completo
- IP y detalles técnicos
- Duración de la sesión (para LOGOUT)

**Por defecto:** Muestra logins del día actual

### **4. Estadísticas**

**Sesiones Activas:** Total de sesiones conectadas ahora
**Usuarios Conectados:** Usuarios únicos (un usuario puede tener múltiples sesiones)
**Logins Hoy:** Total de inicios de sesión en el día
**Múltiples Sesiones:** Usuarios con más de 1 sesión activa

---

## 🔒 PERMISOS

### **Permisos Creados:**

| ID | Nombre | Clave | Descripción |
|----|--------|-------|-------------|
| 24 | Ver sesiones activas | `sesiones.ver` | Permite ver la lista de sesiones |
| 25 | Cerrar sesiones remotas | `sesiones.cerrar` | Permite cerrar sesiones de otros usuarios |
| 26 | Ver historial de logins | `sesiones.historial` | Permite ver el historial de accesos |

### **Asignación de Permisos:**

Por defecto están asignados al **rol administrador (ID 1)**.

**Para asignar a otros roles:**
```sql
-- Ejemplo: Asignar permiso de ver sesiones al rol "supervisor" (ID 2)
INSERT INTO roles_permisos (rol_id, permiso_id) VALUES (2, 24);
```

---

## 🛠️ FUNCIONES TÉCNICAS

### **Registro Automático de Sesiones**

El sistema registra automáticamente cada sesión al hacer login:
```php
// En Login.php doLogin()
- Guarda session_id, usuario_id, IP, navegador, SO
- Registra en tabla sesiones_activas
- Registra en tabla historial_logins
- Registra en tabla auditoria
```

### **Actualización de Actividad**

La última actividad se actualiza automáticamente:
```php
// Método: registrarSesion()
- Si la sesión existe: actualiza ultima_actividad
- Si no existe: crea nueva sesión
```

### **Limpieza Automática**

Al abrir la vista de sesiones:
```php
// Método: limpiarSesionesExpiradas()
- Cierra sesiones con >2 horas sin actividad
- Marca activa = 0
- No elimina registros (para auditoría)
```

### **Detección de Navegador y SO**

Método estático para parsear User Agent:
```php
SesionesActivasModel::parseUserAgent($user_agent)
// Retorna: ['navegador' => 'Chrome', 'sistema_operativo' => 'Windows 10/11']
```

---

## 🧪 PRUEBAS RECOMENDADAS

### **Prueba 1: Sesión Normal**
```
1. Hacer login como administrador
2. Ir a Sesiones Activas
3. Verificar que aparece tu sesión
4. Verificar datos: IP, navegador, SO, duración
5. Confirmar que dice "Tu sesión" en acciones
```

### **Prueba 2: Múltiples Sesiones**
```
1. Abrir navegador en modo incógnito
2. Hacer login con otro usuario
3. En ventana normal, ir a Sesiones Activas
4. Verificar que aparecen ambas sesiones
5. Estadísticas debe mostrar 2 sesiones activas
```

### **Prueba 3: Cerrar Sesión Remota**
```
1. Con 2 sesiones activas (ventana normal + incógnito)
2. En ventana normal: cerrar sesión de incógnito
3. Verificar mensaje de éxito
4. En incógnito: intentar navegar (debe redirigir a login)
5. Verificar registro en historial
```

### **Prueba 4: Historial**
```
1. Hacer varios login/logout
2. Ir a Sesiones Activas → Ver Historial
3. Verificar que aparecen todos los eventos
4. Probar filtros por fecha
5. Verificar duración de sesiones cerradas
```

### **Prueba 5: Auto-refresh**
```
1. Abrir Sesiones Activas
2. Esperar 30 segundos
3. Verificar que la tabla se actualiza sola
4. Abrir navegador incógnito y hacer login
5. En ventana original: esperar 30s y verificar que aparece
```

### **Prueba 6: Limpiar Expiradas**
```
SQL: UPDATE sesiones_activas SET ultima_actividad = DATE_SUB(NOW(), INTERVAL 3 HOUR) WHERE id = X;
1. Ejecutar SQL para simular sesión expirada
2. Click en "Limpiar Expiradas"
3. Verificar que desaparece de la tabla
4. Verificar que activa = 0 en base de datos
```

---

## 📈 CONSULTAS SQL ÚTILES

### **Ver sesiones activas actualmente:**
```sql
SELECT 
    sa.*,
    u.correo,
    TIMESTAMPDIFF(MINUTE, sa.ultima_actividad, NOW()) as minutos_inactivo
FROM sesiones_activas sa
JOIN usuarios u ON sa.usuario_id = u.id
WHERE sa.activa = 1
ORDER BY sa.ultima_actividad DESC;
```

### **Ver historial de logins de hoy:**
```sql
SELECT * 
FROM historial_logins
WHERE DATE(fecha) = CURDATE()
ORDER BY fecha DESC;
```

### **Usuarios con múltiples sesiones:**
```sql
SELECT 
    usuario_id, 
    usuario, 
    nombre, 
    COUNT(*) as num_sesiones,
    GROUP_CONCAT(ip_address) as ips
FROM sesiones_activas
WHERE activa = 1
GROUP BY usuario_id, usuario, nombre
HAVING COUNT(*) > 1;
```

### **Estadísticas de logins por día (última semana):**
```sql
SELECT 
    DATE(fecha) as dia,
    COUNT(*) as total_logins,
    COUNT(DISTINCT usuario_id) as usuarios_unicos
FROM historial_logins
WHERE accion = 'LOGIN'
  AND fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY DATE(fecha)
ORDER BY dia DESC;
```

### **Sesiones más largas del mes:**
```sql
SELECT 
    usuario,
    nombre,
    duracion_segundos,
    CONCAT(FLOOR(duracion_segundos/3600), 'h ', 
           FLOOR((duracion_segundos%3600)/60), 'm') as duracion_formateada,
    fecha
FROM historial_logins
WHERE accion = 'LOGOUT'
  AND MONTH(fecha) = MONTH(CURDATE())
  AND duracion_segundos IS NOT NULL
ORDER BY duracion_segundos DESC
LIMIT 10;
```

### **Detectar intentos de login fallidos (desde auditoría):**
```sql
SELECT 
    COUNT(*) as intentos_fallidos,
    datos,
    MAX(fecha) as ultimo_intento
FROM auditoria
WHERE accion = 'LOGIN_FALLIDO'
  AND fecha >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY datos
HAVING COUNT(*) >= 3
ORDER BY intentos_fallidos DESC;
```

### **Cerrar manualmente todas las sesiones (emergencia):**
```sql
-- ⚠️ USAR SOLO EN EMERGENCIA
UPDATE sesiones_activas 
SET activa = 0 
WHERE activa = 1;
```

---

## 🔧 MANTENIMIENTO

### **Limpieza Periódica**

**Recomendación:** Programar limpieza semanal de historial antiguo

```sql
-- Eliminar historial de logins mayor a 6 meses
DELETE FROM historial_logins
WHERE fecha < DATE_SUB(CURDATE(), INTERVAL 6 MONTH);

-- Eliminar sesiones inactivas mayor a 1 mes
DELETE FROM sesiones_activas
WHERE activa = 0
  AND ultima_actividad < DATE_SUB(CURDATE(), INTERVAL 1 MONTH);
```

### **Monitoreo**

**Indicadores de problemas:**
- Muchas sesiones expiradas sin cerrar (revisar cron de limpieza)
- Usuarios con >5 sesiones activas (posible fuga de sesiones)
- Duraciones de sesión muy largas (revisar timeout de sesión)
- Muchos LOGIN sin LOGOUT correspondiente (sesiones zombies)

---

## ⚠️ SOLUCIÓN DE PROBLEMAS

### **Problema 1: No aparece menú "Sesiones Activas"**
```
✅ Solución:
1. Verificar permisos del usuario: SELECT * FROM roles_permisos WHERE rol_id = [tu_rol];
2. Verificar que existe permiso 24: SELECT * FROM permisos WHERE id = 24;
3. Limpiar caché: php spark cache:clear
4. Hacer logout y login nuevamente
```

### **Problema 2: Error "sesiones_activas table doesn't exist"**
```
✅ Solución:
1. Verificar que ejecutaste sesiones_activas.sql
2. En phpMyAdmin: SELECT * FROM sesiones_activas LIMIT 1;
3. Si no existe, ejecutar el SQL completo
```

### **Problema 3: No se registran sesiones al hacer login**
```
✅ Solución:
1. Verificar que Login.php tiene las importaciones:
   - use App\Models\SesionesActivasModel;
   - use App\Models\HistorialLoginsModel;
2. Verificar logs en: writable/logs/
3. Verificar que las tablas existen y tienen permisos de escritura
```

### **Problema 4: Cerrar sesión no funciona**
```
✅ Solución:
1. Verificar CSRF token en JavaScript
2. Verificar permisos del usuario (necesita sesiones.cerrar)
3. Abrir consola del navegador y buscar errores JavaScript
4. Verificar que la sesión a cerrar existe y está activa
```

### **Problema 5: Auto-refresh no funciona**
```
✅ Solución:
1. Abrir consola del navegador (F12)
2. Verificar errores JavaScript
3. Verificar que jQuery está cargado
4. Verificar URL base_url en AJAX
```

---

## 📚 PRÓXIMAS MEJORAS

- [ ] Exportar historial a Excel con formato
- [ ] Gráfica de logins por hora del día
- [ ] Alertas cuando un usuario tiene >3 sesiones
- [ ] Bloqueo automático por intentos fallidos
- [ ] Notificación por email al cerrar sesión remota
- [ ] Widget de sesiones activas en Dashboard
- [ ] API REST para gestión de sesiones
- [ ] Detectar ubicación geográfica por IP

---

## 📞 SOPORTE

**Problemas comunes:** Ver sección "Solución de Problemas"
**Consultas SQL:** Ver sección "Consultas SQL Útiles"
**Logs del sistema:** `writable/logs/log-[fecha].log`

---

**Desarrollado para:** Sistema de Inventario v2  
**Fecha:** Enero 2026  
**Versión:** 1.0.0
