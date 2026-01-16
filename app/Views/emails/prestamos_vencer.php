<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos Próximos a Vencer</title>
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
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .prestamo-item {
            background: #f8f9fa;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .prestamo-item h3 {
            margin: 0 0 10px 0;
            color: #17a2b8;
            font-size: 16px;
        }
        .prestamo-item p {
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
            background: #17a2b8;
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
            <h1>⏰ Recordatorio de Devolución</h1>
            <p>Sistema de Inventario - Tribunal Constitucional</p>
        </div>
        
        <div class="content">
            <p>Estimado(a) <strong><?= esc($nombre) ?></strong>,</p>

            <div class="alert">
                <strong>📦 Importante:</strong> Tiene préstamos de equipos que deben ser devueltos próximamente.
            </div>

            <h2 style="color: #17a2b8; font-size: 18px;">Detalle de Préstamos:</h2>

            <?php foreach ($prestamos as $prestamo): 
                $fechaLimite = new DateTime($prestamo['fecha_limite']);
                $hoy = new DateTime();
                $diasRestantes = $hoy->diff($fechaLimite)->days;
            ?>
                <div class="prestamo-item">
                    <h3>Lote: <?= esc($prestamo['lote']) ?></h3>
                    <p><strong>Fecha Límite de Devolución:</strong> <?= date('d/m/Y', strtotime($prestamo['fecha_limite'])) ?></p>
                    <p><strong>Fecha de Préstamo:</strong> <?= date('d/m/Y', strtotime($prestamo['fecha_movimiento'])) ?></p>
                    <?php if (!empty($prestamo['observaciones'])): ?>
                        <p><strong>Observaciones:</strong> <?= esc($prestamo['observaciones']) ?></p>
                    <?php endif; ?>
                    <p>
                        <span class="dias-restantes">
                            <?= $diasRestantes ?> día(s) restantes
                        </span>
                    </p>
                </div>
            <?php endforeach; ?>

            <p style="margin-top: 30px;">
                Por favor, coordine la devolución de los equipos antes de la fecha límite. En caso de requerir una extensión, 
                contacte con la Oficina de Tecnologías de la Información.
            </p>

            <div style="text-align: center;">
                <a href="<?= base_url('movimientos') ?>" class="btn">Ver Mis Préstamos</a>
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
