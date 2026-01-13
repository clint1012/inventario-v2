<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de <?= $tipo ?> de Celulares</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
        }
        .info-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 5px;
        }
        .bienes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .bienes-table th,
        .bienes-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .bienes-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .firmas {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .firma-box {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }
        .firma-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 9px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>ACTA DE <?= strtoupper($tipo) ?> DE EQUIPOS CELULARES</h2>
        <p><strong>Lote:</strong> <?= $lote ?></p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td width="20%"><strong>Usuario:</strong></td>
                <td width="80%"><?= $usuario ?></td>
            </tr>
            <tr>
                <td><strong>Departamento:</strong></td>
                <td><?= $departamento ?></td>
            </tr>
            <tr>
                <td><strong>Local:</strong></td>
                <td><?= $local ?></td>
            </tr>
            <tr>
                <td><strong>Fecha:</strong></td>
                <td><?= $fecha ?></td>
            </tr>
            <tr>
                <td><strong>Tipo:</strong></td>
                <td><?= $tipo ?></td>
            </tr>
            <tr>
                <td><strong>Responsable:</strong></td>
                <td><?= esc($responsable) ?></td>
            </tr>
        </table>
    </div>

    <h3>Detalle de Equipos Celulares:</h3>
    <table class="bienes-table">
        <thead>
            <tr>
                <th width="5%">N°</th>
                <th width="20%">IMEI</th>
                <th width="15%">N/S</th>
                <th width="25%">Modelo</th>
                <th width="35%">Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($movimientos as $mov): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= esc($mov['imei']) ?></td>
                    <td><?= esc($mov['numero_serie'] ?? 'N/A') ?></td>
                    <td><?= esc($mov['modelo']) ?></td>
                    <td><?= esc($mov['celular_desc'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!empty($movimientos[0]['observaciones'])): ?>
        <div style="margin-top: 15px;">
            <strong>Observaciones:</strong>
            <p style="border: 1px solid #ccc; padding: 8px; margin-top: 5px;">
                <?= nl2br(esc($movimientos[0]['observaciones'])) ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="firmas">
        <div class="firma-box">
            <div class="firma-line">
                <strong><?= $usuario ?></strong><br>
                Usuario Receptor/Entregador
            </div>
        </div>
        <div class="firma-box">
            <div class="firma-line">
                <strong><?= strtoupper(esc($responsable)) ?></strong><br>
                Responsable de la Acción
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente el <?= date('d/m/Y H:i:s') ?></p>
        <p>Sistema de Gestión de Inventario v2.0</p>
    </div>
</body>
</html>
