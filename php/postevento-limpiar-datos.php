<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

$conexion = new mysqli($host, $user, $pass, $dbname);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

$sql = "
    DELETE FROM candidatura
    WHERE fecha_creacion < (NOW() - INTERVAL 1 MONTH)
      AND estado <> 'aceptada'
";

$conexion->query($sql);

echo "Limpieza de datos completada.";

$conexion->close();
