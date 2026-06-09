<?php
/**
 * Listo las imágenes de la galería de un postevento por id_post_evento; solo organizador.
 */

require_once __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$response = ["ok" => false];

if (!isset($_GET["id_post_evento"])) {
    $response["msg"] = "Falta el ID del post‑evento.";
    echo json_encode($response);
    exit;
}

$id_post_evento = intval($_GET["id_post_evento"]);

$sql = $conexion->prepare("
    SELECT id_imagen, ruta_imagen
    FROM post_evento_imagen
    WHERE id_post_evento = ?
");

$sql->bind_param("i", $id_post_evento);
$sql->execute();
$result = $sql->get_result();

$imagenes = [];

while ($row = $result->fetch_assoc()) {
    $imagenes[] = $row;
}

$response["ok"] = true;
$response["data"] = $imagenes;

echo json_encode($response);
