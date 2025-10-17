<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cargo por Lote</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: left; }
        h2 { text-align: center; margin: 0; }

        .header {
            width: 100%;
            border: none;
            margin-bottom: 20px;
        }
        .header td {
            border: none;
            vertical-align: middle;
        }
        .logo {
            width: 120px;
        }
        .info p { margin: 4px 0; }
        .firmas {
            margin-top: 100px;
            width: 100%;
            text-align: center;
        }
        .firmas td {
            padding: 50px 20px;
            vertical-align: bottom;
        }
        .linea {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
            height: 5px;
        }
    </style>
</head>
<body>
    <!-- Cabecera con logo y título -->
    <table class="header">
        <tr>
            <td class="logo">
                <img src="<?= FCPATH . 'sb2/img/tc_logo_superior.png' ?>" width="120">
            </td>
            <td style="text-align: center; width: 100%;">
                <h2>REPORTE DE CARGO POR LOTE</h2>
                <p>Fecha de emisión: <?= date('d/m/Y H:i:s') ?></p>
            </td>
        </tr>
    </table>

    <!-- Información general del lote -->
    <div class="info">
        <?php $primero = $movimientos[0]; ?>
        <p><strong>Usuario actual:</strong> <?= $primero['nombre'].' '.$primero['ape_paterno'].' '.$primero['ape_materno'] ?></p>

        <?php if (!empty($primero['nombre_anterior'])): ?>
            <p><strong>Usuario anterior:</strong> <?= $primero['nombre_anterior'].' '.$primero['apep_anterior'].' '.$primero['apem_anterior'] ?></p>
        <?php endif; ?>

        <p><strong>Departamento:</strong> <?= $primero['departamento'] ?? '-' ?></p>
        <p><strong>Local:</strong> <?= $primero['local'] ?? '-' ?></p>
        <p><strong>Tipo de movimiento:</strong> <?= ucfirst($primero['tipo_movimiento']) ?></p>
        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i:s', strtotime($primero['fecha_movimiento'])) ?></p>
        <p><strong>Observaciones:</strong> <?= $primero['observaciones'] ?: '-' ?></p>
    </div>

    <!-- Tabla de bienes del lote -->
    <table>
        <thead>
            <tr>
                <th>Código Patrimonial</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Serie</th>
                <th>Usuario</th>
                <th>Departamento</th>
                <th>Local</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movimientos as $m): ?>
                <tr>
                    <td><?= $m['cod_patrimonial'] ?></td>
                    <td><?= $m['descripcion'] ?></td>
                    <td><?= $m['marca'] ?></td>
                    <td><?= $m['modelo'] ?></td>
                    <td><?= $m['serie'] ?></td>
                    <td><?= $m['nombre'].' '.$m['ape_paterno'].' '.$m['ape_materno'] ?></td>
                    <td><?= $m['departamento'] ?></td>
                    <td><?= $m['local'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Sección de firmas -->
    <table class="firmas">
        <tr>
            <td>
                <div class="linea"></div>
                <p>Usuario de destino<br>
                   <?= $primero['nombre'].' '.$primero['ape_paterno'].' '.$primero['ape_materno'] ?>
                </p>
            </td>
            <td>
                <div class="linea"></div>
                <p>Técnico responsable<br>
                   <?= $primero['usuario_registro'] ?? '________________' ?>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
