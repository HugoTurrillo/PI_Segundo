<?php
/**
 * Devuelvo un ganador por ID para rellenar el formulario de edición; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$id = intval($_GET["id"] ?? 0);

$stmt = $conexion->prepare("
  SELECT
    g.*,
    c.nombre AS categoria,
    cand.titulo_obra,
    cand.nombre_contacto
  FROM ganadores g
  INNER JOIN categorias c ON c.id = g.id_categoria
  INNER JOIN candidatura cand ON cand.id_candidatura = g.id_candidatura
  WHERE g.id_ganador = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["ok" => false, "error" => "Ganador no encontrado"]);
  exit;
}

echo json_encode([
  "ok" => true,
  "data" => $res->fetch_assoc()
]);
