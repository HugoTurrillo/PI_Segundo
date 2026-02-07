<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json; charset=utf-8");

$stmt = $conexion->prepare("
    SELECT id, nombre
    FROM categorias
    WHERE nombre IN ('alumnos', 'alumni')
    ORDER BY nombre ASC
");
$stmt->execute();
$res = $stmt->get_result();

echo json_encode([
    "ok" => true,
    "data" => $res->fetch_all(MYSQLI_ASSOC)
]);