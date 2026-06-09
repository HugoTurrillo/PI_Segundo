<?php
/**
 * Devuelvo el detalle de un evento por ID para el modal del calendario; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "mensaje" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("
    SELECT id, titulo, fecha, descripcion
    FROM evento
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Evento no encontrado"]);
    exit;
}

echo json_encode([
    "ok" => true,
    "evento" => $resultado->fetch_assoc()
]);
exit;
