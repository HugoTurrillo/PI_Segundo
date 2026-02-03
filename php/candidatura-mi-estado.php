<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["ok" => false]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$stmt = $conexion->prepare("
    SELECT 
        titulo_obra,
        sinopsis,
        estado,
        motivo_rechazo,
        video_ruta,
        portada_ruta
    FROM candidatura
    WHERE id_usuario = ?
    LIMIT 1
");

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
exit;
