<?php
require "config/conexion.php";
header("Content-Type: application/json");

session_start();
$id_usuario = $_SESSION["id_usuario"] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "No autenticado"]);
    exit;
}

$sql = "SELECT id_candidatura, titulo_obra, sinopsis, estado, motivo_rechazo, video_ruta
        FROM candidatura
        WHERE id_usuario = ?
        ORDER BY id_candidatura DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => true, "candidatura" => null]);
    exit;
}

echo json_encode([
    "ok" => true,
    "candidatura" => $res->fetch_assoc()
]);
