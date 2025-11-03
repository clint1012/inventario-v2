<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Bienes</title>
    <!-- <style>
        

        h2, h3 {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header img {
            width: 70px;
            float: left;
        }

        .header h2 {
            font-size: 16px;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 10px;
        }

        .page-break {
            page-break-before: always;
        }
    </style> -->
</head>

<body>
    <!-- Encabezado con logo y fecha -->
    <div class="header">
        <img src="<?= base_url('assets/img/logo_tc.png') ?>" alt="Logo">
        <h2>TRIBUNAL CONSTITUCIONAL DEL PERÚ</h2>
        <h3>Reporte de Bienes Patrimoniales</h3>
        <div style="text-align: right; font-size: 12px; font-weight: bold;">
            Fecha: <?= date('d/m/Y') ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Estado</th>
                <th>Departamento</th>
                <th>Persona</th>
                <th>Fecha Adquisición</th>
                <th>Años Garantía</th>
                <th>Estado Garantía</th>
                <th>Proveedor</th>
                <th>Local</th>
            </tr>
        </thead>
        <tbody>
            <?php $contador = 0; ?>
            <?php foreach ($bienes as $bien): ?>
                <tr>
                    <td><?= esc($bien['cod_patrimonial']) ?></td>
                    <td><?= esc($bien['descripcion']) ?></td>
                    <td><?= esc($bien['marca']) ?></td>
                    <td><?= esc($bien['modelo']) ?></td>
                    <td><?= esc($bien['estado']) ?></td>
                    <td><?= esc($bien['nombre_departamento'] ?? '—') ?></td>
                    <td><?= esc($bien['nombre_persona'] ?? '—') ?></td>
                    <td><?= esc($bien['fecha_adquisicion']) ?></td>
                    <td><?= esc($bien['años_garantia']) ?></td>
                    <td><?= esc($bien['estado_garantia']) ?></td>
                    <td><?= esc($bien['proveedor']) ?></td>
                    <td><?= esc($bien['nombre_local'] ?? '—') ?></td>
                </tr>

                <?php $contador++; ?>
                <?php if ($contador % 40 == 0): ?>
                    <tr class="page-break"></tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Generado automáticamente el <?= date('d/m/Y H:i') ?>
    </div>
</body>
</html>
