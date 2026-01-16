<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencias Próximas a Vencer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #c41e3a 0%, #8B1538 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .licencia-item {
            background: #f8f9fa;
            border-left: 4px solid #c41e3a;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .licencia-item h3 {
            margin: 0 0 10px 0;
            color: #c41e3a;
            font-size: 16px;
        }
        .licencia-item p {
            margin: 5px 0;
            font-size: 14px;
        }
        .dias-restantes {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .dias-restantes.warning {
            background: #ffc107;
            color: #333;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #c41e3a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Alerta de Licencias</h1>
            <p>Sistema de Inventario - Tribunal Constitucional</p>
        </div>
        
        <div class="content">
            <div class="alert">
                <strong>📋 Atención:</strong> Las siguientes licencias de software están próximas a vencer en los próximos <?= $diasAviso ?> días.
            </div>

            <h2 style="color: #c41e3a; font-size: 18px;">Licencias que Requieren Atención:</h2>

            <?php foreach ($licencias as $licencia): 
                $fechaExpiracion = new DateTime($licencia['fecha_expiracion']);
                $hoy = new DateTime();
                $diasRestantes = $hoy->diff($fechaExpiracion)->days;
                $claseEstado = $diasRestantes <= 7 ? '' : 'warning';
            ?>
                <div class="licencia-item">
                    <h3><?= esc($licencia['software'] ?? $licencia['nombre_software']) ?></h3>
                    <p><strong>Tipo:</strong> <?= esc($licencia['tipo_licencia']) ?></p>
                    <p><strong>Fecha de Expiración:</strong> <?= date('d/m/Y', strtotime($licencia['fecha_expiracion'])) ?></p>
                    <p><strong>Licencias Disponibles:</strong> <?= esc($licencia['cantidad_licencias'] ?? 'N/A') ?></p>
                    <p>
                        <span class="dias-restantes <?= $claseEstado ?>">
                            <?= $diasRestantes ?> día(s) restantes
                        </span>
                    </p>
                </div>
            <?php endforeach; ?>

            <p style="margin-top: 30px;">
                Se recomienda renovar estas licencias antes de su vencimiento para evitar interrupciones en el servicio.
            </p>

            <div style="text-align: center;">
                <a href="<?= base_url('licencias') ?>" class="btn">Ver Todas las Licencias</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>Oficina de Tecnologías de la Información</strong></p>
            <p>Tribunal Constitucional del Perú</p>
            <p style="margin-top: 10px; color: #999; font-size: 11px;">
                Este es un mensaje automático. Por favor no responder a este correo.
            </p>
        </div>
    </div>
</body>
</html>
