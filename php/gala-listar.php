<?php
// php/gala-listar.php
require "config/conexion.php";

header("Content-Type: application/json; charset=utf-8");

$stmt = $conexion->prepare(
    "SELECT id, titulo, fecha, hora, lugar, descripcion, imagen
     FROM gala
     ORDER BY id DESC"
);

$stmt->execute();
$resultado = $stmt->get_result();

$gala = [];
while ($fila = $resultado->fetch_assoc()) {
    $gala[] = $fila;
}

echo json_encode($gala);
