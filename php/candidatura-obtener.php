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
    echo json_encode(["ok" => false, "msg" => "ID inválido"]);
    exit;
}

$stmt = $conexion->prepare(
    "SELECT * FROM candidatura WHERE id_candidatura=?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    echo json_encode(["ok" => true, "data" => $res->fetch_assoc()]);
    exit;
}

echo json_encode(["ok" => false, "msg" => "Candidatura no encontrada"]);
