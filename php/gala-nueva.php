<?php
include("conexion.php");
header("Content-Type: application/json");

$titulo = trim($_POST["titulo"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$hora = trim($_POST["hora"] ?? "");
$lugar = trim($_POST["lugar"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

// Validación de campos obligatorios
if ($titulo === "" || $fecha === "" || $hora === "" || $lugar === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos obligatorios deben completarse"]);
    exit();
}

// VALIDACIÓN DE FECHA: no permitir fechas anteriores a hoy
$hoy = date("Y-m-d");

if ($fecha < $hoy) {
    echo json_encode(["ok" => false, "msg" => "La fecha no puede ser anterior a hoy"]);
    exit();
}

// Validación de imagen
if (!isset($_FILES["imagen"])) {
    echo json_encode(["ok" => false, "msg" => "Debes subir una imagen"]);
    exit();
}

$img = $_FILES["imagen"];
$nombreArchivo = time() . "_" . basename($img["name"]);
$rutaDestino = "../uploads/" . $nombreArchivo;

move_uploaded_file($img["tmp_name"], $rutaDestino);

// Insertar en BD
$stmt = $pdo->prepare("INSERT INTO gala (titulo, fecha, hora, lugar, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$titulo, $fecha, $hora, $lugar, $descripcion, $nombreArchivo]);

echo json_encode(["ok" => true, "msg" => "Evento creado"]);
?>