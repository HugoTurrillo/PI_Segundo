<?php
/**
 * Devuelvo el postevento publicado de la edición activa para la página pública.
 */

require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

$sql = "
SELECT *
FROM post_evento pe
JOIN edicion_festival e ON e.id_edicion = pe.id_edicion
WHERE e.activa = 1 AND pe.publicado = 1
LIMIT 1
";

$res = $conexion->query($sql);

if ($res && $res->num_rows > 0) {
  echo json_encode(["ok" => true, "data" => $res->fetch_assoc()]);
} else {
  echo json_encode(["ok" => false]);
}
