<?php
$host = "localhost";
$user = "root";
$pass = "Cesar_2006";
$db   = "tablap";

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
