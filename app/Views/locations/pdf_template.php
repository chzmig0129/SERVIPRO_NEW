<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Plantas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .fecha {
            text-align: right;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Plantas</h1>
    </div>
    
    <div class="fecha">
        Fecha de generación: <?= $fecha ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Ciudad</th>
                <th>País</th>
                <th>Fecha Creación</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sedes as $sede): ?>
            <tr>
                <td><?= $sede['id'] ?></td>
                <td><?= $sede['nombre'] ?></td>
                <td><?= $sede['direccion'] ?></td>
                <td><?= $sede['ciudad'] ?></td>
                <td><?= $sede['pais'] ?></td>
                <td><?= $sede['fecha_creacion'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Este es un documento generado automáticamente.</p>
    </div>
</body>
</html> 