<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id"])) {
    echo json_encode(["ok" => false, "msg" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id"]);

// Eliminar
$stmt = $pdo->prepare("DELETE FROM evento WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(["ok" => true, "msg" => "Evento eliminado correctamente"]);
?>