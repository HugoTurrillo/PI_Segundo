<?php
require "config/conexion.php";
header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];
$id = 0;

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data["id_noticia"] ?? 0);
} elseif ($method === "GET") {
    // compatibilidad por si algo viejo queda
    $id = intval($_GET["id_noticia"] ?? 0);
} else {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM noticia WHERE id_noticia=?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Noticia eliminada correctamente"]);
