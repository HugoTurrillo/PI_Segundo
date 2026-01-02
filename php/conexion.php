<?php
// php/conexion.php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

try {
    // Conexión a la base de datos del festival
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    // En producción se debería registrar el error en un log
    die("Error de conexión: " . $e->getMessage());
}
