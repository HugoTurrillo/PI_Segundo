<?php
/**
 * Devuelvo las candidaturas aceptadas para elegir ganador: las ya nominadas a la categoría y las sin categoría (para asignarlas aquí).
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$id = intval($_GET["id_categoria"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "error" => "ID de categoría no recibido"]);
    exit;
}

// Candidaturas ya nominadas a esta categoría (incluyo premio si ya es ganador)
$stmt = $conexion->prepare("
    SELECT
        c.id_candidatura,
        c.titulo_obra,
        c.nombre_contacto,
        0 AS sin_categoria,
        g.numero_premio
    FROM candidatura c
    LEFT JOIN ganadores g
        ON g.id_candidatura = c.id_candidatura AND g.id_categoria = ?
    WHERE c.id_categoria = ? AND c.estado = 'aceptada'
");
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$res = $stmt->get_result();
$nominados = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Candidaturas aceptadas sin categoría (el organizador puede nominarlas aquí)
$stmt2 = $conexion->prepare("
    SELECT id_candidatura, titulo_obra, nombre_contacto, 1 AS sin_categoria
    FROM candidatura
    WHERE (id_categoria IS NULL OR id_categoria = 0) AND estado = 'aceptada'
");
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($row = $res2->fetch_assoc()) {
    $row["sin_categoria"] = 1;
    $nominados[] = $row;
}
$stmt2->close();

echo json_encode([
    "ok" => true,
    "data" => $nominados
]);
