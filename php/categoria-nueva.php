<?php
// php/categoria-nueva.html
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data["nombre"] ?? "");
$premios = trim($data["premios"] ?? "");
$premio_fisico = trim($data["premio_fisico"] ?? "");

if ($nombre === "" || $premios === "" || $premio_fisico === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos son obligatorios"]);
    exit;
}

$stmt = $conexion->prepare(
    "INSERT INTO categorias (nombre, premios, premio_fisico)
     VALUES (?, ?, ?)"
);
$stmt->bind_param("sss", $nombre, $premios, $premio_fisico);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Categoría creada correctamente"]);
