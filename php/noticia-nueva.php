<?php
require "config/conexion.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$titulo = trim($data["titulo"] ?? "");
$contenido = trim($data["contenido"] ?? "");

if ($titulo === "" || $contenido === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos son obligatorios"]);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO noticia (titulo, contenido) VALUES (?, ?)");
$stmt->bind_param("ss", $titulo, $contenido);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Noticia creada correctamente"]);
exit;
?>