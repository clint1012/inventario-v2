<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimiento</title>
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
        .firmas {
            margin-top: 80px;
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
                <img src="<?= base_url('assets/img/logo.png') ?>" width="100">
            </td>
            <td style="text-align: center; width: 100%;">
                <h2>Reporte de Movimiento</h2>
            </td>
        </tr>
    </table>

    <p><strong>Bien:</strong> <?= $movimiento['cod_patrimonial'] ?> - <?= $movimiento['descripcion'] ?></p>
    <p><strong>Usuario actual:</strong> <?= $movimiento['nombre'].' '.$movimiento['ape_paterno'].' '.$movimiento['ape_materno'] ?></p>
    
    <?php if (!empty($movimiento['nombre_anterior'])): ?>
        <p><strong>Usuario anterior:</strong> <?= $movimiento['nombre_anterior'].' '.$movimiento['apep_anterior'].' '.$movimiento['apem_anterior'] ?></p>
    <?php endif; ?>

    <p><strong>Departamento:</strong> <?= $movimiento['departamento'] ?? '-' ?></p>
    <p><strong>Local:</strong> <?= $movimiento['local'] ?? '-' ?></p>
    <p><strong>Tipo de movimiento:</strong> <?= ucfirst($movimiento['tipo_movimiento']) ?></p>
    <p><strong>Fecha:</strong> <?= date('d/m/Y H:i:s', strtotime($movimiento['fecha_movimiento'])) ?></p>
    <p><strong>Observaciones:</strong> <?= $movimiento['observaciones'] ?: '-' ?></p>

    <table>
        <thead>
            <tr>
                <th>Código Patrimonial</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Serie</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $movimiento['cod_patrimonial'] ?></td>
                <td><?= $movimiento['descripcion'] ?></td>
                <td><?= $movimiento['marca'] ?></td>
                <td><?= $movimiento['modelo'] ?></td>
                <td><?= $movimiento['serie'] ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Sección de firmas -->
    <table class="firmas">
        <tr>
            <td>
                <div class="linea"></div>
                <p>Usuario de destino<br>
                   <?= $movimiento['nombre'].' '.$movimiento['ape_paterno'].' '.$movimiento['ape_materno'] ?>
                </p>
            </td>
            <td>
                <div class="linea"></div>
                <p>Técnico responsable<br>
                   <?= $movimiento['usuario_registro'] ?>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
