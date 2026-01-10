<?php
include("conexion.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data["nombre"] ?? "");
$premios = trim($data["premios"] ?? "");
$premio_fisico = trim($data["premio_fisico"] ?? "");

if ($nombre === "" || $premios === "" || $premio_fisico === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos son obligatorios"]);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO categorias (nombre, premios, premio_fisico) VALUES (?, ?, ?)");
$stmt->execute([$nombre, $premios, $premio_fisico]);

echo json_encode(["ok" => true, "msg" => "Categoría creada correctamente"]);
?>