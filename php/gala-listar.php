<?php
// php/gala-listar.php
require "config/conexion.php";

header("Content-Type: application/json; charset=utf-8");

// Obtener la única gala existente
$sql = "SELECT id, titulo, fecha, lugar, descripcion FROM gala LIMIT 1";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $gala = $resultado->fetch_assoc();
    echo json_encode([
        "ok" => true,
        "data" => $gala
    ]);
} else {
    echo json_encode([
        "ok" => false,
        "msg" => "No existe ninguna gala"
    ]);
}
