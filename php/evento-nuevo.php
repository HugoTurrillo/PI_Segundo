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

$titulo = trim($datos["titulo"] ?? "");
$fecha = trim($datos["fecha"] ?? "");
$hora = trim($datos["hora"] ?? "");
$descripcion = trim($datos["descripcion"] ?? "");

if ($titulo === "" || $fecha === "" || $hora === "" || $descripcion === "") {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

/*  COMPROBAR EVENTO DUPLICADO */
$stmt = $conexion->prepare(
    "SELECT id FROM evento WHERE fecha = ? AND hora = ?"
);
$stmt->bind_param("ss", $fecha, $hora);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        "ok" => false,
        "confirmar" => true,
        "mensaje" => "Ya existe un evento ese día y a esa hora. ¿Quieres crearlo igualmente?"
    ]);
    exit;
}
$stmt->close();

/* INSERTAR */
$stmt = $conexion->prepare(
    "INSERT INTO evento (titulo, fecha, hora, descripcion)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $titulo, $fecha, $hora, $descripcion);
$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true]);
exit;
