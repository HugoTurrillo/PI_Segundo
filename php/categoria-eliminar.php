<?php
// php/categoria-eliminar.php
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM categorias WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Categoría eliminada correctamente"]);
