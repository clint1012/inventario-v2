<?php
// Script simple de verificación sin bootstrap de CodeIgniter

echo "=== VERIFICACIÓN DEL SISTEMA DE AUDITORÍA ===\n\n";

// 1. Verificar archivos del sistema
echo "1. Verificando archivos del sistema...\n";
$archivos = [
    'app/Models/AuditoriaModel.php' => 'Modelo',
    'app/Controllers/Auditoria.php' => 'Controlador',
    'app/Views/auditoria/index.php' => 'Vista Index',
    'app/Views/auditoria/detalle.php' => 'Vista Detalle',
    'app/Database/Migrations/2026-01-12-000001_CreateAuditoriaTable.php' => 'Migración'
];

$todosExisten = true;
foreach ($archivos as $archivo => $nombre) {
    if (file_exists($archivo)) {
        echo "   ✓ $nombre existe\n";
    } else {
        echo "   ✗ $nombre NO existe\n";
        $todosExisten = false;
    }
}

if (!$todosExisten) {
    echo "\n✗ Algunos archivos no existen. Verifica la instalación.\n";
    exit(1);
}

echo "\n2. Verificando integración en controllers...\n";
$controllers = [
    'app/Controllers/Bienes.php' => 'AuditoriaModel::registrar',
    'app/Controllers/Asignacion.php' => 'AuditoriaModel::registrar',
    'app/Controllers/Login.php' => 'AuditoriaModel::registrar'
];

$todosIntegrados = true;
foreach ($controllers as $controller => $buscar) {
    if (!file_exists($controller)) {
        echo "   ✗ " . basename($controller) . " NO existe\n";
        $todosIntegrados = false;
        continue;
    }
    
    $contenido = file_get_contents($controller);
    if (strpos($contenido, $buscar) !== false) {
        // Contar cuántas veces aparece
        $count = substr_count($contenido, $buscar);
        echo "   ✓ " . basename($controller) . " integrado ($count llamadas)\n";
    } else {
        echo "   ✗ " . basename($controller) . " NO integrado\n";
        $todosIntegrados = false;
    }
}

echo "\n3. Verificando menú en plantilla...\n";
if (file_exists('app/Views/plantilla.php')) {
    $plantilla = file_get_contents('app/Views/plantilla.php');
    if (strpos($plantilla, 'auditoria') !== false && strpos($plantilla, 'fa-history') !== false) {
        echo "   ✓ Menú de Auditoría agregado\n";
    } else {
        echo "   ✗ Menú de Auditoría NO agregado\n";
    }
} else {
    echo "   ✗ Plantilla no encontrada\n";
}

echo "\n4. Verificando rutas...\n";
if (file_exists('app/Config/Routes.php')) {
    $routes = file_get_contents('app/Config/Routes.php');
    if (strpos($routes, "resource('auditoria'") !== false) {
        echo "   ✓ Rutas de auditoría configuradas\n";
    } else {
        echo "   ✗ Rutas de auditoría NO configuradas\n";
    }
} else {
    echo "   ✗ Archivo de rutas no encontrado\n";
}

echo "\n5. Verificando migración ejecutada...\n";
// Conectar directamente a MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'inventariov2';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        echo "   ⚠ No se pudo conectar a la BD: " . $conn->connect_error . "\n";
    } else {
        // Verificar si existe la tabla
        $result = $conn->query("SHOW TABLES LIKE 'auditoria'");
        if ($result && $result->num_rows > 0) {
            echo "   ✓ Tabla 'auditoria' existe en la base de datos\n";
            
            // Contar registros
            $result = $conn->query("SELECT COUNT(*) as total FROM auditoria");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "   ℹ Total de registros: {$row['total']}\n";
            }
        } else {
            echo "   ✗ Tabla 'auditoria' NO existe en la base de datos\n";
            echo "   ⚠ Ejecuta: php spark migrate\n";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "   ⚠ Error al verificar BD: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";
echo "\nAcceso al sistema:\n";
echo "URL: http://localhost/inventariov2/auditoria\n";
echo "\nDocumentación: SISTEMA_AUDITORIA.md\n";
