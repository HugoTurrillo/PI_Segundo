<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id"])) {
    echo json_encode([
        "ok" => false,
        "error" => "ID no recibido"
    ]);
    exit();
}

$id = intval($_GET["id"]);

$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$id]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    echo json_encode([
        "ok" => false,
        "error" => "Categoría no encontrada"
    ]);
    exit();
}

echo json_encode([
    "ok" => true,
    "data" => $categoria
]);
