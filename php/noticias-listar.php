<?php
include("conexion.php");
header("Content-Type: application/json");

$stmt = $pdo->query("SELECT * FROM noticias ORDER BY id DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($noticias);
?>