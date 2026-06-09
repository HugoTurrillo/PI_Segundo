<?php
/**
 * Asigno la categoría a una candidatura (nominación); compruebo que el perfil del usuario coincida con la categoría. Solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id_candidatura = intval($data["id_candidatura"] ?? 0);
$id_categoria   = intval($data["id_categoria"] ?? 0);

if ($id_candidatura <= 0 || $id_categoria <= 0) {
    echo json_encode(["ok" => false, "msg" => "Datos inválidos"]);
    exit;
}

/* ============================
   OBTENER PERFIL DEL USUARIO
============================ */
$stmt = $conexion->prepare("
  SELECT u.rol_participante, cat.nombre AS categoria
  FROM candidatura c
  INNER JOIN usuario u ON u.id_usuario = c.id_usuario
  INNER JOIN categorias cat ON cat.id = ?
  WHERE c.id_candidatura = ?
");
$stmt->bind_param("ii", $id_categoria, $id_candidatura);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "Datos no encontrados"]);
    exit;
}

$dataCheck = $res->fetch_assoc();

/* ============================
   VALIDAR PERFIL ↔ CATEGORÍA
============================ */
$mapa = [
  "alumno"       => "Alumnos",
  "alumni"       => "Alumni",
  "profesional"  => "Profesionales"
];

if (!isset($mapa[$dataCheck["rol_participante"]]) ||
    $mapa[$dataCheck["rol_participante"]] !== $dataCheck["categoria"]) {

    echo json_encode([
      "ok" => false,
      "msg" => "Esta candidatura no puede ser nominada a esa categoría"
    ]);
    exit;
}

/* ============================
   UPDATE SEGURO
============================ */
$stmt = $conexion->prepare(
    "UPDATE candidatura SET id_categoria=? WHERE id_candidatura=?"
);
$stmt->bind_param("ii", $id_categoria, $id_candidatura);
$stmt->execute();

echo json_encode(["ok" => true]);
exit;
