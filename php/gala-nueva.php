<?php
require "config/conexion.php";
header("Content-Type: application/json");

$titulo = $_POST["titulo"] ?? "";
$fecha = $_POST["fecha"] ?? "";
$hora = $_POST["hora"] ?? "";
$lugar = $_POST["lugar"] ?? "";
$descripcion = $_POST["descripcion"] ?? "";

if (!$titulo || !$fecha || !$hora || !$lugar) {
    echo json_encode(["ok" => false, "msg" => "Faltan datos obligatorios"]);
    exit;
}

// Comprobar si ya existe una gala
$check = $conexion->query("SELECT id FROM gala LIMIT 1");
if ($check->num_rows > 0) {
    echo json_encode(["ok" => false, "msg" => "Ya existe una gala"]);
    exit;
}

// Subir imagen
$imagen = null;

if (!empty($_FILES["imagen"]["name"])) {
    $nombreArchivo = time() . "_" . basename($_FILES["imagen"]["name"]);
    $rutaDestino = "../uploads/" . $nombreArchivo;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
        $imagen = $nombreArchivo;
    }
}

$stmt = $conexion->prepare("INSERT INTO gala (titulo, fecha, hora, lugar, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $titulo, $fecha, $hora, $lugar, $descripcion, $imagen);

echo json_encode([
    "ok" => $stmt->execute(),
    "msg" => $stmt->execute() ? "Gala creada" : "Error al crear gala"
]);
