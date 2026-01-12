<?php
require "conexion.php";
header("Content-Type: application/json");

$id = $_GET["id"] ?? null;

if (!$id) {
    echo json_encode(["ok" => false, "error" => "ID no proporcionado"]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM candidatura WHERE id_candidatura = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo json_encode(["ok" => false, "error" => "Candidatura no encontrada"]);
    exit;
}

echo json_encode(["ok" => true, "data" => $data]);
