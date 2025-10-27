<?php
include 'db.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT e.*, f.nombre AS familia, p.numero AS periodo 
        FROM elementos e
        LEFT JOIN familias f ON e.familia_id = f.id
        LEFT JOIN periodos p ON e.periodo_id = p.id
        WHERE e.id = $id";

$result = $conn->query($sql);
$elemento = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $elemento['nombre'] ?></title>
</head>
<body>
    <h1><?= $elemento['nombre'] ?> (<?= $elemento['simbolo'] ?>)</h1>
    <ul>
        <li>Número atómico: <?= $elemento['numero_atomico'] ?></li>
        <li>Masa atómica: <?= $elemento['masa_atomica'] ?></li>
        <li>Electronegatividad: <?= $elemento['electronegatividad'] ?></li>
        <li>Familia: <?= $elemento['familia'] ?></li>
        <li>Periodo: <?= $elemento['periodo'] ?></li>
        <li>Estado de oxidación: <?= $elemento['estado_oxidacion'] ?></li>
        <li>Configuración: <?= $elemento['configuracion_corta'] ?></li>
        <li>Punto de fusión: <?= $elemento['punto_fusion'] ?> °C</li>
        <li>Punto de ebullición: <?= $elemento['punto_ebullicion'] ?> °C</li>
        <li>Descripción: <?= $elemento['descripcion'] ?></li>
    </ul>
    <a href="index.php">Volver</a>
</body>
</html>
