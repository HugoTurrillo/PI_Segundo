<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

// ============================
// VALIDAR SESIÓN Y ROL
// ============================
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "participante") {
    echo json_encode([
        "ok" => false,
        "msg" => "No autenticado o no autorizado"
    ]);
    exit;
}

// ============================
// VALIDAR MÉTODO
// ============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "ok" => false,
        "msg" => "Método no permitido"
    ]);
    exit;
}

// ============================
// LEER JSON
// ============================
$data = json_decode(file_get_contents("php://input"), true);
$mensaje = trim($data["mensaje"] ?? "");

// ============================
// VALIDAR MENSAJE
// ============================
if ($mensaje === "") {
    echo json_encode([
        "ok" => false,
        "msg" => "El mensaje de subsanación es obligatorio"
    ]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

// ============================
// ACTUALIZAR CANDIDATURA
// ============================
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

// ============================
// COMPROBAR SI SE ACTUALIZÓ
// ============================
if ($stmt->affected_rows === 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "No hay candidatura rechazada para subsanar"
    ]);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode([
    "ok" => true,
    "msg" => "Subsanación enviada correctamente"
]);
exit;