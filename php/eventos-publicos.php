<?php
require __DIR__ . "/config/conexion.php";

header("Content-Type: application/json");

$sql = "SELECT id, titulo, fecha, descripcion FROM evento ORDER BY fecha ASC";
$result = $conexion->query($sql);

$eventos = [];

while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
}

echo json_encode([
    "ok" => true,
    "eventos" => $eventos
]);
exit;
