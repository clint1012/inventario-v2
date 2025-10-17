<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Bienes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Reporte de Bienes</h2>
    <div style="text-align: right; font-size: 12px; font-weight: bold;">
        <?= date('d/m/Y') ?>
    </div>
    <h3 style="text-align: start;">ENTIDAD: TRIBUNAL CONSTITUCIONAL DEL PERU</h3>
    <h3 style="text-align: start;">DEPENDENCIA: TRIBUNAL CONSTITUCIONAL</h3>

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
                <th>Fecha Adq.</th>
                <th>Años Garantía</th>
                <th>Estado Garantía</th>
                <th>Proveedor</th>
                <th>Local</th>
            </tr>
        </thead>
        <tbody>
            <?php $counter = 0; ?>
            <?php foreach ($bienes as $bien): ?>
                <tr>
                    <td><?= $bien['cod_patrimonial'] ?></td>
                    <td><?= $bien['descripcion'] ?></td>
                    <td><?= $bien['marca'] ?></td>
                    <td><?= $bien['modelo'] ?></td>
                    <td><?= $bien['estado'] ?></td>
                    <td><?= $bien['id_departamento'] ?></td>
                    <td><?= $bien['id_personas'] ?></td>
                    <td><?= $bien['fecha_adquisicion'] ?></td>
                    <td><?= $bien['años_garantia'] ?></td>
                    <td><?= $bien['estado_garantia'] ?></td>
                    <td><?= $bien['proveedor'] ?></td>
                    <td><?= $bien['id_locales'] ?></td>
                </tr>
                <?php $counter++; ?>
                <?php if ($counter % 40 == 0): ?> <!-- Esto fuerza un salto de página cada 40 filas -->
                    <tr>
                        <td colspan="16" style="page-break-before: always;"></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Fecha en la parte inferior derecha -->
    
</body>

</html>