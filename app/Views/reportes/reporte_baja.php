<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Bienes Retirados</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; }

        /* Anchos fijos (ajusta según necesites) */
        th:nth-child(1), td:nth-child(1) { width: 12%; }  /* Código patrimonial */
        th:nth-child(2), td:nth-child(2) { width: 15%; }  /* Descripción */
     s3|   th:nth-child(3), td:nth-child(3) { width: 10%; }  /* Marca */
        th:nth-child(4), td:nth-child(4) { width: 10%; }  /* Modelo */
        th:nth-child(5), td:nth-child(5) { width: 6%; }   /* Estado */
        th:nth-child(6), td:nth-child(6) { width: 10%; }  /* Fecha de compra */
        th:nth-child(7), td:nth-child(7) { width: 8%; }   /* Estado garantía */
        th:nth-child(8), td:nth-child(8) { width: 8%; }   /* Proveedor */
        th:nth-child(9), td:nth-child(9) { width: 11%; }  /* Motivo de baja (máx) */

        /* Estilos para la columna motivo de baja: ajuste de línea para no expandir la página */
        td.motivo { white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere; max-width: 400px; }

        /* Para impresiones/PDF: evitar romper el contenido del motivo en una mitad de página cuando sea posible */
        @media print {
            td.motivo { -webkit-column-break-inside: avoid; page-break-inside: avoid; break-inside: avoid; }
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Reporte de Bienes Retirados</h2>
    <table>
        <thead>
            <tr>
                <th>Código Patrimonial</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Estado</th>
                <th>Fecha de Compra</th>
                <th>Estado de Garantía</th>
                <th>Proveedor</th>
                <th>Motivo de baja</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bienes as $bien) : ?>
                <tr>
                    <td><?= esc($bien['cod_patrimonial']) ?></td>
                    <td><?= esc($bien['descripcion']) ?></td>
                    <td><?= esc($bien['marca']) ?></td>
                    <td><?= esc($bien['modelo']) ?></td>
                    <td><?= esc($bien['estado']) ?></td>
                    <td><?= esc($bien['fecha_adquisicion']) ?></td>
                    <td><?= esc($bien['estado_garantia']) ?></td>
                    <td><?= esc($bien['proveedor_id']) ?></td>
                    <td class="motivo"><?= nl2br(esc($bien['motivo_baja'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>