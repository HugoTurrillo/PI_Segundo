<?php
require "config/conexion.php";

header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ============================
// RECIBIR DATOS
// ============================
$nombre      = trim($_POST["nombre"] ?? "");
$url_web     = trim($_POST["enlace"] ?? "");
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

$nombreLimpio  = preg_replace("/[^a-zA-Z0-9_\.-]/", "", basename($logo["name"]));
$nombreArchivo = time() . "_" . $nombreLimpio;
$rutaDestino   = "uploads/" . $nombreArchivo;

if (!is_dir("uploads")) {
    mkdir("uploads", 0777, true);
}

if (!move_uploaded_file($logo["tmp_name"], $rutaDestino)) {
    echo json_encode(["ok" => false, "msg" => "Error al guardar el archivo en el servidor"]);
    exit();
}

// ============================
// INSERTAR EN BD
// ============================
// IMPORTANTE: la tabla debe ser EXACTAMENTE:
//
// CREATE TABLE patrocinador (
//   id_patrocinador INT AUTO_INCREMENT PRIMARY KEY,
//   nombre VARCHAR(100) NOT NULL,
//   logo_ruta VARCHAR(255) NOT NULL,
//   url_web VARCHAR(255) NOT NULL,
//   descripcion TEXT
// );

$sql = "
    INSERT INTO patrocinador (nombre, logo_ruta, url_web, descripcion)
    VALUES (?, ?, ?, ?)
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "ok"  => false,
        "msg" => "Error en prepare(): " . $conexion->error
    ]);
    exit();
}

$stmt->bind_param("ssss", $nombre, $nombreArchivo, $url_web, $descripcion);

if (!$stmt->execute()) {
    echo json_encode([
        "ok"  => false,
        "msg" => "Error al insertar: " . $stmt->error
    ]);
    $stmt->close();
    exit();
}

$stmt->close();

// ============================
// RESPUESTA FINAL
// ============================
echo json_encode(["ok" => true, "msg" => "Patrocinador creado correctamente"]);
