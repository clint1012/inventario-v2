<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha del Proveedor</title>
    <style>
        /* === Tipografía y estilos generales === */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }

        /* === Encabezado === */
        .header {
            text-align: center;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .header img {
            width: 90px;
            margin-bottom: 8px;
        }
        .header h2 {
            color: #0d6efd;
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        /* === Tabla === */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            vertical-align: middle;
            border: 1px solid #ddd;
        }
        th {
            background-color: #0d6efd;
            color: white;
            width: 30%;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* === Footer === */
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <div class="header">
       
        <h2>Ficha del Proveedor</h2>
    </div>

    <!-- Tabla de información -->
    <table>
        <tr>
            <th>Nombre</th>
            <td><?= esc($proveedor['nombre']) ?></td>
        </tr>
        <tr>
            <th>RUC</th>
            <td><?= esc($proveedor['ruc']) ?></td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td><?= esc($proveedor['telefono']) ?></td>
        </tr>
        <tr>
            <th>Correo</th>
            <td><?= esc($proveedor['correo']) ?></td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td><?= esc($proveedor['direccion']) ?></td>
        </tr>
        <tr>
            <th>Giro</th>
            <td><?= esc($proveedor['giro']) ?></td>
        </tr>
        <tr>
            <th>Estado</th>
            <td><?= esc(ucfirst($proveedor['estado'])) ?></td>
        </tr>
        <tr>
            <th>Fecha de Registro</th>
            <td><?= esc($proveedor['created_at'] ?? 'N/A') ?></td>
        </tr>
    </table>

    <!-- Pie de página -->
    <div class="footer">
        <hr>
        <p>Generado automáticamente por el Sistema de Inventario — <?= date('d/m/Y H:i') ?></p>
    </div>

</body>
</html>
