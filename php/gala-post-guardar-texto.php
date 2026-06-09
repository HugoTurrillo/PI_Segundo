<?php
/**
 * Guardo el texto de una sección del postevento (resumen, etc.) para la edición activa; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json; charset=utf-8");
requireApiOrganizer();

$input = json_decode(file_get_contents("php://input"), true);

$texto = trim($input["texto"] ?? "");

if ($texto === "") {
    echo json_encode([
        "ok" => false,
        "msg" => "El texto no puede estar vacío"
    ]);
    exit;
}

// Obtener post_evento activo (el último o el de la edición activa)
$sql = "
  SELECT pe.id_post_evento
  FROM post_evento pe
  JOIN edicion_festival e ON e.id_edicion = pe.id_edicion
  WHERE e.activa = 1
  LIMIT 1
";

$res = $conexion->query($sql);

if (!$res || $res->num_rows === 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "No existe ningún post-evento"
    ]);
    exit;
}

$id_post_evento = $res->fetch_assoc()["id_post_evento"];

// Actualizar texto
$stmt = $conexion->prepare("
  UPDATE post_evento
  SET resumen = ?
  WHERE id_post_evento = ?
");

$stmt->bind_param("si", $texto, $id_post_evento);

if ($stmt->execute()) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode([
        "ok" => false,
        "msg" => "Error al guardar el texto"
    ]);
}
