<?php
include("conexion.php");
header("Content-Type: application/json");

// Recibir datos
$nombre = trim($_POST["nombre"] ?? "");
$enlace = trim($_POST["enlace"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

// Validaciones
if ($nombre === "" || $enlace === "") {
    echo json_encode(["ok" => false, "msg" => "Nombre y enlace son obligatorios"]);
    exit();
}

if (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
    echo json_encode(["ok" => false, "msg" => "Debes subir un logo válido"]);
    exit();
}

// Procesar archivo
$logo = $_FILES["logo"];
$nombreArchivo = time() . "_" . basename($logo["name"]);
$rutaDestino = "../uploads/" . $nombreArchivo;

// Crear carpeta si no existe
if (!is_dir("../uploads")) {
    mkdir("../uploads", 0777, true);
}

if (!move_uploaded_file($logo["tmp_name"], $rutaDestino)) {
    echo json_encode(["ok" => false, "msg" => "Error al guardar el archivo"]);
    exit();
}

// Insertar en BD
$stmt = $pdo->prepare("INSERT INTO patrocinadores (nombre, logo, enlace, descripcion) VALUES (?, ?, ?, ?)");
$stmt->execute([$nombre, $nombreArchivo, $enlace, $descripcion]);

echo json_encode(["ok" => true, "msg" => "Patrocinador creado correctamente"]);
?>
