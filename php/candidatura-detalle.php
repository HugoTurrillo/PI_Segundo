<?php
require "config/conexion.php";
header("Content-Type: application/json");

if (!isset($_GET["id"])) {
    echo json_encode(["ok" => false]);
    exit;
}

$id = intval($_GET["id"]);

$stmt = $conexion->prepare("
    SELECT 
        id_candidatura,
        titulo_obra,
        ficha_tecnica,
        cartel,
        expediente,
        video,
        sinopsis,
        nombre_contacto,
        email_contacto,
        dni,
        estado,
        motivo_rechazo,
        fecha_creacion
    FROM candidatura
    WHERE id_candidatura = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false]);
    exit;
}

echo json_encode([
    "ok" => true,
    "candidatura" => $res->fetch_assoc()
]);