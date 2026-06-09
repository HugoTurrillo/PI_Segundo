<?php
/**
 * Actualizo una sección de la gala por ID; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$input = json_decode(file_get_contents("php://input"), true);

$id = intval($input["id"] ?? 0);
$titulo = $input["titulo"] ?? "";
$hora = $input["hora"] ?? "";
$sala = $input["sala"] ?? "";
$descripcion = $input["descripcion"] ?? "";

if (!$id || !$titulo || !$hora || !$sala) {
    echo json_encode(["ok" => false, "msg" => "Faltan datos"]);
    exit;
}

$stmt = $conexion->prepare("UPDATE gala_secciones SET titulo=?, hora=?, sala=?, descripcion=? WHERE id=?");
$stmt->bind_param("ssssi", $titulo, $hora, $sala, $descripcion, $id);

echo json_encode([
    "ok" => $stmt->execute(),
    "msg" => $stmt->execute() ? "Sección actualizada" : "Error al actualizar sección"
]);
