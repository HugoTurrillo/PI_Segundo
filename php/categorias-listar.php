<?php
require "conexion.php";

header("Content-Type: application/json; charset=utf-8");

$sql = "SELECT id, nombre, premios, premio_fisico FROM categorias ORDER BY id DESC";
$stmt = $pdo->query($sql);

$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "ok" => true,
    "data" => $categorias
]);
