<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asignación</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { width: 80px; position: absolute; top: 10px; left: 10px; }
        .header h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<div class="header">
    <img src="<?= base_url('sb2/img/tc_logo_superior.png') ?>" alt="">
    <h2>Reporte de Asignación</h2>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre Completo</th>
            <th>Departamento</th>
            <th>Sede</th>
            <th>Máquinas Asignadas</th>
            <th>Fecha de Creación</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach ($inventario as $item) : ?>
        <tr>
            <td><?= $i++; ?></td>
            <td><?= $item['nombre_completo']; ?></td>
            <td><?= $item['departamento']; ?></td>
            <td><?= $item['sede']; ?></td>
            <td><?= $item['maquinas_asignadas']; ?></td>
            <td><?= date('d/m/Y', strtotime($item['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>