<?php
/**
 * Elimino una sección de la gala por ID; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input["id"] ?? 0);

if (!$id) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM gala_secciones WHERE id = ?");
$stmt->bind_param("i", $id);

echo json_encode([
    "ok" => $stmt->execute(),
    "msg" => $stmt->execute() ? "Sección eliminada" : "Error al eliminar sección"
]);
