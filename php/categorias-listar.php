<?php
// php/categorias-listar.php
require "config/conexion.php";

header("Content-Type: application/json; charset=utf-8");

$stmt = $conexion->prepare(
    "SELECT id, nombre, premios, premio_fisico
     FROM categorias
     ORDER BY id DESC"
);
$stmt->execute();

$res = $stmt->get_result();

echo json_encode([
    "ok" => true,
    "data" => $res->fetch_all(MYSQLI_ASSOC)
]);
