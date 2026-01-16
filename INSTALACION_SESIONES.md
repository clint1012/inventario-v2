# ⚡ INSTALACIÓN RÁPIDA - SISTEMA DE SESIONES

## 🎯 PASOS DE INSTALACIÓN (5 minutos)

### **1️⃣ EJECUTAR SQL (2 minutos)**

1. Abrir **phpMyAdmin**: http://localhost/phpmyadmin
2. Seleccionar base de datos: **inventariov2**
3. Click en pestaña **"SQL"**
4. Copiar y pegar **TODO** el contenido de: `sesiones_activas.sql`
5. Click en **"Continuar"**

**✅ Verificación:**
```sql
-- Ejecutar estas consultas para verificar:
SELECT * FROM sesiones_activas;
SELECT * FROM historial_logins;
SELECT * FROM permisos WHERE id IN (24, 25, 26);
SELECT * FROM roles_permisos WHERE permiso_id IN (24, 25, 26);
```

**Debe mostrar:**
- Tablas vacías `sesiones_activas` y `historial_logins` ✅
- 3 permisos nuevos (IDs 24, 25, 26) ✅
- 3 registros en `roles_permisos` asignados al rol 1 ✅

---

### **2️⃣ LIMPIAR CACHÉ (30 segundos)**

Abrir PowerShell en el proyecto:
```powershell
cd c:\xampp\htdocs\inventariov2
php spark cache:clear
```

---

### **3️⃣ PROBAR EL SISTEMA (2 minutos)**

1. **Abrir:** http://localhost/inventariov2
2. **Login** como administrador
3. **Verificar menú lateral:** debe aparecer **"Sesiones Activas"** (con ícono 👥)
4. **Click en "Sesiones Activas"**
5. **Verificar:**
   - ✅ Aparecen 4 tarjetas de estadísticas
   - ✅ Tabla muestra tu sesión actual
   - ✅ Tu sesión dice "Tu sesión" en acciones
   - ✅ Se muestran: usuario, nombre, IP, navegador, SO

---

## 🧪 PRUEBA COMPLETA (Opcional - 5 minutos)

### **Test 1: Múltiples Sesiones**
```
1. Abrir navegador en modo INCÓGNITO
2. Login con OTRO usuario
3. En ventana NORMAL: ir a Sesiones Activas
4. VERIFICAR: aparecen 2 sesiones
5. VERIFICAR: estadística "Sesiones Activas" = 2
```

### **Test 2: Cerrar Sesión Remota**
```
1. Con 2 sesiones activas (normal + incógnito)
2. En ventana NORMAL: click en botón ROJO "Cerrar" de la sesión incógnito
3. Confirmar diálogo
4. VERIFICAR: mensaje "Sesión cerrada exitosamente"
5. En ventana INCÓGNITO: intentar navegar → debe redirigir a LOGIN
```

### **Test 3: Historial**
```
1. Click en botón "Ver Historial"
2. VERIFICAR: aparecen tus logins del día
3. VERIFICAR: filtros funcionan (cambiar fecha)
4. VERIFICAR: badges de colores (LOGIN verde, LOGOUT azul)
```

---

## ⚠️ SI ALGO FALLA

### **Error: "sesiones_activas table doesn't exist"**
```
✅ Ejecutar nuevamente el SQL completo en phpMyAdmin
✅ Verificar que estás en la base de datos correcta (inventariov2)
```

### **No aparece menú "Sesiones Activas"**
```
✅ Hacer LOGOUT y LOGIN nuevamente
✅ Verificar que eres administrador
✅ Ejecutar: php spark cache:clear
```

### **Sesiones no se registran al hacer login**
```
✅ Verificar que ejecutaste el SQL completo
✅ Verificar logs en: writable/logs/log-[fecha].log
✅ Hacer logout/login nuevamente
```

---

## ✅ CHECKLIST DE INSTALACIÓN

- [ ] SQL ejecutado en phpMyAdmin
- [ ] Tablas `sesiones_activas` y `historial_logins` creadas
- [ ] 3 permisos nuevos creados (IDs 24-26)
- [ ] Permisos asignados a rol administrador
- [ ] Caché limpiado con `php spark cache:clear`
- [ ] Menú "Sesiones Activas" visible en sidebar
- [ ] Vista principal carga correctamente
- [ ] Tu sesión aparece en la tabla
- [ ] Botón "Ver Historial" funciona
- [ ] Historial muestra tu login actual

---

## 📚 DOCUMENTACIÓN COMPLETA

Para más detalles, consultar: **SISTEMA_SESIONES.md**

---

**¡Instalación completada!** 🎉

Ahora puedes:
- ✅ Monitorear usuarios conectados en tiempo real
- ✅ Cerrar sesiones remotamente
- ✅ Ver historial completo de logins
- ✅ Detectar sesiones múltiples
- ✅ Auditar todos los accesos al sistema
