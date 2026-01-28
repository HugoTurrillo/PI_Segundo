<?php
require "config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id_candidatura"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare(
    "UPDATE candidatura SET estado='aceptada' WHERE id_candidatura=?"
);
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["ok" => true]);
