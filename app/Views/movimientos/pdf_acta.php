<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Movimiento</title>
    <style>

        
        
        h2 { text-align: center; margin-bottom: 10px; }
        p { margin: 4px 0; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .firmas {
            width: 100%;
            margin-top: 60px;
            text-align: center;
        }
        .firmas td {
            width: 50%;
            padding-top: 50px;
        }

        .nota {
            font-size: 11px;
            margin-top: 30px;
        }

        .fecha {
            text-align: right;
            font-style: italic;
            margin-top: 25px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>ACTA DE <?= strtoupper($tipo ?? 'MOVIMIENTO') ?></h2>
    </div>
    
     <br>
    <br>

    <p><strong>Fecha de emisión:</strong> <?= esc($fecha_mov ?? '') ?></p>
    <p><strong>Usuario actual:</strong> <?= esc(($persona['nombre'] ?? '') . ' ' . ($persona['ape_paterno'] ?? '') . ' ' . ($persona['ape_materno'] ?? '')) ?></p>

    <?php 
        // ✅ Tomar los datos del primer bien (referencia)
        $departamento = $bienes[0]['departamento'] ?? 'No especificado';
        $local = $bienes[0]['local'] ?? 'No especificado';
        $ultimo_duenio = trim(($bienes[0]['nombre_anterior'] ?? '') . ' ' . ($bienes[0]['apep_anterior'] ?? '') . ' ' . ($bienes[0]['apem_anterior'] ?? ''));
        if ($ultimo_duenio === '') $ultimo_duenio = 'No registrado';
        
    ?>

    <p><strong>Departamento:</strong> <?= esc($departamento) ?></p>
    <p><strong>Local:</strong> <?= esc($local) ?></p>
    <!-- <p><strong>Último responsable anterior:</strong> <?= esc($ultimo_responsable) ?></p> -->
 
    <br> <br>
    
    <?php if (!empty($bienes)): ?>
        <table>
            <thead>
                <tr>
                    <th>Código Patrimonial</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Serie</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bienes as $b): ?>
                    <tr>
                        <td><?= esc($b['cod_patrimonial']) ?></td>
                        <td><?= esc($b['descripcion']) ?></td>
                        <td><?= esc($b['marca']) ?></td>
                        <td><?= esc($b['modelo']) ?></td>
                        <td><?= esc($b['serie']) ?></td>
                        <td><?= esc(ucfirst($b['estado'] ?? '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><em>No hay bienes registrados en este movimiento.</em></p>
    <?php endif; ?>

    <br>

    <p><strong>Observaciones:</strong><br> <?= nl2br($observaciones) ?></p>

    <table class="firmas">
        <tr>
            <td>__________________________<br><?= esc(($persona['nombre'] ?? '') . ' ' . ($persona['ape_paterno'] ?? '') . ' ' . ($persona['ape_materno'] ?? '')) ?></td>
            <td>__________________________<br>Técnico de Soporte</td>
        </tr>
    </table>

    <p class="nota">
        (*) El usuario acepta haber leído y se sujeta a las disposiciones contenidas en la DIRECTIVA Nº 002-2016-DIGA/TC 
        “Normas que regulan el Uso de las Tecnologías de Información y Comunicaciones en el Tribunal Constitucional”.
    </p>

    <p class="fecha">Lima, <?= $fecha_mov ?></p>

</body>
</html>
