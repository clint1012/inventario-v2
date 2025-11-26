<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimiento</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
            font-size: 13px;
        }

        h2 {
            text-align: center;
            margin: 0;
            font-size: 16px;
        }

        .header {
            width: 100%;
            border: none;
            margin-bottom: 10px;
        }

        .header td {
            border: none;
            vertical-align: middle;
        }

        .info p {
            margin: 4px 0;
            font-size: 16px;
        }

        .seccion {
            margin-top: 12px;
        }

        .seccion h3 {
            margin: 6px 0;
            font-size: 14px;
        }

        .firmas {
            margin-top: 80px;
            width: 100%;
            border: none;
            text-align: center;
        }

        .firmas td {
            padding: 40px 20px;
            vertical-align: bottom;
        }

        .linea {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
            height: 5px;
        }

        .small {
            font-size: 12px;
            white-space: pre-line;
        }

        .firmas {
            margin-top: 80px;
            width: 100%;
            border: none;
            text-align: center;
        }

        .firmas td {
            width: 50%;
            vertical-align: bottom;
            text-align: center;
            padding: 90px 20px 15px 20px;
            font-size: 13px;
        }

        .linea {
            border-top: 1.5px solid #000;
            width: 70%;
            margin: 0 auto 10px auto;
        }

        .firmas p {
            margin: 0;
            line-height: 1.4;
        }

        .firmas strong {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: bold;
        }


        /* <-- preserva saltos de línea */
    </style>
</head>

<body>

    <img src="<?= base_url('img/logo_principal.png') ?>" width="200">

    <table class="header">
        <tr>

            <td style="text-align: center;">
                <h1>REPORTE DE MOVIMIENTO</h1>
                <h2>OFICINA DE TECNOLOGIA DE INFORMACION</h2>
            </td>
        </tr>
    </table>

    <?php
    if (empty($movimientos)) {
        echo "<p>No hay movimientos para mostrar.</p>";
        exit;
    }

    // Determinar tipo del lote
    $tiposPresentes = array_unique(array_map(fn($m) => $m['tipo_movimiento'], $movimientos));
    $esCambio = count($tiposPresentes) > 1;
    $tipoLote = $esCambio ? 'Cambio' : ucfirst($tiposPresentes[0]);

    // Clasificación
    $asignados = array_filter($movimientos, fn($m) => in_array($m['tipo_movimiento'], ['asignacion', 'prestamo']));
    $retirados = array_filter($movimientos, fn($m) => $m['tipo_movimiento'] === 'retiro');

    // Usuario, área y local según tipo de movimiento del lote
    $usuarioDestino = '-';
    $departamentoDestino = '-';
    $localDestino = '-';

    if ($tipoLote === 'Retiro') {

        // En RETIRO → se usa PERSONA ANTERIOR
        foreach ($movimientos as $m) {
            if ($m['tipo_movimiento'] === 'retiro') {

                // Datos del usuario anterior
                $usuarioDestino = trim(
                    ($m['nombre_anterior'] ?? '') . ' ' .
                    ($m['apep_anterior'] ?? '') . ' ' .
                    ($m['apem_anterior'] ?? '')
                );

                // Departamento anterior
                $departamentoDestino = $m['departamento_anterior'] ?? '-';

                // Local anterior
                $localDestino = $m['local_anterior'] ?? '-';

                break;
            }
        }

    } else {

        // Asignación o préstamo → usuario destino normal
        foreach ($asignados as $m) {
            $usuarioDestino = trim(
                ($m['nombre'] ?? '') . ' ' .
                ($m['ape_paterno'] ?? '') . ' ' .
                ($m['ape_materno'] ?? '')
            );
            $departamentoDestino = $m['departamento'] ?? '-';
            $localDestino = $m['local'] ?? '-';
            break;
        }

        // Si no hubiese datos, tomar el primero
        if ($usuarioDestino === '-') {
            $p = $movimientos[0];
            $usuarioDestino = trim(
                ($p['nombre'] ?? '') . ' ' .
                ($p['ape_paterno'] ?? '') . ' ' .
                ($p['ape_materno'] ?? '')
            );
            $departamentoDestino = $p['departamento'] ?? '-';
            $localDestino = $p['local'] ?? '-';
        }
    }



    // Observaciones únicas con saltos de línea
    $observacionesArr = [];
    foreach ($movimientos as $m) {
        $obs = trim($m['observaciones'] ?? '');
        if ($obs !== '' && !in_array($obs, $observacionesArr)) {
            $observacionesArr[] = $obs;
        }
    }
    $observaciones = empty($observacionesArr) ? '-' : implode("\n", $observacionesArr);

    // Fecha
    $fecha = date('d/m/Y H:i:s', strtotime($movimientos[0]['fecha_movimiento']));
    ?>
    <br>
    <div class="info">
        <p><strong>Tipo de Movimiento:</strong> <?= $tipoLote ?></p>
        <p><strong>Usuario:</strong> <?= $usuarioDestino ?: '-' ?></p>
        <p><strong>Area:</strong> <?= $departamentoDestino ?></p>
        <p><strong>Local:</strong> <?= $localDestino ?></p>
        <p><strong>Fecha:</strong> <?= $fecha ?></p>
    </div>


    <?php if (!empty($asignados)): ?>
        <div class="seccion">
            <h3>Bienes ASIGNADOS (<?= count($asignados) ?>)</h3>
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
                    <?php foreach ($asignados as $m): ?>
                        <tr>
                            <td><?= $m['cod_patrimonial'] ?></td>
                            <td><?= $m['descripcion'] ?></td>
                            <td><?= $m['marca'] ?></td>
                            <td><?= $m['modelo'] ?></td>
                            <td><?= $m['serie'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <br>

    <?php if (!empty($retirados)): ?>
        <div class="seccion">
            <h3>Bienes RETIRADOS (<?= count($retirados) ?>)</h3>
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
                    <?php foreach ($retirados as $m): ?>
                        <tr>
                            <td><?= $m['cod_patrimonial'] ?></td>
                            <td><?= $m['descripcion'] ?></td>
                            <td><?= $m['marca'] ?></td>
                            <td><?= $m['modelo'] ?></td>
                            <td><?= $m['serie'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <br><br>
    <p class="small"><strong>Observaciones:</strong><br><br><?= nl2br(htmlspecialchars($observaciones)) ?></p>

    <table class="firmas">
        <tr>
            <td>
                <div class="linea"></div>
                <p><strong><?= $usuarioDestino ?></strong></p>
                <p style="text-align:left; padding-left:125px;">DNI:</p>
                <p><?= $departamentoDestino ?></p>

            </td>
            <td>
                <div class="linea"></div>
                <p><strong>BORIS HORNA L.</strong></p>
                <p>Especialista Técnico</p>
                <p>Oficina de Tecnología de la Información</p>
            </td>
        </tr>
    </table>
    <br>

    <p>(*) El usuario acepta haber leído y se sujeta a las disposiciones contenidas en la DIRECTIVA Nº 002 -2016-DIGA/TC
        <br>
        "Normas que regulan el Uso de las Tecnologías de Información y Comunicaciones en el Tribunal Constitucional".
    </p>

    
</body>

</html>