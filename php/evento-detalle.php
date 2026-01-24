<?php
require "config/conexion.php";

header("Content-Type: application/json");

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "mensaje" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("
    SELECT id, titulo, fecha, descripcion
    FROM evento
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Evento no encontrado"]);
    exit;
}

echo json_encode([
    "ok" => true,
    "evento" => $resultado->fetch_assoc()
]);
exit;
