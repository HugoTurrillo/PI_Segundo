<?php
require_once __DIR__ . "/bootstrap_db.php";

$conexion = new mysqli("localhost", "root", "", "festival_cortos_uem");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
