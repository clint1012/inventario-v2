# 🧪 Guía de Prueba de Sistema de Backup y Restauración

## ⚠️ ADVERTENCIA IMPORTANTE
**NUNCA probar restauración en producción sin un backup reciente.**  
Esta guía es para ambiente de desarrollo/prueba únicamente.

---

## 📋 Preparación

### 1. Verificar Requisitos
```powershell
# Verificar que mysqldump existe
C:\xampp\mysql\bin\mysqldump.exe --version

# Verificar que mysql existe
C:\xampp\mysql\bin\mysql.exe --version
```

### 2. Crear Datos de Prueba
Antes de probar el backup, agrega algunos datos:
1. Crear 2-3 bienes nuevos
2. Agregar 1-2 personas
3. Registrar algún movimiento
4. **Anotar** los IDs o códigos de estos registros

---

## 🔄 Proceso de Prueba Completo

### PASO 1: Crear Backup Inicial

1. **Acceder al módulo de Backup:**
   ```
   http://localhost/inventariov2/public/backup
   ```

2. **Crear primer backup:**
   - Clic en botón "Crear Backup"
   - Esperar confirmación (aparecerá nombre del archivo y tamaño)
   - **Anotar el nombre del archivo** (ej: `backup_20260113_153045.sql`)

3. **Descargar el backup:**
   - Clic en botón "Descargar" del backup recién creado
   - Guardar en ubicación segura (ej: `C:\Backups\`)

### PASO 2: Realizar Cambios en la BD

Ahora vamos a modificar datos para verificar que la restauración funciona:

**Opción A - Desde el Sistema:**
1. Editar un bien existente (cambiar descripción)
2. Desactivar una persona
3. Crear un nuevo bien

**Opción B - Desde phpMyAdmin:**
```sql
-- Insertar un bien de prueba
INSERT INTO bienes (cod_patrimonial, descripcion, marca, modelo, estado) 
VALUES ('TEST001', 'BIEN DE PRUEBA PARA RESTAURACION', 'TEST', 'TEST', 'ACTIVO');

-- Actualizar un bien existente
UPDATE bienes SET descripcion = 'MODIFICADO PARA PRUEBA' WHERE id = 1;
```

**Anotar los cambios realizados** para verificar después.

### PASO 3: Verificar Estado "Modificado"

Antes de restaurar, verificar que los cambios están presentes:
- Ver el bien modificado
- Confirmar que el bien de prueba existe
- Revisar auditoría de cambios

### PASO 4: Restaurar Backup

⚠️ **ESTE PASO SOBRESCRIBE TODOS LOS DATOS**

1. En el módulo de Backup, localizar el backup del PASO 1
2. Clic en botón "Restaurar"
3. Leer la advertencia completa
4. Confirmar restauración
5. Sistema cerrará sesión automáticamente

### PASO 5: Verificar Restauración

1. **Volver a iniciar sesión**

2. **Verificar que los cambios del PASO 2 desaparecieron:**
   - El bien modificado debe tener su descripción original
   - El bien de prueba (TEST001) NO debe existir
   - La persona desactivada debe estar activa nuevamente

3. **Revisar auditoría:**
   ```
   http://localhost/inventariov2/public/auditoria
   ```
   - Debe haber un registro de "RESTAURAR BACKUP"
   - Los registros de cambios del PASO 2 deben seguir existiendo (porque están en la auditoría)

---

## 🎯 Checklist de Verificación

Marcar cada punto después de verificar:

**Antes de Restaurar:**
- [ ] Backup descargado y guardado externamente
- [ ] Cambios realizados y anotados
- [ ] Estado "modificado" verificado visualmente

**Después de Restaurar:**
- [ ] Sistema cierra sesión automáticamente
- [ ] Puedo volver a iniciar sesión correctamente
- [ ] Los cambios del PASO 2 ya no existen
- [ ] Los datos originales están restaurados
- [ ] Auditoría registra la restauración
- [ ] Sistema funciona normalmente (navegar por módulos)

---

## 📊 Pruebas Adicionales Recomendadas

### Prueba 2: Backup con Datos Grandes
1. Subir varios bienes (100+)
2. Crear backup
3. Verificar que el archivo es más grande
4. Descargar y verificar tamaño

### Prueba 3: Limpiar Backups Antiguos
1. Crear 3-4 backups en diferentes momentos
2. Usar función "Limpiar Antiguos"
3. Verificar que backups de más de 30 días se eliminan

### Prueba 4: Restaurar con Usuarios Activos
**Solo en desarrollo:**
1. Abrir 2 navegadores con sesiones activas
2. Restaurar backup desde navegador 1
3. Verificar que navegador 2 pierde sesión o muestra error

---

## 🐛 Problemas Comunes y Soluciones

### Problema 1: "Error al crear backup"
**Causas posibles:**
- mysqldump no está instalado
- Permisos insuficientes en carpeta `writable/backups/`
- MySQL no está corriendo

**Solución:**
```powershell
# Verificar MySQL corriendo
C:\xampp\xampp-control.exe

# Crear carpeta manualmente si no existe
New-Item -ItemType Directory -Path "C:\xampp\htdocs\inventariov2\writable\backups" -Force

# Dar permisos completos
icacls "C:\xampp\htdocs\inventariov2\writable\backups" /grant Everyone:F
```

### Problema 2: "Error al restaurar backup"
**Causas posibles:**
- Archivo backup corrupto
- Base de datos con tablas bloqueadas
- Usuario MySQL sin permisos DROP

**Solución:**
```sql
-- En phpMyAdmin, verificar permisos del usuario
SHOW GRANTS FOR 'root'@'localhost';

-- Debe tener: ALL PRIVILEGES
```

### Problema 3: Backup vacío (0 KB)
**Causa:** mysqldump no encuentra credenciales

**Solución:**
Verificar en `app/Config/Database.php`:
```php
public string $hostname = 'localhost';
public string $username = 'root';
public string $password = '';  // Tu contraseña
public string $database = 'inventariov2';
```

### Problema 4: Restauración exitosa pero datos incorrectos
**Causa:** Restauraste un backup antiguo

**Solución:**
1. Restaurar el backup más reciente
2. O crear nuevo backup ANTES de restaurar
3. Siempre verificar fecha del backup antes de restaurar

---

## 📝 Log de Pruebas (Plantilla)

```
FECHA DE PRUEBA: _______________
PROBADO POR: _______________

BACKUP INICIAL:
- Nombre archivo: _______________________
- Tamaño: _______ KB
- Hora creación: _______

CAMBIOS REALIZADOS:
1. _________________________________
2. _________________________________
3. _________________________________

RESTAURACIÓN:
- Backup restaurado: _______________________
- Hora restauración: _______
- Resultado: ☐ Exitoso  ☐ Fallido

VERIFICACIÓN:
☐ Datos restaurados correctamente
☐ Cambios eliminados como se esperaba
☐ Sistema funciona normalmente
☐ Auditoría registró la restauración
☐ No hay errores en logs

NOTAS ADICIONALES:
_________________________________
_________________________________
_________________________________
```

---

## 🎓 Recomendaciones Finales

1. **Probar restauración al menos 1 vez al mes** en desarrollo
2. **Nunca restaurar en producción** sin backup reciente
3. **Descargar backups críticos** a ubicación externa (nube, USB)
4. **Programar backups automáticos** diarios o semanales
5. **Revisar logs** después de cada operación de backup/restauración
6. **Documentar incidentes** para mejorar el proceso

---

## 🔗 Archivos Relacionados

- Sistema de Backup: `app/Controllers/Backup.php`
- Vista: `app/Views/backup/index.php`
- Configuración: `app/Config/Database.php`
- Logs: `writable/logs/log-{fecha}.php`
- Backups guardados: `writable/backups/`

---

## ✅ Lista de Verificación Pre-Producción

Antes de pasar a producción, verificar:

- [ ] Backup funciona correctamente en desarrollo
- [ ] Restauración probada y verificada
- [ ] Backups se descargan sin corromper
- [ ] Limpieza de backups antiguos funciona
- [ ] Permisos configurados correctamente
- [ ] Auditoría registra todas las operaciones
- [ ] Solo administradores tienen acceso
- [ ] Documentación completa y actualizada
- [ ] Plan de respaldo externo definido
- [ ] Procedimiento de emergencia documentado
