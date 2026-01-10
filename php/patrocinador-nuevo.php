<?php
include("conexion.php");
header("Content-Type: application/json");

$nombre = trim($_POST["nombre"] ?? "");
$enlace = trim($_POST["enlace"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($nombre === "" || $enlace === "") {
    echo json_encode(["ok" => false, "msg" => "Nombre y enlace son obligatorios"]);
    exit();
}

if (!isset($_FILES["logo"])) {
    echo json_encode(["ok" => false, "msg" => "Debes subir un logo"]);
    exit();
}

$logo = $_FILES["logo"];
$nombreArchivo = time() . "_" . basename($logo["name"]);
$rutaDestino = "../uploads/" . $nombreArchivo;

move_uploaded_file($logo["tmp_name"], $rutaDestino);

$stmt = $pdo->prepare("INSERT INTO patrocinadores (nombre, logo, enlace, descripcion) VALUES (?, ?, ?, ?)");
$stmt->execute([$nombre, $nombreArchivo, $enlace, $descripcion]);

echo json_encode(["ok" => true, "msg" => "Patrocinador creado"]);
?>