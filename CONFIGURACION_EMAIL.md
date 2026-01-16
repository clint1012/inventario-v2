# Configuración del Sistema de Notificaciones por Email

## 📧 Descripción General

El sistema incluye notificaciones automáticas por email para:
- **Licencias próximas a vencer** (dentro de 30 días)
- **Préstamos próximos a vencer** (dentro de 7 días)

## ⚙️ Configuración Básica

### 1. Configurar Credenciales SMTP

Editar el archivo: `app/Config/Email.php`

```php
public string $fromEmail = 'sistema@tudominio.com';
public string $fromName = 'Sistema de Inventario';

public string $SMTPHost = 'smtp.gmail.com';      // Servidor SMTP
public string $SMTPUser = 'tucorreo@gmail.com';  // Usuario SMTP
public string $SMTPPass = 'tu_contraseña';       // Contraseña SMTP
public string $SMTPPort = 587;                   // Puerto SMTP
public string $SMTPCrypto = 'tls';               // tls o ssl
```

### 2. Ejemplos de Configuración por Proveedor

#### Gmail
```php
public string $SMTPHost = 'smtp.gmail.com';
public string $SMTPPort = 587;
public string $SMTPCrypto = 'tls';
```

**Nota**: Para Gmail necesitas usar "Contraseña de Aplicación":
1. Ir a https://myaccount.google.com/security
2. Activar "Verificación en 2 pasos"
3. Generar "Contraseña de aplicación"
4. Usar esa contraseña en `$SMTPPass`

#### Outlook/Office 365
```php
public string $SMTPHost = 'smtp-mail.outlook.com';
public string $SMTPPort = 587;
public string $SMTPCrypto = 'tls';
```

#### Servidor SMTP Personalizado
```php
public string $SMTPHost = 'mail.tudominio.com';
public string $SMTPUser = 'noreply@tudominio.com';
public string $SMTPPass = 'password_segura';
public string $SMTPPort = 465;
public string $SMTPCrypto = 'ssl';
```

## 🧪 Probar Configuración

### Método 1: Navegador
1. Acceder a: `http://tudominio.com/notificaciones/probar-email`
2. Si está correctamente configurado, verás:
   ```
   Email de prueba enviado correctamente
   ```

### Método 2: Terminal/Consola
```bash
cd c:\xampp\htdocs\inventariov2
php spark
```
Luego en la consola de Spark:
```php
$notif = new \App\Controllers\Notificaciones();
$notif->probarEmail();
```

## 📅 Automatizar Notificaciones

### Opción 1: Cron Job (Linux/Mac)

Editar crontab:
```bash
crontab -e
```

Agregar líneas:
```cron
# Ejecutar diariamente a las 8:00 AM
0 8 * * * cd /var/www/html/inventariov2 && php spark notificaciones:ejecutar

# Licencias cada lunes a las 9:00 AM
0 9 * * 1 cd /var/www/html/inventariov2 && php spark notificaciones:licencias

# Préstamos diariamente a las 10:00 AM
0 10 * * * cd /var/www/html/inventariov2 && php spark notificaciones:prestamos
```

### Opción 2: Programador de Tareas (Windows)

1. Abrir "Programador de tareas"
2. Crear tarea básica
3. **Desencadenador**: Diariamente a las 8:00 AM
4. **Acción**: Iniciar programa
   - **Programa**: `C:\xampp\php\php.exe`
   - **Argumentos**: `spark notificaciones:ejecutar`
   - **Iniciar en**: `C:\xampp\htdocs\inventariov2`

### Opción 3: Manual desde el Sistema

Acceder a una URL protegida con autenticación:
```
POST http://tudominio.com/notificaciones/ejecutar
POST http://tudominio.com/notificaciones/licencias
POST http://tudominio.com/notificaciones/prestamos
```

## 🎨 Personalizar Plantillas de Email

Las plantillas están en `app/Views/emails/`:

- **licencias_vencer.php** - Email de licencias por vencer
- **prestamos_vencer.php** - Email de préstamos por vencer
- **prueba.php** - Email de prueba

### Estructura de Plantilla

```php
<?php
$header = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        /* Tus estilos CSS aquí */
    </style>
</head>
<body>';

$footer = '</body></html>';

echo $header;
?>

<!-- Contenido HTML del email aquí -->

<?= $footer ?>
```

## 🔍 Destinatarios de Notificaciones

### Licencias por Vencer
Se envía a **todos los usuarios activos** del sistema que tengan email configurado.

```sql
SELECT email FROM usuarios WHERE estado = 1 AND email IS NOT NULL
```

### Préstamos por Vencer
Se envía **individualmente** a cada usuario que tenga préstamos pendientes.

```sql
SELECT DISTINCT p.email
FROM asignacion a
INNER JOIN personas p ON a.persona_id = p.id
WHERE a.tipo_movimiento = 'PRESTAMO'
  AND a.fecha_devolucion_estimada BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
  AND a.estado_actual = 'ASIGNADO'
```

## 📊 Verificar Logs

Los logs de email se guardan en: `writable/logs/log-{fecha}.php`

Buscar por:
```log
INFO --> Email de licencias enviado exitosamente
ERROR --> Error al enviar email: [mensaje de error]
```

## ⚠️ Solución de Problemas

### Error: "Failed to connect to server"
**Solución**:
1. Verificar firewall permite conexión al puerto SMTP (587 o 465)
2. Verificar que `$SMTPHost` es correcto
3. Probar con `telnet smtp.gmail.com 587`

### Error: "Authentication failed"
**Solución**:
1. Verificar `$SMTPUser` y `$SMTPPass`
2. Para Gmail, usar "Contraseña de aplicación"
3. Verificar que la cuenta no tenga bloqueada la opción "Acceso de apps menos seguras"

### Error: "Email must be a valid email address"
**Solución**:
1. Verificar que `$fromEmail` tiene formato válido
2. Verificar que usuarios en BD tienen emails válidos:
   ```sql
   SELECT id, nombre, email FROM usuarios WHERE email NOT LIKE '%@%'
   ```

### Emails no llegan (sin error)
**Solución**:
1. Revisar carpeta de SPAM
2. Verificar configuración SPF/DKIM del dominio
3. Probar con otro email de destino
4. Revisar logs del servidor SMTP

## 🔐 Seguridad

### Recomendaciones:
1. **Nunca** subir `Email.php` con credenciales a repositorios públicos
2. Usar variables de entorno:
   ```php
   public string $SMTPUser = $_ENV['SMTP_USER'];
   public string $SMTPPass = $_ENV['SMTP_PASS'];
   ```
3. Crear archivo `.env`:
   ```env
   SMTP_USER=tucorreo@gmail.com
   SMTP_PASS=tu_contraseña_app
   ```
4. Agregar `Email.php` al `.gitignore`:
   ```
   app/Config/Email.php
   ```

## 📞 Contacto de Soporte

Para problemas de configuración:
1. Revisar logs en `writable/logs/`
2. Ejecutar `notificaciones/probar-email`
3. Verificar configuración con el administrador del servidor SMTP
