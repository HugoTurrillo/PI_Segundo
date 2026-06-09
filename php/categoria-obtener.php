<?php
/**
 * Devuelvo una categoría por ID para el formulario de edición; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("
    SELECT id, nombre, premios, premio_fisico
    FROM categorias
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    echo json_encode([
        "ok" => true,
        "data" => $res->fetch_assoc()
    ]);
    exit;
}

echo json_encode(["ok" => false, "msg" => "Categoría no encontrada"]);
