<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

if (!$datos) {
    echo json_encode(["ok" => false, "mensaje" => "Datos no válidos"]);
    exit;
}

$id = intval($datos["id"] ?? 0);
$titulo = trim($datos["titulo"] ?? "");
$fecha = trim($datos["fecha"] ?? "");
$hora = trim($datos["hora"] ?? "");
$descripcion = trim($datos["descripcion"] ?? "");

if ($id <= 0 || $titulo === "" || $fecha === "" || $hora === "" || $descripcion === "") {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

$stmt = $conexion->prepare(
    "UPDATE evento 
     SET titulo = ?, fecha = ?, hora = ?, descripcion = ?
     WHERE id = ?"
);
$stmt->bind_param("ssssi", $titulo, $fecha, $hora, $descripcion, $id);
$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true]);
exit;
