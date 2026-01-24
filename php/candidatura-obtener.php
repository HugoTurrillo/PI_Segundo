<?php
require "config/conexion.php";
header("Content-Type: application/json");

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "ID inválido"
    ]);
    exit;
}

$stmt = $conexion->prepare("
    SELECT 
        id_candidatura,
        titulo_obra,
        nombre_contacto,
        email_contacto
    FROM candidatura
    WHERE id_candidatura = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "Candidatura no encontrada"
    ]);
    exit;
}

echo json_encode([
    "ok" => true,
    "data" => $res->fetch_assoc()
]);
exit;
