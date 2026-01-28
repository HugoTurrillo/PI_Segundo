<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "ok" => false,
        "msg" => "No autenticado"
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "ok" => false,
        "msg" => "Método no permitido"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$mensaje = trim($data["mensaje"] ?? "");

if ($mensaje === "") {
    echo json_encode([
        "ok" => false,
        "msg" => "El mensaje de subsanación es obligatorio"
    ]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$stmt = $conexion->prepare("
    UPDATE candidatura
    SET 
        mensaje_subsanacion = ?,
        estado = 'en_proceso',
        motivo_rechazo = NULL
    WHERE id_usuario = ?
      AND estado = 'rechazada'
");

$stmt->bind_param("si", $mensaje, $id_usuario);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "No hay candidatura rechazada para subsanar"
    ]);
    exit;
}

echo json_encode([
    "ok" => true,
    "msg" => "Subsanación enviada correctamente"
]);
exit;
