<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Inventarios</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 9px;
            margin: 0;
            padding: 10px;
        }
        h1 { 
            text-align: center; 
            font-size: 16px;
            margin-bottom: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        th, td { 
            border: 1px solid #333; 
            padding: 4px; 
            text-align: left;
            word-wrap: break-word;
        }
        th { 
            background-color: #e9ecef; 
            font-weight: bold;
            font-size: 8px;
        }
        .usuario-header { 
            background-color: #d1d3d4; 
            font-weight: bold; 
            padding: 8px; 
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 10px;
        }
        .info-adicional {
            font-size: 9px;
            padding: 5px 8px;
            background-color: #f8f9fa;
            margin-bottom: 5px;
        }
        .verificado-si { color: green; font-weight: bold; }
        .verificado-no { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?= base_url('img/logo_principal.png') ?>" alt="Logo" width="200">
        <h1>INVENTARIO DE BIENES PATRIMONIALES</h1>
    </div>
    
    <?php if (empty($inventarios)): ?>
        <p style="text-align: center;">No hay inventarios registrados.</p>
    <?php else: ?>
        <?php foreach ($inventarios as $inv): ?>
            <div class="usuario-header">
                Usuario: <?= esc($inv['usuario'] ?? 'Desconocido') ?> | 
                Periodo: <?= esc($inv['mes'] ?? '—') ?> / <?= esc($inv['anio']) ?> | 
                Total bienes: <?= count($detalles[$inv['id']] ?? []) ?>
            </div>
            
            <?php if (!empty($inv['regimen']) || !empty($inv['jefe'])): ?>
                <div class="info-adicional">
                    <?php if (!empty($inv['regimen'])): ?>
                        Régimen Laboral: <?= esc($inv['regimen']) ?>
                    <?php endif; ?>
                    <?php if (!empty($inv['jefe'])): ?>
                        <?php if (!empty($inv['regimen'])): ?> | <?php endif; ?>
                        Jefe Responsable: <?= esc($inv['jefe']) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($detalles[$inv['id']])): ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 10%;">SBN</th>
                            <th style="width: 25%;">Descripción</th>
                            <th style="width: 12%;">Marca</th>
                            <th style="width: 12%;">Serie</th>
                            <th style="width: 15%;">Local</th>
                            <th style="width: 15%;">Departamento</th>
                            <th style="width: 8%;">Verificado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles[$inv['id']] as $detalle): ?>
                            <tr>
                                <td><?= esc($detalle['cod_patrimonial'] ?? '—') ?></td>
                                <td><?= esc($detalle['descripcion'] ?? '—') ?></td>
                                <td><?= esc($detalle['marca'] ?? '—') ?></td>
                                <td><?= esc($detalle['serie'] ?? '—') ?></td>
                                <td><?= esc($detalle['local'] ?? '—') ?></td>
                                <td><?= esc($detalle['departamento'] ?? '—') ?></td>
                                <td class="<?= $detalle['verificado'] ? 'verificado-si' : 'verificado-no' ?>">
                                    <?= $detalle['verificado'] ? 'SÍ' : 'NO' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="padding: 10px; color: #666;">Sin bienes asignados.</p>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div style="margin-top: 20px; text-align: right; font-size: 8px; color: #666;">
        Generado el <?= date('d/m/Y H:i:s') ?>
    </div>
</body>
</html>