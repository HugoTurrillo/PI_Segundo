<?php
session_start();
require "config/conexion.php";
header("Content-Type: application/json");

// ============================
// VALIDAR MÉTODO
// ============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

// ============================
// VALIDAR SESIÓN Y ROL
// ============================
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "organizador") {
    echo json_encode(["ok" => false, "msg" => "No autorizado"]);
    exit;
}

// ============================
// LEER JSON
// ============================
$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id_candidatura"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

// ============================
// ACTUALIZAR ESTADO
// ============================
$stmt = $conexion->prepare(
    "UPDATE candidatura SET estado='aceptada' WHERE id_candidatura=?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

// Comprobar si realmente se actualizó
if ($stmt->affected_rows === 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "No se encontró la candidatura o ya estaba aceptada"
    ]);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode(["ok" => true]);
exit;