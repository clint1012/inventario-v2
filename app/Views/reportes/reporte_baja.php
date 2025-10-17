<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Bienes Retirados</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Reporte de Bienes Retirados</h2>
    <table>
        <thead>
            <tr>
                <th>Código Patrimonial</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Modelo</th>
                
                <th>Estado</th>
                <th>Fecha de Compra</th>
                <th>Estado de Garantía</th>
                <th>Proveedor</th>
                <th>Motivo de baja</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bienes as $bien) : ?>
                <tr>
                    <td><?= $bien['cod_patrimonial'] ?></td>
                    <td><?= $bien['descripcion'] ?></td>
                    <td><?= $bien['marca'] ?></td>
                    <td><?= $bien['modelo'] ?></td>
                    
                    <td><?= $bien['estado'] ?></td>
                    <td><?= $bien['fecha_adquisicion'] ?></td>
                    <td><?= $bien['estado_garantia'] ?></td>
                    <td><?= $bien['proveedor'] ?></td>
                    <td><?= $bien['motivo_baja'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>