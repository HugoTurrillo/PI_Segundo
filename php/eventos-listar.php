<?php
/**
 * Listo todos los eventos para el panel del organizador; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$stmt = $conexion->prepare("SELECT * FROM evento ORDER BY fecha ASC, hora ASC");
$stmt->execute();

$resultado = $stmt->get_result();
$eventos = $resultado->fetch_all(MYSQLI_ASSOC);

$stmt->close();

echo json_encode([
    "ok" => true,
    "eventos" => $eventos
]);
exit;
