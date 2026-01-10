<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id"])) {
    echo json_encode(["ok" => false, "msg" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id"]);

$stmt = $pdo->prepare("SELECT logo FROM patrocinadores WHERE id = ?");
$stmt->execute([$id]);
$patro = $stmt->fetch(PDO::FETCH_ASSOC);

if ($patro && file_exists("../uploads/" . $patro["logo"])) {
    unlink("../uploads/" . $patro["logo"]);
}

$stmt = $pdo->prepare("DELETE FROM patrocinadores WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(["ok" => true, "msg" => "Patrocinador eliminado"]);
?>