<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

$id = intval($_GET["id_categoria"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "error" => "ID de categoría no recibido"]);
    exit;
}

// Candidaturas ya nominadas a esta categoría
$stmt = $conexion->prepare("
    SELECT id_candidatura, titulo_obra, nombre_contacto, 0 AS sin_categoria
    FROM candidatura
    WHERE id_categoria = ? AND estado = 'aceptada'
");
$stmt->bind_param("i", $id);
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
