<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Email</title>
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
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
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
        .success-box {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Prueba de Configuración</h1>
            <p>Sistema de Inventario - Tribunal Constitucional</p>
        </div>
        
        <div class="content">
            <div class="success-box">
                <strong>🎉 ¡Éxito!</strong> El sistema de correo electrónico está funcionando correctamente.
            </div>

            <p>Hola <strong><?= esc($nombre) ?></strong>,</p>

            <p>Este es un correo de prueba para confirmar que la configuración de email del sistema está operativa.</p>

            <table class="info-table">
                <tr>
                    <td>Fecha y Hora:</td>
                    <td><?= $fecha ?></td>
                </tr>
                <tr>
                    <td>Sistema:</td>
                    <td>Inventario OTI v2.0</td>
                </tr>
                <tr>
                    <td>Estado:</td>
                    <td><span style="color: #28a745; font-weight: bold;">✓ Operativo</span></td>
                </tr>
            </table>

            <p>Las notificaciones automáticas están listas para funcionar:</p>
            <ul>
                <li>✅ Alertas de licencias próximas a vencer</li>
                <li>✅ Recordatorios de préstamos por devolver</li>
                <li>✅ Notificaciones del sistema</li>
            </ul>

            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                Si recibió este correo, significa que la configuración de email está correcta y puede empezar a usar el sistema de notificaciones.
            </p>
        </div>

        <div class="footer">
            <p><strong>Oficina de Tecnologías de la Información</strong></p>
            <p>Tribunal Constitucional del Perú</p>
            <p style="margin-top: 10px; color: #999; font-size: 11px;">
                Este es un mensaje de prueba del sistema.
            </p>
        </div>
    </div>
</body>
</html>
