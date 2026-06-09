<?php
/**
 * Conexión a la base de datos del festival.
 * Incluyo el bootstrap para asegurarme de que las tablas existan y creo la conexión MySQLi.
 */

require_once __DIR__ . "/bootstrap_db.php";

$conexion = new mysqli("localhost", "root", "", "festival_cortos_uem");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
