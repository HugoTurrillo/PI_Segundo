<?php
require "config/conexion.php";
header("Content-Type: application/json");

// ============================
// RECIBIR DATOS
// ============================
$nombre = trim($_POST["nombre"] ?? "");
$url_web = trim($_POST["enlace"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

// ============================
// VALIDACIONES
// ============================
if ($nombre === "" || $url_web === "") {
    echo json_encode(["ok" => false, "msg" => "Nombre y enlace son obligatorios"]);
    exit();
}

if (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
    echo json_encode(["ok" => false, "msg" => "Debes subir un logo válido"]);
    exit();
}

// ============================
// PROCESAR ARCHIVO
// ============================
$logo = $_FILES["logo"];

// Normalizar nombre
$nombreLimpio = preg_replace("/[^a-zA-Z0-9_\.-]/", "", basename($logo["name"]));
$nombreArchivo = time() . "_" . $nombreLimpio;

$rutaDestino = "../uploads/" . $nombreArchivo;

// Crear carpeta si no existe
if (!is_dir("../uploads")) {
    mkdir("../uploads", 0777, true);
}

if (!move_uploaded_file($logo["tmp_name"], $rutaDestino)) {
    echo json_encode(["ok" => false, "msg" => "Error al guardar el archivo"]);
    exit();
}

// ============================
// INSERTAR EN BD
// ============================
$stmt = $conexion->prepare("
    INSERT INTO patrocinador (nombre, logo_ruta, url_web, descripcion)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("ssss", $nombre, $nombreArchivo, $url_web, $descripcion);

if (!$stmt->execute()) {
    echo json_encode(["ok" => false, "msg" => "Error al insertar en la base de datos"]);
    exit();
}

$stmt->close();

// ============================
// RESPUESTA FINAL
// ============================
echo json_encode(["ok" => true, "msg" => "Patrocinador creado correctamente"]);
?>
