<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id_noticia"])) {
    echo json_encode(["ok" => false, "msg" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id_noticia"]);

$stmt = $pdo->prepare("DELETE FROM noticia WHERE id_noticia = ?");
$stmt->execute([$id]);

echo json_encode(["ok" => true, "msg" => "Noticia eliminada correctamente"]);
?>
