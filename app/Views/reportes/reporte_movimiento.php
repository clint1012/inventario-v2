<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimiento</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header img { width: 120px; } /* Ajusta el tamaño del logo */
        .header .fecha { font-size: 14px; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2, h3 { margin-bottom: 10px; }
        .firma-container { display: flex; justify-content: space-between; margin-top: 50px; }
        .firma { text-align: center; width: 45%; }
        .firma p { margin-bottom: 40px; border-bottom: 1px solid #000; padding-bottom: 10px; }
        .pie-pagina { font-size: 12px; text-align: center; margin-top: 50px; border-top: 1px solid #000; padding-top: 10px; }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <div class="header">
        <img src="<?= base_url('sb2\img\tc_logo_superior.png') ?>" alt="Logo">
        <div class="fecha"><strong>Fecha:</strong> <?= date('d/m/Y') ?></div>
    </div>

    <h2>Reporte de Movimiento</h2>

    <h3>Datos del Usuario Solicitante</h3>
    <p><strong>Nombre:</strong> <?= esc($usuario) ?></p>
    <p><strong>Unidad Orgánica:</strong> <?= esc($unidad_organica) ?></p>

    <h3>Equipos Instalados</h3>
    <table>
        <thead>
            <tr>
                <th>SBN</th>
                <th>Descripción</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sbn_instalados)): ?>
                <?php foreach ($sbn_instalados as $key => $sbn): ?>
                    <tr>
                        <td><?= esc($sbn) ?></td>
                        <td><?= esc($descripcion_instalados[$key]) ?></td>
                        <td><?= esc($observacion_instalados[$key]) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No hay equipos instalados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Equipos Retirados</h3>
    <table>
        <thead>
            <tr>
                <th>SBN</th>
                <th>Descripción</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sbn_retirados)): ?>
                <?php foreach ($sbn_retirados as $key => $sbn): ?>
                    <tr>
                        <td><?= esc($sbn) ?></td>
                        <td><?= esc($descripcion_retirados[$key]) ?></td>
                        <td><?= esc($observacion_retirados[$key]) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No hay equipos retirados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Observaciones</h3>
    <p><?= esc($observaciones) ?></p>

    <!-- Sección de firmas -->
    <div class="firma-container">
        <div class="firma">
            <p><?= esc($usuario) ?></p>
            <strong>Usuario Solicitante</strong>
        </div>
        <div class="firma">
            <p>______________________</p>
            <strong>Atendido por</strong>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="pie-pagina">
        El usuario acepta haber leído y se sujeta a las disposiciones contenidas en la DIRECTIVA Nº 002 -2016-DIGA/TC "Normas que regulan el Uso de las Tecnologías de Información y Comunicaciones en el Tribunal Constitucional".
    </div>

</body>
</html>
