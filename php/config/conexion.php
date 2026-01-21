<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";


$conexion = new mysqli($host, $user, $pass, $dbname);

if ($conexion->connect_error) {
    die("Error de conexión: " . $e->getMessage());
}
