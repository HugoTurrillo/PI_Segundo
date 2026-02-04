<?php
require_once "conexion.php"; // o el archivo donde creas $conexion

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id_gala'])) {
    echo json_encode([
        "ok" => false,
        "msg" => "Falta id_gala"
    ]);
    exit;
}

$id_gala = (int) $_GET['id_gala'];

$sql = "SELECT id, id_gala, titulo, hora, sala, descripcion 
        FROM gala_secciones 
        WHERE id_gala = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_gala);
$stmt->execute();
$res = $stmt->get_result();

$secciones = [];
while ($row = $res->fetch_assoc()) {
    $secciones[] = $row;
}

echo json_encode([
    "ok" => true,
    "data" => $secciones
]);
