<?php
require "config/conexion.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id_noticia"] ?? 0);
$titulo = trim($data["titulo"] ?? "");
$contenido = trim($data["contenido"] ?? "");

if ($id <= 0 || $titulo === "" || $contenido === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit;
}

$stmt = $conexion->prepare("UPDATE noticia SET titulo=?, contenido=? WHERE id_noticia=?");
$stmt->bind_param("ssi", $titulo, $contenido, $id);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Noticia actualizada"]);
exit;
?>