<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

$stmt = $conexion->prepare("SELECT * FROM evento ORDER BY fecha ASC, hora ASC");
$stmt->execute();

$resultado = $stmt->get_result();
$eventos = $resultado->fetch_all(MYSQLI_ASSOC);

$stmt->close();

echo json_encode([
    "ok" => true,
    "eventos" => $eventos
]);
exit;
