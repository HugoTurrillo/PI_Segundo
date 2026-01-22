<?php
require "config/conexion.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id_noticia"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM noticia WHERE id_noticia = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Noticia eliminada correctamente"]);
exit;
?>