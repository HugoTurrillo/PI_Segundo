<?php
require "config/conexion.php";
header("Content-Type: application/json");

$sql = "SELECT id, nombre, premios, premio_fisico, es_base FROM categorias";
$res = $conexion->query($sql);

if (!$res) {
    echo json_encode([
        "ok" => false,
        "msg" => "Error SQL",
        "sql_error" => $conexion->error
    ]);
    exit;
}

$categorias = [];
while ($fila = $res->fetch_assoc()) {
    $categorias[] = $fila;
}

echo json_encode([
    "ok" => true,
    "data" => $categorias
]);
