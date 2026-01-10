<?php
include("conexion.php");
header("Content-Type: application/json");

$stmt = $pdo->query("SELECT * FROM patrocinadores ORDER BY id DESC");
$patrocinadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($patrocinadores);
?>