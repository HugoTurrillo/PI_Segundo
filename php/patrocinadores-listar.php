<?php
include("conexion.php");
header("Content-Type: application/json");

// Obtener todos los patrocinadores
$stmt = $pdo->query("SELECT * FROM patrocinador ORDER BY id_patrocinador DESC");
$patrocinadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver lista
echo json_encode($patrocinadores);
?>