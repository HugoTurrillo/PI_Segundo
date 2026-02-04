<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

$id = intval($_GET["id"] ?? 0);

$stmt = $conexion->prepare("
  SELECT *
  FROM ganadores
  WHERE id_ganador = ?
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
