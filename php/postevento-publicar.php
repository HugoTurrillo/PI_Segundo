<?php
require "config/conexion.php";
header("Content-Type: application/json");

$sqlEdicion = "
  SELECT id_edicion 
  FROM edicion_festival 
  WHERE activa = 1 
  LIMIT 1
";
$res = $conexion->query($sqlEdicion);

if (!$res || $res->num_rows === 0) {
  echo json_encode(["ok" => false, "msg" => "No hay edición activa"]);
  exit;
}

$id_edicion = $res->fetch_assoc()["id_edicion"];

$conexion->query("
  UPDATE post_evento
  SET publicado = 1
  WHERE id_edicion = $id_edicion
");

echo json_encode(["ok" => true]);
