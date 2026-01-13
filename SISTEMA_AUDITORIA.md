# Sistema de Auditoría - Inventario v2

## 📋 Descripción
El módulo de auditoría registra automáticamente todas las acciones importantes realizadas por los usuarios en el sistema, proporcionando trazabilidad completa y facilitando el análisis de eventos.

## 🎯 Características

### ✅ Funcionalidades Principales
- **Registro Automático** de eventos del sistema
- **Filtros Avanzados** por usuario, módulo, acción, y fechas
- **Vista Detallada** de cada evento con información técnica
- **Exportación a Excel** de registros filtrados
- **Estadísticas en Tiempo Real** (total eventos, eventos hoy, módulos activos)
- **Detección Automática** de navegador y sistema operativo
- **Soporte para JSON** en detalles adicionales

### 📊 Estadísticas Disponibles
- Total de eventos registrados
- Eventos del día actual
- Módulos activos
- Usuarios activos del día
- Top 10 módulos más usados
- Top 10 acciones más frecuentes
- Top 10 usuarios más activos

## 🔧 Instalación

### 1. Ejecutar la Migración
```bash
php spark migrate
```

Esto creará la tabla `auditoria` con los siguientes campos:
- id
- usuario_id
- usuario_nombre
- accion
- modulo
- registro_id
- detalles
- ip_address
- user_agent
- created_at

### 2. Verificar Rutas
Las siguientes rutas ya están configuradas en `app/Config/Routes.php`:
```php
$routes->resource('auditoria', ['placeholder' => '(:num)', 'filter' => 'auth']);
$routes->get('auditoria/exportar', 'Auditoria::exportar', ['filter' => 'auth']);
$routes->post('auditoria/limpiar', 'Auditoria::limpiar', ['filter' => 'auth']);
```

## 💻 Uso en el Código

### Registrar un Evento Manualmente
```php
use App\Models\AuditoriaModel;

// Sintaxis básica
AuditoriaModel::registrar('CREAR', 'Bienes', $bien_id);

// Con detalles adicionales (texto)
AuditoriaModel::registrar('EDITAR', 'Bienes', $bien_id, 'Se modificó el estado a asignado');

// Con detalles adicionales (array - se convierte a JSON)
AuditoriaModel::registrar('ELIMINAR', 'Usuarios', $usuario_id, [
    'nombre' => $usuario['nombre'],
    'rol' => $usuario['rol'],
    'motivo' => 'Usuario inactivo'
]);
```

### Tipos de Acciones Recomendadas
- **CREAR**: Cuando se crea un nuevo registro
- **EDITAR**: Cuando se modifica un registro existente
- **ELIMINAR**: Cuando se elimina un registro
- **LOGIN**: Inicio de sesión exitoso
- **LOGOUT**: Cierre de sesión
- **VER**: Consulta de información sensible
- **EXPORTAR**: Descarga de reportes
- **IMPORTAR**: Carga masiva de datos
- **ANULAR**: Anulación de operaciones
- **APROBAR**: Aprobación de procesos

### Módulos Recomendados
- Bienes
- Movimientos
- Usuarios
- Inventario
- Licencias
- Celulares
- Personas
- Departamentos
- Sistema

## 📝 Ejemplos de Implementación

### Ejemplo 1: Registrar Creación de Bien
```php
// En Bienes::create()
public function create()
{
    // ... código de creación ...
    
    $bien_id = $this->bienesModel->insert($data);
    
    // Registrar auditoría
    AuditoriaModel::registrar('CREAR', 'Bienes', $bien_id, [
        'cod_patrimonial' => $data['cod_patrimonial'],
        'tipo_bien' => $data['tipo_bien'],
        'descripcion' => $data['descripcion']
    ]);
    
    // ... resto del código ...
}
```

### Ejemplo 2: Registrar Edición con Cambios
```php
// En Bienes::update()
public function update($id)
{
    $bien_anterior = $this->bienesModel->find($id);
    
    // ... código de actualización ...
    
    $this->bienesModel->update($id, $data);
    
    // Registrar cambios específicos
    $cambios = [];
    if ($bien_anterior['estado'] !== $data['estado']) {
        $cambios['estado'] = [
            'anterior' => $bien_anterior['estado'],
            'nuevo' => $data['estado']
        ];
    }
    
    AuditoriaModel::registrar('EDITAR', 'Bienes', $id, $cambios);
}
```

### Ejemplo 3: Registrar Login
```php
// En Login::doLogin()
if ($usuario) {
    session()->set($usuario);
    
    AuditoriaModel::registrar('LOGIN', 'Sistema', $usuario['id'], [
        'username' => $usuario['username'],
        'rol' => $usuario['rol']
    ]);
    
    return redirect()->to(base_url('dashboard'));
}
```

### Ejemplo 4: Registrar Eliminación
```php
// En Bienes::delete()
public function delete($id)
{
    $bien = $this->bienesModel->find($id);
    
    if ($this->bienesModel->delete($id)) {
        AuditoriaModel::registrar('ELIMINAR', 'Bienes', $id, [
            'cod_patrimonial' => $bien['cod_patrimonial'],
            'descripcion' => $bien['descripcion'],
            'motivo' => 'Eliminación permanente'
        ]);
    }
}
```

## 🔍 Filtros Disponibles

### En la Vista Web
- **Usuario**: Búsqueda por nombre de usuario
- **Módulo**: Filtrar por módulo específico
- **Acción**: Filtrar por tipo de acción
- **Fecha Desde**: Fecha inicial del rango
- **Fecha Hasta**: Fecha final del rango

### Programáticamente
```php
$auditoriaModel = new AuditoriaModel();

$filtros = [
    'usuario' => 'Juan Pérez',
    'modulo' => 'Bienes',
    'accion' => 'CREAR',
    'fecha_desde' => '2026-01-01',
    'fecha_hasta' => '2026-01-31'
];

$builder = $auditoriaModel->getAuditoriaConFiltros($filtros);
$registros = $builder->findAll();
```

## 📤 Exportación

### Exportar a Excel
1. Aplicar los filtros deseados en la interfaz
2. Hacer clic en el botón "Exportar Excel"
3. Se descargará un archivo con todos los registros filtrados

### Características del Excel
- Incluye todos los campos principales
- Formato profesional con encabezados en azul
- Columnas auto-ajustadas
- Nombre del archivo con timestamp

## 🧹 Mantenimiento

### Limpiar Registros Antiguos
Para evitar que la tabla crezca indefinidamente, se puede limpiar registros antiguos:

```php
// Eliminar registros mayores a 90 días (solo administradores)
POST /auditoria/limpiar
{
    "dias": 90
}
```

**Nota**: Esta acción también se registra en la auditoría.

## 🎨 Interfaz de Usuario

### Página Principal
- **Tarjetas de Estadísticas** en la parte superior
- **Panel de Filtros** expandible
- **Tabla de Eventos** con paginación (20 por página)
- **Badges de colores** según tipo de acción:
  - Verde: CREAR
  - Amarillo: EDITAR
  - Rojo: ELIMINAR
  - Azul: LOGIN
  - Gris: LOGOUT
  - Cyan: VER

### Página de Detalle
- **Información del Evento**: Fecha, usuario, módulo, acción
- **Información Técnica**: IP, navegador, sistema operativo
- **Detalles Adicionales**: JSON formateado o texto plano

## 🔒 Seguridad

- Todos los endpoints requieren autenticación (`filter' => 'auth'`)
- La limpieza de registros requiere rol de administrador
- Las IPs y User Agents se registran automáticamente
- Los detalles sensibles se pueden omitir si es necesario

## 📌 Mejores Prácticas

1. **Registrar acciones importantes**: No todo necesita auditoría, enfócate en operaciones críticas
2. **Usar detalles JSON**: Para información estructurada que pueda ser útil en el futuro
3. **Incluir contexto**: Agregar información relevante sobre el cambio realizado
4. **Mantener nombres consistentes**: Usar siempre los mismos nombres de módulos y acciones
5. **Limpiar periódicamente**: Establecer una política de retención (ej: 6 meses)

## 🚀 Integración con Otros Módulos

### Agregar Auditoría a un Nuevo Módulo
1. Importar el modelo: `use App\Models\AuditoriaModel;`
2. Llamar al método estático después de cada operación importante
3. Usar nombres de módulo y acción consistentes

### Ejemplo Completo en un Controller
```php
<?php

namespace App\Controllers;

use App\Models\MiModel;
use App\Models\AuditoriaModel;

class MiController extends BaseController
{
    public function create()
    {
        $data = $this->request->getPost();
        $id = $this->miModel->insert($data);
        
        AuditoriaModel::registrar('CREAR', 'MiModulo', $id, $data);
        
        return redirect()->to('mimodulo')->with('success', 'Creado correctamente');
    }
}
```

## 📞 Soporte
Para dudas o problemas con el módulo de auditoría, revisar el código en:
- **Modelo**: `app/Models/AuditoriaModel.php`
- **Controller**: `app/Controllers/Auditoria.php`
- **Vistas**: `app/Views/auditoria/`
- **Rutas**: `app/Config/Routes.php`
