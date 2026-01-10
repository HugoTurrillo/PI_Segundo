<?php
include("conexion.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$titulo = trim($data["titulo"] ?? "");
$contenido = trim($data["contenido"] ?? "");

if ($titulo === "" || $contenido === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos son obligatorios"]);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO noticia (titulo, contenido) VALUES (?, ?)");
$stmt->execute([$titulo, $contenido]);

echo json_encode(["ok" => true, "msg" => "Noticia creada correctamente"]);
?>