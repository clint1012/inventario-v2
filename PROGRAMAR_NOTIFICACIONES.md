# Guía para Programar Notificaciones Automáticas

## 📅 Programar Tarea en Windows (Task Scheduler)

### Opción 1: Ejecutar Todas las Notificaciones Diariamente

1. **Abrir Programador de Tareas**
   - Presiona `Windows + R`
   - Escribe `taskschd.msc` y presiona Enter

2. **Crear Nueva Tarea Básica**
   - Clic derecho en "Biblioteca del Programador de tareas"
   - Selecciona "Crear tarea básica..."

3. **Configurar la Tarea**
   
   **Paso 1 - Nombre:**
   - Nombre: `Notificaciones Inventario`
   - Descripción: `Enviar notificaciones de licencias y préstamos por vencer`
   
   **Paso 2 - Desencadenador:**
   - Seleccionar: `Diariamente`
   - Hora: `08:00:00` (8:00 AM)
   - Repetir cada: `1 día`
   
   **Paso 3 - Acción:**
   - Seleccionar: `Iniciar un programa`
   - Programa: `C:\xampp\php\php.exe`
   - Argumentos: `index.php notificaciones ejecutar`
   - Iniciar en: `C:\xampp\htdocs\inventariov2\public`

4. **Configuración Avanzada (Opcional)**
   - Marcar: "Ejecutar con los privilegios más altos"
   - Marcar: "Ejecutar tanto si el usuario inició sesión como si no"

---

### Opción 2: Notificaciones de Licencias (Semanal - Lunes)

**Desencadenador:**
- Seleccionar: `Semanalmente`
- Día: `Lunes`
- Hora: `09:00:00`

**Acción:**
- Programa: `C:\xampp\php\php.exe`
- Argumentos: `index.php notificaciones licencias`
- Iniciar en: `C:\xampp\htdocs\inventariov2\public`

---

### Opción 3: Recordatorios de Préstamos (Diario)

**Desencadenador:**
- Seleccionar: `Diariamente`
- Hora: `10:00:00`

**Acción:**
- Programa: `C:\xampp\php\php.exe`
- Argumentos: `index.php notificaciones prestamos`
- Iniciar en: `C:\xampp\htdocs\inventariov2\public`

---

## 🧪 Probar Manualmente las Notificaciones

### Desde PowerShell/CMD:

```powershell
# Ir al directorio del proyecto
cd C:\xampp\htdocs\inventariov2\public

# Ejecutar todas las notificaciones
php index.php notificaciones ejecutar

# Solo notificaciones de licencias
php index.php notificaciones licencias

# Solo recordatorios de préstamos
php index.php notificaciones prestamos
```

### Desde el Navegador (Solo Administradores):

**Enviar todas las notificaciones:**
```
POST http://localhost/inventariov2/public/notificaciones/ejecutar
```

**Probar configuración de email:**
```
GET http://localhost/inventariov2/public/notificaciones/probar-email
```

---

## 📋 Crear Archivo BAT para Ejecución Manual

Crea un archivo `enviar_notificaciones.bat` en `C:\xampp\htdocs\inventariov2\`:

```batch
@echo off
echo ========================================
echo  SISTEMA DE NOTIFICACIONES
echo  Inventario v2.0
echo ========================================
echo.

cd C:\xampp\htdocs\inventariov2\public

echo [1/3] Enviando notificaciones de licencias...
php index.php notificaciones licencias
echo.

echo [2/3] Enviando recordatorios de prestamos...
php index.php notificaciones prestamos
echo.

echo [3/3] Proceso completado
echo.

pause
```

Luego puedes:
1. Hacer doble clic en el archivo para ejecutar manualmente
2. Crear un acceso directo en el escritorio
3. Programar este BAT en el Programador de Tareas

---

## 🔍 Verificar que las Tareas Funcionan

### 1. Ver Logs del Sistema
Los logs se guardan en: `writable/logs/log-{fecha}.php`

Buscar líneas como:
```
INFO --> Notificaciones de licencias ejecutadas: X emails enviados
INFO --> Notificaciones de préstamos ejecutadas: X emails enviados
```

### 2. Revisar en phpMyAdmin
```sql
-- Ver auditoría de envíos de notificaciones
SELECT * FROM auditoria 
WHERE modulo = 'Sistema' 
AND accion LIKE '%notificacion%'
ORDER BY created_at DESC 
LIMIT 20;
```

### 3. Verificar Última Ejecución en Task Scheduler
- Abrir Programador de Tareas
- Buscar la tarea creada
- Ver columna "Última ejecución" y "Resultado de última ejecución"
- Si dice "0x0" = Exitoso
- Si dice otro código = Error

---

## ⚠️ Solución de Problemas

### Problema: "PHP no se reconoce como comando"
**Solución:** Usar ruta completa: `C:\xampp\php\php.exe`

### Problema: "No se puede encontrar index.php"
**Solución:** Verificar que "Iniciar en" apunta a la carpeta `public/`

### Problema: "Access denied for database"
**Solución:** 
1. Verificar credenciales en `app/Config/Database.php`
2. Asegurarse que MySQL/MariaDB está corriendo

### Problema: Emails no se envían
**Solución:**
1. Verificar configuración SMTP en `app/Config/Email.php`
2. Ejecutar: `http://localhost/inventariov2/public/notificaciones/probar-email`
3. Revisar logs en `writable/logs/`

### Problema: Tarea programada no se ejecuta
**Solución:**
1. Verificar que XAMPP (Apache + MySQL) está corriendo como servicio
2. Ejecutar Task Scheduler como Administrador
3. Marcar "Ejecutar con los privilegios más altos"
4. Probar ejecutar manualmente desde Task Scheduler (botón "Ejecutar")

---

## 📊 Frecuencias Recomendadas

| Notificación | Frecuencia | Hora Sugerida | Razón |
|--------------|------------|---------------|-------|
| Todas (ejecutar) | Diaria | 08:00 AM | Primera hora laboral |
| Licencias | Lunes | 09:00 AM | Revisión semanal |
| Préstamos | Diaria | 10:00 AM | Recordatorio constante |

---

## 📝 Notas Importantes

- **SMTP debe estar configurado** antes de programar las tareas automáticas
- Las notificaciones **solo se envían** si hay licencias o préstamos por vencer
- Los emails **no se duplican** si se ejecuta manualmente y automáticamente el mismo día
- Revisar los **logs regularmente** para detectar errores
- Hacer **backup antes** de probar la restauración de base de datos

---

## 🔗 Referencias

- Configuración SMTP: Ver `CONFIGURACION_EMAIL.md`
- Sistema de Backup: Acceder a `http://localhost/inventariov2/public/backup`
- Auditoría: Acceder a `http://localhost/inventariov2/public/auditoria`
