<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id"])) {
    echo json_encode(["ok" => false, "msg" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id"]);

$stmt = $pdo->prepare("SELECT imagen FROM gala WHERE id = ?");
$stmt->execute([$id]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if ($evento && file_exists("../uploads/" . $evento["imagen"])) {
    unlink("../uploads/" . $evento["imagen"]);
}

$stmt = $pdo->prepare("DELETE FROM gala WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(["ok" => true, "msg" => "Evento eliminado"]);
?>