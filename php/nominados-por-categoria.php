<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id_categoria"])) {
    echo json_encode(["ok" => false, "error" => "ID de categoría no recibido"]);
    exit();
}

$id = intval($_GET["id_categoria"]);

$stmt = $pdo->prepare("
    SELECT id_candidatura, titulo_obra, nombre_contacto
    FROM candidatura
    WHERE id_categoria = ? AND estado = 'aceptada'
");
$stmt->execute([$id]);
$nominados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "ok" => true,
    "data" => $nominados
]);
