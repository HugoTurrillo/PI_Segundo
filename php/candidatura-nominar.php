<?php
require "config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id_candidatura = intval($data["id_candidatura"] ?? 0);
$id_categoria = intval($data["id_categoria"] ?? 0);

if ($id_candidatura <= 0 || $id_categoria <= 0) {
    echo json_encode(["ok" => false, "msg" => "Datos inválidos"]);
    exit;
}

$stmt = $conexion->prepare(
    "UPDATE candidatura SET id_categoria=? WHERE id_candidatura=?"
);
$stmt->bind_param("ii", $id_categoria, $id_candidatura);
$stmt->execute();

echo json_encode(["ok" => true]);
