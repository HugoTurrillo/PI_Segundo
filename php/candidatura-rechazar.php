<?php
require "config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id"] ?? 0);
$motivo = trim($data["motivo"] ?? "");

if ($id <= 0 || $motivo === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incorrectos"]);
    exit;
}

$stmt = $conexion->prepare(
    "UPDATE candidatura
     SET estado='rechazada', motivo_rechazo=?
     WHERE id_candidatura=?"
);
$stmt->bind_param("si", $motivo, $id);
$stmt->execute();

echo json_encode(["ok" => true]);
