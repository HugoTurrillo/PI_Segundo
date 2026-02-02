<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

$id = intval($_GET["id_categoria"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "error" => "ID de categoría no recibido"]);
    exit;
}

$stmt = $conexion->prepare("
    SELECT id_candidatura, titulo_obra, nombre_contacto
    FROM candidatura
    WHERE id_categoria = ? AND estado = 'aceptada'
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

echo json_encode([
    "ok" => true,
    "data" => $res->fetch_all(MYSQLI_ASSOC)
]);
