<?php
/**
 * Creo una sección de la gala (titulo, hora, sala, descripción); solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$input = json_decode(file_get_contents("php://input"), true);

$id_gala = intval($input["id_gala"] ?? 0);
$titulo = $input["titulo"] ?? "";
$hora = $input["hora"] ?? "";
$sala = $input["sala"] ?? "";
$descripcion = $input["descripcion"] ?? "";

if (!$id_gala || !$titulo || !$hora || !$sala) {
    echo json_encode(["ok" => false, "msg" => "Faltan datos"]);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO gala_secciones (id_gala, titulo, hora, sala, descripcion) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $id_gala, $titulo, $hora, $sala, $descripcion);

echo json_encode([
    "ok" => $stmt->execute(),
    "msg" => $stmt->execute() ? "Sección creada" : "Error al crear sección"
]);
