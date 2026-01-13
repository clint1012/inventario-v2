# Instrucciones: Subida Masiva de Bienes

## 📋 Descripción

La funcionalidad de subida masiva permite:
- **Crear nuevos bienes** de forma rápida
- **Actualizar bienes existentes** de forma eficiente

El sistema identifica bienes por **código patrimonial** y solo actualiza los campos que presentan modificaciones.

## 🚀 Proceso de Uso

### 1. Descargar la Plantilla CSV

1. Ve a la sección **Bienes** en el sistema
2. Haz clic en el botón **"Descargar Plantilla CSV"**
3. Se descargará un archivo CSV con todos los bienes activos del sistema

### 2. Editar la Plantilla

#### Para Actualizar Bienes Existentes:
1. Abre el archivo descargado con **Excel**, **LibreOffice Calc** o cualquier editor de hojas de cálculo
2. **NO MODIFIQUES:**
   - La primera fila (encabezados)
   - La columna `id` (opcional, pero útil para referencia)
3. **MODIFICA:**
   - Cualquier otro campo según sea necesario

#### Para Crear Nuevos Bienes:
1. Agrega nuevas filas al final del archivo
2. **Campos obligatorios:**
   - `cod_patrimonial`: Código patrimonial único (OBLIGATORIO)
   - Puedes dejar el `id` vacío o con cualquier valor no numérico
3. **Llena los demás campos** según necesites

### 3. Guardar el Archivo

1. **IMPORTANTE:** Guarda el archivo como **CSV (UTF-8)** o **CSV**
2. No lo guardes como Excel (.xlsx) - debe ser .csv

### 4. Subir el Archivo

1. En la sección **Bienes**, haz clic en **"Subida Masiva"**
2. Selecciona el archivo CSV editado
3. Haz clic en **"Subir y Procesar"**
4. El sistema procesará el archivo y mostrará un resumen:
   - Cuántos bienes se crearon
   - Cuántos bienes se actualizaron
   - Errores si los hubiera

## 📊 Campos del CSV

### Campos Directos
- `cod_patrimonial`: Código patrimonial del bien
- `descripcion`: Descripción del bien
- `tipo_bien`: Tipo (laptop, desktop, monitor, etc.)
- `marca`: Marca del equipo
- `modelo`: Modelo del equipo
- `serie`: Número de serie
- `procesador`: Tipo de procesador
- `memoria`: Memoria RAM
- `tipo_disco`: Tipo de disco (HDD/SSD)
- `espacio_disco`: Capacidad de almacenamiento
- `sistema_operativo`: Sistema operativo instalado
- `ver_office`: Versión de Office
- `Ip`: Dirección IP
- `estado`: Estado actual del bien
- `fecha_adquisicion`: Fecha de adquisición (YYYY-MM-DD)
- `años_garantia`: Años de garantía
- `num_doc_compra`: Número de documento de compra

### Campos de Relación (busca por nombre)
- `local_nombre`: Nombre del local (se busca automáticamente)
- `departamento_nombre`: Nombre del departamento (se busca automáticamente)
- `persona_nombre_completo`: Nombre completo de la persona asignada (se busca automáticamente)
  - Para desasignar: coloca "Sin asignar" o "-"

## ✅ Características Importantes

### Identificación por Código Patrimonial
El sistema usa el **código patrimonial** como identificador único:
- Si el código patrimonial ya existe → **actualiza ese bien**
- Si el código patrimonial es nuevo → **crea un nuevo bien**
- El campo ID es opcional (se usa solo como referencia si está presente)

### Solo Actualiza lo Modificado
Para bienes existentes, el sistema compara cada campo:
- Si el valor es **igual**, no se actualiza
- Si el valor es **diferente**, se actualiza
- Esto optimiza el rendimiento y evita actualizaciones innecesarias

### Búsqueda Inteligente
Para los campos de relación (local, departamento, persona):
- El sistema busca por nombre
- Si no encuentra una coincidencia exacta, intenta búsqueda aproximada
- Si no encuentra nada, mantiene el valor actual o deja vacío (nuevo bien)

### Validación de Datos
- Verifica que el código patrimonial sea único para nuevos bienes
- Valida que los encabezados sean correctos
- Muestra errores específicos si algo falla

## 🔍 Ejemplos de Uso

### Ejemplo 1: Actualizar un Bien Existente

**CSV Descargado:**
```csv
id,cod_patrimonial,marca,modelo,memoria,estado,...
123,PAT-001,HP,ProBook 450,8GB,disponible,...
```

**CSV Editado:**
```csv
id,cod_patrimonial,marca,modelo,memoria,estado,...
123,PAT-001,Dell,Latitude 5420,16GB,asignado,...
```

**Resultado:**
- ✅ Se actualiza: marca (HP → Dell)
- ✅ Se actualiza: modelo (ProBook 450 → Latitude 5420)
- ✅ Se actualiza: memoria (8GB → 16GB)
- ✅ Se actualiza: estado (disponible → asignado)

### Ejemplo 2: Crear un Nuevo Bien

**CSV Editado (nueva fila al final):**
```csv
id,cod_patrimonial,descripcion,tipo_bien,marca,modelo,estado,...
,PAT-999,Laptop Nueva,laptop,Lenovo,ThinkPad X1,disponible,...
```

**Resultado:**
- ✅ Se crea un nuevo bien con código PAT-999
- ✅ Se asignan todos los campos proporcionados

### Ejemplo 3: Crear y Actualizar en un Solo Archivo

```csv
id,cod_patrimonial,marca,estado,...
123,PAT-001,Dell,asignado,...           (actualiza)
456,PAT-002,HP,mantenimiento,...        (actualiza)
,PAT-NEW-001,Lenovo,disponible,...      (crea nuevo)
,PAT-NEW-002,Acer,disponible,...        (crea nuevo)
```

**Resultado:**
- ✅ 2 bienes actualizados
- ✅ 2 bienes nuevos creados

## ⚠️ Errores Comunes

1. **"El formato del CSV no es válido"**
   - Solución: Usa la plantilla descargada, no crees un CSV desde cero

2. **"Código patrimonial es obligatorio"**
   - Solución: Asegúrate de llenar el campo cod_patrimonial en todas las filas

3. **"Código patrimonial duplicado"** (al crear)
   - Solución: Si el código ya existe, el sistema actualizará ese bien en lugar de crear uno nuevo

4. **"Error al procesar el archivo"**
   - Solución: Verifica que el archivo sea .csv y no .xlsx

5. **Caracteres extraños (tildes, ñ)**
   - Solución: Al guardar el CSV, selecciona codificación UTF-8

## 💡 Consejos y Mejores Prácticas

- **Haz una prueba con pocos registros primero**
- Mantén una copia de respaldo del CSV original
- Para crear nuevos bienes, agrégalos al final de la plantilla descargada
- Usa códigos patrimoniales únicos y descriptivos
- Revisa el mensaje de confirmación que indica:
  - Cuántos registros se crearon
  - Cuántos se actualizaron
  - Si hubo errores
- Si hay errores, el sistema te mostrará los primeros 5 para que puedas corregirlos

## 🎯 Casos de Uso Comunes

### 1. Actualización Masiva de Estado
Cambiar el estado de múltiples equipos (ej: de "disponible" a "en_mantenimiento")

### 2. Reasignación de Departamentos
Mover varios bienes de un departamento a otro

### 3. Carga Inicial de Inventario
Crear cientos de bienes nuevos desde un archivo Excel externo

### 4. Corrección de Datos
Actualizar información técnica (memoria, disco, etc.) de múltiples equipos

### 5. Actualización de Garantías
Actualizar fechas y años de garantía masivamente

## 📞 Soporte

Si tienes problemas o dudas, contacta al administrador del sistema.
