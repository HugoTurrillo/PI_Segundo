<?php
session_start();
//require "conexion.php";
require __DIR__ . "/config/conexion.php";

$id_usuario = $_SESSION["id_usuario"] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "No hay sesión"]);
    exit;
}

$sql = "SELECT c.*, cat.nombre AS categoria_nombre 
        FROM candidatura c
        LEFT JOIN categorias cat ON c.id_categoria = cat.id
        WHERE c.id_usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "No hay candidatura"]);
    exit;
}

$data = $result->fetch_assoc();

echo json_encode([
    "ok" => true,
    "candidatura" => $data
]);
