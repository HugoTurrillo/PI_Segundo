<?php
include("conexion.php");
header("Content-Type: application/json");

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["ok" => false, "msg" => "No se recibieron datos"]);
    exit();
}

$titulo = trim($data["titulo"] ?? "");
$fecha = trim($data["fecha"] ?? "");
$descripcion = trim($data["descripcion"] ?? "");

// Validaciones backend
if ($titulo === "" || $fecha === "" || $descripcion === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos son obligatorios"]);
    exit();
}

// Insertar en BD
$stmt = $pdo->prepare("INSERT INTO evento (titulo, fecha, descripcion) VALUES (?, ?, ?)");
$stmt->execute([$titulo, $fecha, $descripcion]);

echo json_encode(["ok" => true, "msg" => "Evento creado correctamente"]);
?>