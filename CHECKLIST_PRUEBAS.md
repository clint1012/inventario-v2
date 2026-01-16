# ✅ Checklist de Verificación - Sistema Completo

## 📋 Funcionalidades Implementadas

### 1. Sistema de Auditoría ✅
**Ubicación:** `http://localhost/inventariov2/public/auditoria`

**Probar:**
- [ ] Ver listado completo de auditoría
- [ ] Filtrar por módulo y acción
- [ ] Exportar auditoría a Excel
- [ ] Ver detalles JSON de cada evento

**Módulos con auditoría activa:**
- ✅ Login (inicios de sesión)
- ✅ Bienes (crear, editar, cambiar estado)
- ✅ Personas (crear, editar, activar/desactivar)
- ✅ Usuarios (crear, editar, eliminar)
- ✅ Movimientos (asignaciones, préstamos, devoluciones)
- ✅ Proveedores (crear, editar, eliminar)
- ✅ Licencias (crear, editar, eliminar)
- ✅ Inventario (registrar, liberar, asignar)
- ✅ Celulares (crear, editar, baja, movimientos)
- ✅ Baja (recuperar bien)
- ✅ Mantenimiento (recuperar bien)
- ✅ IPs (actualizar asignación)
- ✅ Roles (crear, editar, eliminar, asignar permisos)
- ✅ Permisos (crear, editar, eliminar)

---

### 2. Historial de Cambios Visual ✅
**Acceso:** Botón "Ver Historial" en vistas de detalle/edición

**Probar:**
- [ ] **Bienes** → Ver detalle de un bien → Clic en "Ver Historial"
- [ ] **Personas** → Ver detalle de persona → Clic en "Ver Historial"  
- [ ] **Licencias** → En listado, clic en icono de historial (⏱️)
- [ ] **Proveedores** → En listado, clic en icono de historial (⏱️)
- [ ] **Celulares** → Editar celular → Clic en "Ver Historial"
- [ ] **Usuarios** → Editar usuario → Clic en "Ver Historial"

**Verificar en vista de historial:**
- [ ] Timeline muestra eventos ordenados cronológicamente
- [ ] Cada evento tiene: fecha, usuario, IP, acción
- [ ] Colores distintos según tipo de acción (crear=verde, editar=amarillo, eliminar=rojo)
- [ ] Se puede expandir "Ver Detalles" para ver JSON
- [ ] Muestra datos actuales del registro arriba

---

### 3. Sistema de Backup ✅
**Ubicación:** `http://localhost/inventariov2/public/backup`

**Probar creación:**
- [ ] Clic en "Crear Backup"
- [ ] Confirmar creación
- [ ] Verificar que aparece en la lista con nombre, tamaño y fecha

**Probar descarga:**
- [ ] Clic en botón "Descargar" de un backup
- [ ] Verificar que se descarga archivo .sql
- [ ] Abrir archivo con editor de texto y verificar que contiene datos SQL

**Probar restauración (⚠️ SOLO EN DESARROLLO):**
- [ ] Hacer modificación en BD (editar un bien, cambiar descripción)
- [ ] Clic en botón "Restaurar" de backup anterior
- [ ] **LEER ADVERTENCIA** completa
- [ ] Confirmar restauración
- [ ] Sistema cierra sesión automáticamente
- [ ] Volver a iniciar sesión
- [ ] Verificar que la modificación desapareció
- [ ] Revisar auditoría: debe haber registro "RESTAURAR BACKUP"

**Probar limpieza:**
- [ ] Clic en "Limpiar Antiguos"
- [ ] Confirmar acción
- [ ] Verificar que backups de más de 30 días se eliminaron

**Probar eliminación:**
- [ ] Clic en botón "Eliminar" de un backup
- [ ] Confirmar eliminación
- [ ] Verificar que desaparece de la lista

---

### 4. Protección CSRF ✅
**Automático en todos los formularios**

**Verificar:**
- [ ] Crear un bien nuevo → Debe funcionar correctamente
- [ ] Editar una persona → Debe funcionar correctamente
- [ ] Crear movimiento → Debe funcionar correctamente
- [ ] **Si hay error "Invalid CSRF token":**
  - Verificar que el formulario tiene `<?= csrf_field() ?>`
  - Verificar que peticiones AJAX incluyen token CSRF

---

### 5. Dashboard Mejorado ✅
**Ubicación:** `http://localhost/inventariov2/public/home`

**Verificar KPIs (6 cards superiores):**
- [ ] Total Equipos - Muestra número correcto
- [ ] Equipos Activos - Muestra cantidad
- [ ] En Mantenimiento - Muestra cantidad
- [ ] Asignados - Muestra cantidad y porcentaje
- [ ] Personas Activas - Muestra total
- [ ] Licencias Activas - Muestra total

**Verificar Alertas (si hay datos):**
- [ ] **Licencias por Vencer** - Muestra licencias que vencen en 30 días
  - Badge rojo si faltan ≤7 días
  - Badge amarillo si faltan ≤15 días
  - Badge verde si faltan >15 días
- [ ] **Préstamos por Vencer** - Muestra préstamos que vencen en 7 días
  - Badge rojo si faltan ≤2 días
  - Badge amarillo si faltan ≤5 días
  - Badge verde si faltan >5 días

**Verificar Gráficas:**
- [ ] **Gráfica de Líneas** - Movimientos últimos 6 meses
  - Se ve correctamente
  - Al pasar mouse muestra tooltip con datos
  - Leyenda en parte inferior
- [ ] **Gráfica de Dona** - Distribución por estado
  - Muestra colores distintos por estado
  - Al pasar mouse muestra porcentajes

**Verificar Secciones Adicionales:**
- [ ] Bienes por Tipo - Cards con cantidades
- [ ] Últimos 5 Movimientos - Tabla con registros recientes
- [ ] Top 5 Usuarios - Barras de progreso

---

### 6. Sistema de Notificaciones Email 📧 (Pendiente configuración)
**Ubicación:** `http://localhost/inventariov2/public/notificaciones/probar-email`

**Antes de probar:** Configurar SMTP en `app/Config/Email.php` (ver [CONFIGURACION_EMAIL.md](CONFIGURACION_EMAIL.md))

**Una vez configurado:**
- [ ] Acceder a `/notificaciones/probar-email`
- [ ] Debe mostrar "Email de prueba enviado correctamente"
- [ ] Revisar bandeja de entrada del email configurado
- [ ] Verificar que llegó el email de prueba

**Probar notificaciones automáticas:**
- [ ] POST a `/notificaciones/licencias` - Envía notificación de licencias por vencer
- [ ] POST a `/notificaciones/prestamos` - Envía notificación de préstamos por vencer
- [ ] POST a `/notificaciones/ejecutar` - Ejecuta todas las notificaciones

---

## 🔐 Permisos y Roles

**Verificar permisos de Backup:**
- [ ] Usuario admin puede acceder a `/backup`
- [ ] Usuario sin permisos ve error "No tienes permisos"

**Verificar permisos en phpMyAdmin:**
```sql
-- Ver permisos de backup
SELECT p.clave, p.descripcion 
FROM permisos p 
WHERE p.clave LIKE 'backup%';

-- Ver qué roles tienen permisos de backup
SELECT r.nombre as rol, p.clave as permiso
FROM roles r
INNER JOIN roles_permisos rp ON r.id = rp.rol_id
INNER JOIN permisos p ON rp.permiso_id = p.id
WHERE p.clave LIKE 'backup%';
```

---

## 🐛 Problemas Comunes y Soluciones

### Error: "Invalid CSRF token"
**Solución:**
1. Verificar que el formulario tiene `<?= csrf_field() ?>`
2. Para AJAX, agregar:
```javascript
data: {
    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
    // ... otros datos
}
```

### Error: "No tienes permisos para acceder a Backup"
**Solución:**
1. Ejecutar script SQL: `permisos_nuevos.sql` en phpMyAdmin
2. Cerrar sesión e iniciar sesión nuevamente

### Dashboard no muestra alertas
**Normal si:**
- No hay licencias próximas a vencer (30 días)
- No hay préstamos próximos a vencer (7 días)

### Gráficas no se ven
**Solución:**
1. Abrir consola del navegador (F12)
2. Verificar que no hay errores de Chart.js
3. Verificar que Chart.js se carga correctamente

### Backup da error "mysqldump not found"
**Solución:**
1. Verificar que XAMPP está instalado correctamente
2. Ruta esperada: `C:\xampp\mysql\bin\mysqldump.exe`
3. Si no existe, usar método alternativo PHP (ya incluido en código)

---

## 📊 Reportes a Generar para Prueba Completa

1. **Reporte de Auditoría:**
   - Ir a Auditoría
   - Exportar a Excel
   - Verificar que tiene todos los eventos

2. **Reporte de Bienes:**
   - Ir a Dashboard
   - Clic en "Exportar Todo" (exporta todos los bienes a Excel)

3. **Backup de Seguridad:**
   - Ir a Backup
   - Crear backup
   - Descargar archivo
   - Guardar en ubicación segura

---

## ✅ Checklist Final Antes de Producción

- [ ] **Todos los módulos tienen auditoría activa**
- [ ] **Historial funciona en todas las vistas principales**
- [ ] **Backup probado (crear, descargar, restaurar)**
- [ ] **CSRF activado y funcionando en todos los formularios**
- [ ] **Dashboard muestra datos correctos**
- [ ] **Permisos configurados correctamente**
- [ ] **Email configurado (si se requiere notificaciones)**
- [ ] **Backup automático programado (Task Scheduler)**
- [ ] **Documentación entregada al cliente**
- [ ] **Base de datos tiene backup reciente**
- [ ] **Credenciales de producción configuradas**
- [ ] **Logs revisados (sin errores críticos)**

---

## 📞 Soporte

**Archivos de documentación:**
- [CONFIGURACION_EMAIL.md](CONFIGURACION_EMAIL.md) - Configurar SMTP
- [PROGRAMAR_NOTIFICACIONES.md](PROGRAMAR_NOTIFICACIONES.md) - Automatizar notificaciones
- [PRUEBA_BACKUP.md](PRUEBA_BACKUP.md) - Guía detallada de pruebas de backup
- [SISTEMA_AUDITORIA.md](SISTEMA_AUDITORIA.md) - Documentación del sistema de auditoría

**Logs del sistema:**
- Auditoría: `public/auditoria`
- Errores PHP: `writable/logs/log-{fecha}.php`
- Backups: `writable/backups/`

**Comandos útiles:**
```bash
# Ver logs recientes
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').php -Tail 50

# Verificar backups
Get-ChildItem writable/backups/ | Sort-Object LastWriteTime -Descending

# Limpiar caché
php spark cache:clear
```
