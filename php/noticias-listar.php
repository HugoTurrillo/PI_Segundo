<?php
include("conexion.php");
header("Content-Type: application/json");

$stmt = $pdo->query("SELECT * FROM noticia ORDER BY id_noticia DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($noticias);
?>