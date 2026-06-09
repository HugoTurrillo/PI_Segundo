<?php
/**
 * Actualizo una categoría por ID; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"] ?? 0);
$nombre = trim($data["nombre"] ?? "");
$premios = trim($data["premios"] ?? "");
$premio_fisico = trim($data["premio_fisico"] ?? "");

if ($id <= 0 || $nombre === "" || $premios === "" || $premio_fisico === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit;
}

$stmt = $conexion->prepare(
    "UPDATE categorias
     SET nombre=?, premios=?, premio_fisico=?
     WHERE id=?"
);
$stmt->bind_param("sssi", $nombre, $premios, $premio_fisico, $id);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Categoría actualizada"]);
