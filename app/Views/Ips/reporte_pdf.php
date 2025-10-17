<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; margin-top: 0; }
    </style>
</head>
<body>
    <h3>Reporte de Direcciones IP</h3>
    <table>
        <thead>
            <tr>
                <th>Dirección IP</th>
                <th>Estado</th>
                <th>Cód. Patrimonial</th>
                <th>Descripción</th>
                <th>Fecha Asignación</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ips as $ip): ?>
                <tr>
                    <td><?= $ip['direccion_ip'] ?></td>
                    <td><?= $ip['estado'] ?></td>
                    <td><?= $ip['cod_patrimonial'] ?? '-' ?></td>
                    <td><?= $ip['descripcion'] ?? '-' ?></td>
                    <td><?= $ip['fecha_asignacion'] ?? '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
