<?php
// php/gala-nueva.html
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$titulo = trim($_POST["titulo"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$hora = trim($_POST["hora"] ?? "");
$lugar = trim($_POST["lugar"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($titulo === "" || $fecha === "" || $hora === "" || $lugar === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos obligatorios deben completarse"]);
    exit;
}

$hoy = date("Y-m-d");
if ($fecha < $hoy) {
    echo json_encode(["ok" => false, "msg" => "La fecha no puede ser anterior a hoy"]);
    exit;
}

if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["size"] <= 0) {
    echo json_encode(["ok" => false, "msg" => "Debes subir una imagen"]);
    exit;
}

$img = $_FILES["imagen"];
$nombreArchivo = time() . "_" . basename($img["name"]);
$rutaDestino = "../uploads/" . $nombreArchivo;

if (!move_uploaded_file($img["tmp_name"], $rutaDestino)) {
    echo json_encode(["ok" => false, "msg" => "Error al subir la imagen"]);
    exit;
}

$stmt = $conexion->prepare(
    "INSERT INTO gala (titulo, fecha, hora, lugar, descripcion, imagen)
     VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssssss",
    $titulo,
    $fecha,
    $hora,
    $lugar,
    $descripcion,
    $nombreArchivo
);

$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Evento creado"]);
