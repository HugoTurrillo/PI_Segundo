<?php
include("conexion.php");
header("Content-Type: application/json");

$stmt = $pdo->query("SELECT * FROM eventos ORDER BY fecha ASC");
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($eventos);
?>