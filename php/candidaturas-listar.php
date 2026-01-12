<?php
require "conexion.php";

header("Content-Type: application/json");

$sql = "SELECT * FROM candidatura ORDER BY fecha_creacion DESC";
$stmt = $pdo->query($sql);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
