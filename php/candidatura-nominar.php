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

$id_candidatura = intval($data["id_candidatura"] ?? 0);
$id_categoria = intval($data["id_categoria"] ?? 0);

// ============================
// VALIDAR DATOS
// ============================
if ($id_candidatura <= 0 || $id_categoria <= 0) {
    echo json_encode(["ok" => false, "msg" => "Datos inválidos"]);
    exit;
}

// ============================
// ACTUALIZAR CATEGORÍA
// ============================
$stmt = $conexion->prepare("
    UPDATE candidatura 
    SET id_categoria=? 
    WHERE id_candidatura=?
");

$stmt->bind_param("ii", $id_categoria, $id_candidatura);
$stmt->execute();

// ============================
// COMPROBAR SI SE ACTUALIZÓ
// ============================
if ($stmt->affected_rows === 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "No se encontró la candidatura o ya estaba nominada"
    ]);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode(["ok" => true]);
exit;