<?php
include("conexion.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"] ?? 0);
$titulo = trim($data["titulo"] ?? "");
$fecha = trim($data["fecha"] ?? "");
$descripcion = trim($data["descripcion"] ?? "");

if ($id <= 0 || $titulo === "" || $fecha === "" || $descripcion === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit();
}

$stmt = $pdo->prepare("UPDATE evento SET titulo=?, fecha=?, descripcion=? WHERE id=?");
$stmt->execute([$titulo, $fecha, $descripcion, $id]);

echo json_encode(["ok" => true, "msg" => "Evento actualizado"]);
?>