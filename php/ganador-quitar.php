<?php
/**
 * Quito un ganador de un premio (el puesto queda vacío).
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$id_ganador = intval($_POST["id_ganador"] ?? 0);

if ($id_ganador <= 0) {
    echo json_encode(["ok" => false, "error" => "Ganador no indicado"]);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM ganadores WHERE id_ganador = ?");
$stmt->bind_param("i", $id_ganador);

if (!$stmt->execute() || $stmt->affected_rows === 0) {
    echo json_encode(["ok" => false, "error" => "No se pudo quitar el ganador"]);
    exit;
}

echo json_encode([
    "ok"  => true,
    "msg" => "Ganador quitado del podio",
]);
