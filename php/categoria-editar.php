<?php
include("conexion.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"] ?? 0);
$nombre = trim($data["nombre"] ?? "");
$premios = trim($data["premios"] ?? "");
$premio_fisico = trim($data["premio_fisico"] ?? "");

if ($id <= 0 || $nombre === "" || $premios === "" || $premio_fisico === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit();
}

$stmt = $pdo->prepare("UPDATE categorias SET nombre=?, premios=?, premio_fisico=? WHERE id=?");
$stmt->execute([$nombre, $premios, $premio_fisico, $id]);

echo json_encode(["ok" => true, "msg" => "Categoría actualizada"]);
?>