<?php
include("conexion.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"] ?? 0);
$titulo = trim($data["titulo"] ?? "");
$contenido = trim($data["contenido"] ?? "");

if ($id <= 0 || $titulo === "" || $contenido === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit();
}

$stmt = $pdo->prepare("UPDATE noticias SET titulo=?, contenido=? WHERE id=?");
$stmt->execute([$titulo, $contenido, $id]);

echo json_encode(["ok" => true, "msg" => "Noticia actualizada"]);
?>