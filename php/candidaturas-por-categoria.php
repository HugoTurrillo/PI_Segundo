<?php
include("conexion.php");
header("Content-Type: application/json");

$id_categoria = $_GET["id_categoria"];

$stmt = $pdo->prepare("SELECT * FROM candidatura WHERE id_categoria = ?");
$stmt->execute([$id_categoria]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
