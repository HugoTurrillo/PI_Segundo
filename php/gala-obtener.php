<?php
// php/gala-obtener.php
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

$id = intval($datos["id"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM gala WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$evento = $resultado->fetch_assoc();

echo json_encode($evento ?: []);
