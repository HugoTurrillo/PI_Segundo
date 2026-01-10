<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id_noticia"])) {
    echo json_encode(["error" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id_noticia"]);

$stmt = $pdo->prepare("SELECT * FROM noticia WHERE id_noticia = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($noticia ?: []);
?>