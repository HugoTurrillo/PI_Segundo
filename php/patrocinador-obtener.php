<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id"])) {
    echo json_encode(["error" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id"]);

$stmt = $pdo->prepare("SELECT * FROM patrocinador WHERE id_patrocinador = ?");
$stmt->execute([$id]);
$patro = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($patro ?: []);
?>